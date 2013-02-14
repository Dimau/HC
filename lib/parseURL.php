<?php
/**
 * Автоматический парсинг сайта с объявлениями
 * Сценарий должен запускаться автоматически каждые несколько минут с помощью cron
 * Задача - выявить новые объявления появившиеся на сайте (еще не сохранявшиеся в мою базу)
 * Выявленные новые объявления сохраняются в базу и отправляется e-mail оператору, которое предлагает пройти по ссылке отредактировать данные и опубликовать объявление на моем портале
 *
 * Общий алгоритм работы:
 *
 * Обработка списка объявлений
 * 1. Один раз в несколько минут парсер загружает самый последний список объявлений долгосрочной аренды жилья в Екатеринбурге с указанного сайта
 * 2. Последовательно проходим список сверху вниз. Если достигли конца списка, загружаем следующую страницу со списком объявлений
 *
 * Обработка краткого описания объявления в списке
 * 3. Проверяем каждое объявление - было ли оно обработано ранее.
 * 4. Если объявление было обработано ранее (его идентификатор сохранен в БД) - переходим к следующему пункту списка объявлений.
 * 5. Проверяем дату публикации объявления, если она слишком далека от текущей - заканчиваем парсинг (считаем его успешно завершенным).
 *      6. Проверяем идентификатор объявления на совпадение с одним из трех идентификаторов объявлений, которые были уже обработаны первыми в предыдущий успешный парсинг
 *      7. Если идентификатор текущего объявления совпал с одним из этих трех, то заканчиваем парсинг, считаем его успешно завершенным. Если идентификатор обрабатываемого объявления совпадает с одним из 3-х id, запомненных в качестве начальных для предыдущего успешно законченного парсинга, то прекращаем парсинг - с большой долей вероятности мы достигли момента, после кторого все объявления уже были обработаны ранее. Исключением может быть лишь ситуация, при которой с момента предыдущего парсинга и до начала текущего одно из этих 3-х объявлений было поднято в выдаче собственником (но такая ситуация крайне маловероятна, если парсинг запускается часто - 1 раз в 5-10 мин., да и не страшна, так как не будут обработаны объявления только за этот короткий промежуток времени между парсингами).
 * 8. Если объявление не подошло ни под одно из предыдущих условий, значит это новое ранее необработанное объявление
 * 9. Получаем подробные сведения по нему.
 *      10. При успешном завершении парсинга сохраняем в БД идентификаторы первого, второго и третьего объявлений с которых мы начали эту успешную обработку. Это значит, что все объявления, начиная с запомненного id и до указанного в настройках парсера ограничения на количество дней, являются успешно обработанными. Как Вы заметили, после каждого успешного парсинга нужно сохранять именно 3 подряд идущих id объявлений, с которых он начался, а не один id. Это позволит избежать ситуации, при которой объявление с сохраненным id будет удалено и парсеру придется отработать множество объявлений за все те дни, которые указаны в настройках парсера. Маловероятно, чтобы все 3 объявления были удалены за такое короткое время, как интервал между парсингами (5 - 10 мин.)
 *
 * Обработка подробного описания объявления
 * 11. Прорабатываем телефон объявления
 *   11.0. Получаем телефон контактного лица по объявлению, нормализуем его, если нужно распознаем с картинки
 *   11.1. Проверяем телефон по нашей базе
 *   11.2. Если телефон не известен, ищем признаки агента
 *   11.3. Если признаки агента в объявлении найдены, добавляем телефон агента в базу и пропускаем это объявление
 *   11.4. Если признаки агента не найдены, добавляем телефон собственника в базу и продолжаем обработку данного объявления
 *   11.5. Если телефон принадлежит агенту и использовался не позже, чем год наза, то игнорируем объявление
 *   11.6. Если телефон принадлежит агенту и использовался последний раз ранее, чем год назад, то отрабатываем с ним, как с объявлением, по которому телефон не известен базе (то есть снова ищем признаки агента)
 *      11.7. Если телефон принадлежит собственнику (или арендатору) то в будущем нужно избегать дубликатов
 * 12. Парсим структурированные данные
 * 13. Проверяем наличие фотографий
 * 14. Парсим комментарий
 *      15. Получаем координаты примерные на яндекс карте, название адреса оставляем такое же как указал собственник. Если адрес не удалось найти или район левый указан, то не публикуем - отправляем обьявление оператору
 * 16. Сохраняем обьявление в базу и, в идеале, сразу публикуем его
 *
 */

// Подключаем необходимые модели, классы
if (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] != "") $websiteRoot = $_SERVER['DOCUMENT_ROOT']; else $websiteRoot = "/var/www/dimau/data/www/svobodno.org";
require_once $websiteRoot . '/lib/simple_html_dom.php';
require_once $websiteRoot . '/lib/class.phpmailer.php';
require_once $websiteRoot . '/models/DBconnect.php';
require_once $websiteRoot . '/models/GlobFunc.php';
require_once $websiteRoot . '/models/Logger.php';
require_once $websiteRoot . '/models/ParserBasic.php';
require_once $websiteRoot . '/models/Property.php';

// Получаем параметр для идентификаци режима работы (а точнее ресурса, который нужно пропарсить сейчас) - передается как параметр команды для cron
if (isset($argv[1]) && ($argv[1] == "e1" || $argv[1] == "bazab2b")) {
    $mode = $argv[1];
} else {
    Logger::getLogger(GlobFunc::$loggerName)->log("parseURL.php:1 Обращение к parseURL.php без указания mode");
    exit();
}

// Фиксируем в логах факт запуска парсинга
Logger::getLogger(GlobFunc::$loggerName)->log("parseURL.php:2 Запуск процесса парсинга в режиме '".$mode."'");

// Удалось ли подключиться к БД?
if (DBconnect::get() == FALSE) {
    Logger::getLogger(GlobFunc::$loggerName)->log("parseURL.php:3 Ошибка инициализации соединения с БД");
    exit();
}

// Подключим соответствующую модель и инициализируем для нее объект парсера, который собственно и содержит все необходимые сведения и методы для парсинга сайта
switch ($mode) {
    case "e1":
        require_once $websiteRoot . '/models/ParserE1.php';
        $parser = new ParserE1();
        break;
    case "bazab2b":
        require_once $websiteRoot . '/models/ParserBazaB2B.php';
        $parser = new ParserBazaB2B();
        break;
}

/********************************************************************************
 * Парсим сайт
 *******************************************************************************/

//TODO: test
/*function curl($URLServer, $postdata = "", $cookieFile = null, $proxy = true, $proxyRetry = 0) {
    global $proxyCache;
    //sleep(20);
    $agent = "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.12) Gecko/20101027 Ubuntu/10.10 (maverick) Firefox/3.6.12";
    $cURL_Session = curl_init();

    curl_setopt($cURL_Session, CURLOPT_URL, $URLServer);
    curl_setopt($cURL_Session, CURLOPT_USERAGENT, $agent);
    if ($postdata != "") {
        curl_setopt($cURL_Session, CURLOPT_POST, 1);
        curl_setopt($cURL_Session, CURLOPT_POSTFIELDS, $postdata);
    }
    curl_setopt($cURL_Session, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($cURL_Session, CURLOPT_FOLLOWLOCATION, 1);
    if ($cookieFile != null) {
        curl_setopt($cURL_Session, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($cURL_Session, CURLOPT_COOKIEFILE, $cookieFile);
    }

    if ($proxy == true) {
        if ($proxyCache == "") {
            $c = curl("http://www.proxylist.net/", "", null, false);
            preg_match_all("/([0-9]*).([0-9]*).([0-9]*).([0-9]*):([0-9]*)/", $c, $matches);
            $matches = $matches [0];
            $proxyCache = $matches [rand(0, (count($matches) - 1))];
        }

        echo    "proxy:$proxyCache<br>";

        list ($proxy_ip, $proxy_port) = explode(":", $proxyCache);
        curl_setopt($cURL_Session, CURLOPT_PROXYPORT, $proxy_port);
        curl_setopt($cURL_Session, CURLOPT_PROXYTYPE, 'HTTP');
        curl_setopt($cURL_Session, CURLOPT_PROXY, $proxy_ip);
    }

    $result = curl_exec($cURL_Session);

    if ($result === false) {
        echo    'Curl error: ' . curl_error($cURL_Session) . "<br>";

        if ($proxy == true && $proxyRetry <= 5) curl($URLServer, $postdata = "", $cookieFile, $proxy, $proxyRetry++);
    }
    curl_close($cURL_Session);

    return $result;
} */

// Для некоторых ресурсов необходима авторизация на сайте
if ($mode == "bazab2b") $parser->authorization();

//TODO: test
$jk = 0;

// В цикле закачиваем страницы со списками объявлений и обрабатываем каждую из них (начиная с самой актуальной - первой).
// Вплоть до момента, пока не доберемся до объявления со временем публикации позже, чем наша граница актуальности (задается в классе ParserBazaB2B в переменной actualDayAmountForAdvert)
while ($parser->loadNextAdvertsList()) {

    // Перебираем последовательно все объявления с текущей страницы, содержащей список объявлений (начиная с самого актуального объявления по дате публикации).
    // Формируем соответствующие объявления в нашей базе (если ранее они не были сформированы).
    while ($parser->getNextAdvertShortDescription()) {

        //TODO: test
        if ($jk >= 5) {
            DBconnect::closeConnectToDB();
            exit();
        }
        $jk++;

        //TODO: test
        Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: работаем с объявлением номер X");

        // Проверить, работали ли мы с этим объявлением уже. Если да, то сразу переходим к следующему
        if ($parser->isAdvertAlreadyHandled()) {
            //TODO: test
            Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: объявление ранее обработано");
            continue;
        }

        //TODO: test
        Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: объявление еще не обработано");

        // Если мы достигли конца временного диапазона актуальности объявлений, то необходимо остановить обработку страницы на этом объявлении
        if ($parser->isStopHandling()) {
            //TODO: test
            Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: дата публикации объявления слишком поздняя");
            DBconnect::closeConnectToDB();
            exit();
        }

        //TODO: test
        Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: подходящая дата публикации - работаем далее");

        // Загрузим подробные сведения по этому объявлению
        if (!$parser->loadFullAdvertDescription()) {
            //TODO: test
            Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: не удалось загрузить страницу с полное описание объявления");
            continue;
        }

        // Работа с телефоном контактного лица по объявлению
        if ($mode != "bazab2b") {

            // Получим телефон контактного лица по объявлению
            if (!$parser->getPhoneNumber()) {
                //TODO: test
                Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: не удалось получить телефонный номер из объявления");

                // Оповещаем операторов о новом объявлении, у которого не удалось определить номер телефона
                $subject = 'Объявление на ' . $mode . ' без телефонного номера';
                $msgHTML = "Новое объявление на " . $mode . " без телефонного номера: http://www.e1.ru/business/realty/" . $parser->getId();
                GlobFunc::sendEmailToOperator($subject, $msgHTML);
                continue;
            }

            //TODO: test
            Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: телефонный номер получен");

            // Проверяем телефонный номер по БД
            $dataAboutPhoneNumber = $parser->getDataAboutPhoneNumber();

            //TODO: test
            Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: проверили телефонный номер по БД - ".json_encode($dataAboutPhoneNumber));

            // В зависимости от статуса контактного лица по этому номеру выполняем определенные действия
            if (count($dataAboutPhoneNumber) == 0 || ($dataAboutPhoneNumber['status'] == "агент" && $dataAboutPhoneNumber['dateOfLastPublication'] < time() - 31449600)) {

                //TODO: test
                Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: номер телефона еще не известен БД");

                // Если номер телефона еще не известен БД. А также, если это номер телефона агента, который использовался последний раз больше, чем год назад
                // Проверяем признаки объявления от агента
                if ($parser->hasSignsAgent()) {

                    //TODO: test
                    Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: обнаружены признаки агента в объявлении");

                    // Если в объявлении есть признаки агентства, то добавляем телефон в БД как агента и переходим к следующему объявлению
                    $parser->newKnownPhoneNumber("агент");
                    continue;

                } else {

                    //TODO: test
                    Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: признаки агента не обнаружены в объявлении");

                    // Если в объявлении нет признаков агентства, то добавляем телефон в БД как телефон собственника
                    $parser->newKnownPhoneNumber("собственник");
                }

            } elseif ($dataAboutPhoneNumber['status'] == "собственник" || $dataAboutPhoneNumber['status'] == "арендатор") {

                //TODO: test
                Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: телефонный номер принадлежит собственнику");

                // Если это номер собственника - боремся с дубликатами
                // TODO: есть ли опубликованные объявления с таким номером телефона.
                // TODO: если есть с полнотой 1 - игнорируем это объявление
                // TODO: если есть с полнотой 0 - дополняем исходное объявление

            } elseif ($dataAboutPhoneNumber['status'] == "агент" && $dataAboutPhoneNumber['dateOfLastPublication'] >= time() - 31449600) {

                //TODO: test
                Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: телефонный номер принадлежит агенту");

                // Если это телефонный номер агента и он использовался последний раз менее года назад (31449600 секунд) - игнорируем объявление. Проверка на срок год нужна, так как агент мог отказаться от данного номера, и номер мог быть переназначен хорошему человеку
                // Актуализируем дату последнего использования телефонного номера
                $parser->updateDateKnownPhoneNumber();
                continue;
            }

        }

        // Преобразуем подробные сведения по объявлению к виду ассоциативного массива
        if (!($paramsArr = $parser->parseFullAdvert())) {
            //TODO: test
            Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: не удалось распарсить подробное объявление");

            if ($mode == "bazab2b") $parser->authorization();
            continue;
        }

        //TODO: test
        Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: удалось распарсить полное объявление");

        // Проверим, что объявление относится к Екатеринбургу. Если нет, считаем его успешно обработанным и не добавляем в базу
        if (!isset($paramsArr['city']) || $paramsArr['city'] != "Екатеринбург") {
            //TODO: test
            Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: объявление не из Екатеринбурга");

            $parser->setAdvertIsHandled();
            continue;
        }

        //TODO: test
        Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: объявление относится к Екатеринбургу");

        // Инициализируем модель и сохраняем данные в БД
        $property = new Property($paramsArr);
        $property->setCompleteness("0");
        $property->setStatus("не опубликовано");
        $property->setOwnerLogin("owner"); // Используем в качестве логина собственника логин служебного собственника owner, на которого сохраняются все чужие объявления
        $correctSaveCharacteristicToDB = $property->saveCharacteristicToDB("new");

        // Добавим объявление в список успешно обработанных, чтобы избежать в будущем его повторной обработки
        if (!$parser->setAdvertIsHandled()) {
            //TODO: test
            Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: не удалось отметить объявление как обработанное");

            DBconnect::closeConnectToDB();
            exit();
        }

        //TODO: test
        Logger::getLogger(GlobFunc::$loggerName)->log("Тестирование парсера e1: отметили объявление как обработанное");

        // Оповещаем операторов о новом объявлении на сайте bazab2b.ru
        $subject = 'Объявление на ' . $mode;
        $msgHTML = "Новое объявление на " . $mode . ": <a href='http://svobodno.org/editadvert.php?propertyId=" . $property->getId() . "'>" . $property->getAddress() . "</a>";
        GlobFunc::sendEmailToOperator($subject, $msgHTML);
    }

    // Когда мы переберем все объявления на текущей странице списка объявлений, то следующим шагом автоматически загрузится следующая страница - и все по новой
}

/********************************************************************************
 * Закрываем соединение с БД
 *******************************************************************************/

DBconnect::closeConnectToDB();