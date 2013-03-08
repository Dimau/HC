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
 * 3. Перед загрузкой каждой новой страницы со списком проверяем сколько страниц со списком объявлений мы уже распарсили. Если достигли лимита ($parser->maxAdvertsListForHandling) - заканчиваем парсинг и не считаем его успешно завершенным, так как это экстренная защитная мера от ошибок в коде, а не штатное завершение алгоритма.
 *
 * Обработка краткого описания объявления в списке
 * 4. Если мы работаем с одним из трех первых за эту сессию парсинга объявлением, то запоминаем его идентификатор (чтобы в следующую сессию парсинга, достигнув его, закончить парсинг)
 * 5. Если мы уже обработали обязательное количество страниц со списком объявлений, то проверяем идентификатор данного объявления на соответствие одному из трех id объявлений, с которых была начата предыдущая успешно оконченная сессия парсинга. Если есть совпадение, прекращаем парсинг и запоминаем новые 3 id объявлений, с которых он был начат, чтобы в следующию сессию парсинга дойти только до них и не дальше. C большой долей вероятности мы достигли объявления, после кторого все остальные объявления уже были обработаны ранее. Исключением может быть ситуация, при которой с момента предыдущего парсинга и до начала текущего одно из этих 3-х объявлений было поднято в выдаче собственником (но такая ситуация крайне маловероятна, если парсинг запускается часто - 1 раз в 5-10 мин., да и не страшна, так как не будут обработаны объявления только за этот короткий промежуток времени между парсингами).
 * 6. Проверяем каждое объявление - было ли оно обработано ранее.
 * 7. Если объявление было обработано ранее (его идентификатор сохранен в БД) - переходим к следующему пункту списка объявлений.
 * 8. Проверяем дату публикации объявления, если она слишком далека от текущей (больше чем на $parser->actualDayAmountForAdvert дней) - заканчиваем парсинг (считаем его успешно завершенным).
 * 9. Если объявление не подошло ни под одно из предыдущих условий, значит это новое ранее необработанное объявление
 * 10. Получаем подробные сведения по нему.
 * 11. При успешном завершении парсинга сохраняем в БД идентификаторы первого, второго и третьего объявлений с которых мы начали эту успешную обработку. Это значит, что все объявления, начиная с запомненного id и до указанного в настройках парсера ограничения на количество дней, являются успешно обработанными. Как Вы заметили, после каждого успешного парсинга нужно сохранять именно 3 подряд идущих id объявлений, с которых он начался, а не один id. Это позволит избежать ситуации, при которой объявление с сохраненным id будет удалено и парсеру придется отработать множество объявлений за все те дни, которые указаны в настройках парсера. Маловероятно, чтобы все 3 объявления были удалены за такое короткое время, как интервал между парсингами (5 - 10 мин.). Парсинг считается успешно оконченным если выполняется одно из 3-х условий: 1. достигли объявления с датой публикации старше, чем $actualDayAmountForAdvert 2. достигли предела по количеству загруженных за 1 сессию страниц со списком объявлений и у нас есть как минимум 3 объявления, чьи идентификаторы мы сможем сохранить 3. достигли объявления, чей идентификатор совпадает с одним из 3-х, обработанных первыми в прошлую успешно закончившеюся сессию парсинга в данном режиме.
 *
 * Обработка подробного описания объявления
 * 12. Прорабатываем телефон объявления
 *   12.0. Получаем телефон контактного лица по объявлению, нормализуем его, если нужно распознаем с картинки
 *   12.1. Проверяем телефон по нашей базе
 *   12.2. Если телефон не известен, ищем признаки агента
 *   12.3. Если признаки агента в объявлении найдены, добавляем телефон агента в базу и пропускаем это объявление
 *   12.4. Если признаки агента не найдены, добавляем телефон собственника в базу и продолжаем обработку данного объявления
 *   12.5. Если телефон принадлежит агенту и использовался не позже, чем год наза, то игнорируем объявление
 *   12.6. Если телефон принадлежит агенту и использовался последний раз ранее, чем год назад, то отрабатываем с ним, как с объявлением, по которому телефон не известен базе (то есть снова ищем признаки агента)
 *                  12.7. Если телефон принадлежит собственнику (или арендатору) то в будущем нужно избегать дубликатов
 * 13. Парсим структурированные данные
 * 14. Проверяем наличие фотографий
 * 15. Парсим комментарий
 *                  16. Получаем координаты примерные на яндекс карте, название адреса оставляем такое же как указал собственник. Если адрес не удалось найти или район левый указан, то не публикуем - отправляем обьявление оператору
 * 17. Сохраняем обьявление в базу и, в идеале, сразу публикуем его
 *
 */

/********************************************************************************
 * ИНИЦИАЛИЗАЦИЯ ПАРСИНГА
 *******************************************************************************/

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
if (isset($argv[1]) && ($argv[1] == "e1Kv1k" || $argv[1] == "e1Kv2k" || $argv[1] == "e1Kv3k" || $argv[1] == "e1Kv4k" || $argv[1] == "e1Kv5k" || $argv[1] == "e1Kom" || $argv[1] == "66ruKv" || $argv[1] == "66ruKom" || $argv[1] == "avitoKvEkat" || $argv[1] == "avitoKomEkat")) {
    $mode = $argv[1];
} else {
    Logger::getLogger(GlobFunc::$loggerName)->log("parseURL.php:1 Обращение к parseURL.php без указания mode");
    exit();
}

// Фиксируем в логах факт запуска парсинга
Logger::getLogger(GlobFunc::$loggerName)->log("parseURL.php:2 Запуск процесса парсинга в режиме '" . $mode . "'");

// Удалось ли подключиться к БД?
if (DBconnect::get() == FALSE) {
    Logger::getLogger(GlobFunc::$loggerName)->log("parseURL.php:3 Ошибка инициализации соединения с БД");
    exit();
}

// Подключим соответствующую модель и инициализируем для нее объект парсера, который собственно и содержит все необходимые сведения и методы для парсинга сайта
switch ($mode) {
    case "e1Kv1k":
    case "e1Kv2k":
    case "e1Kv3k":
    case "e1Kv4k":
    case "e1Kv5k":
    case "e1Kom":
        require_once $websiteRoot . '/models/ParserE1.php';
        $parser = new ParserE1($mode);
        break;
    case "66ruKv":
    case "66ruKom":
        require_once $websiteRoot . '/models/Parser66ru.php';
        $parser = new Parser66ru($mode);
        break;
    case "avitoKvEkat":
    case "avitoKomEkat":
        require_once $websiteRoot . '/models/ParserAvito.php';
        $parser = new ParserAvito($mode);
        break;
}

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

/*************************************************************************************
 * АВТОРИЗАЦИЯ НА РЕСУРСЕ ИСТОЧНИКЕ
 ************************************************************************************/

// Для базыБ2Б 2 раза подряд пытаемся авторизоваться, если не получается - заканчиваем работу - дальнейшие действия не имеют смысла (адрес и телефон по объявлению будут недоступны)
/*if ($mode == "bazab2b") {
    if (!$parser->authorization()) {
        if (!$parser->authorization()) {
            DBconnect::closeConnectToDB();
            exit();
        }
    }
}*/

/*************************************************************************************
 * ПОЛУЧАЕМ СПИСОК СВЕЖИХ ОБЪЯВЛЕНИЙ
 * В цикле закачиваем страницы со списками объявлений и обрабатываем каждую из них (начиная с самой актуальной - первой).
 ************************************************************************************/

while ($parser->loadNextAdvertsList()) {

    /*************************************************************************************
     * ОБРАБАТЫВАЕМ КАЖДОЕ КОНКРЕТНОЕ ОБЪЯВЛЕНИЕ ИЗ СПИСКА
     * Перебираем последовательно все объявления с текущей страницы, содержащей список объявлений (начиная с самого актуального объявления по дате публикации).
     * Формируем соответствующие объявления в нашей базе (если ранее они не были сформированы).
     ************************************************************************************/

    while ($parser->getNextAdvertShortDescription()) {

        /*************************************************************************************
         * ПРОВЕРКА УСЛОВИЙ ПРОДОЛЖЕНИЯ ПАРСИНГА
         ************************************************************************************/

        // Прежде всего, в начале работы с объявлением, проверим, является ли оно одним из трех первых в эту итерацию парсинга. Если да, то запоним его идентификатор
        $parser->checkAdvertForOneOfFirst();

        // Проверим, в прошлый успешно законченный парсинг в этом режиме мы уже обрабатывали данное объявление?
        if ($parser->isAdvertLastSuccessfulHandled()) {
            // Так как парсинг достиг объявления, за которым все остальные уже были успешно обработаны ранее - считаем парсинг успешно законченным
            // Сохраняем список из 3-х идентификаторов, с которых начался этот успешный парсинг
            $parser->saveNewLastSuccessfulHandledAdvertsId();
            DBconnect::closeConnectToDB();
            exit();
        }

        // Проверить, работали ли мы с этим объявлением уже. Если да, то сразу переходим к следующему
        if ($parser->isAdvertAlreadyHandled()) continue;

        // Если мы достигли конца временного диапазона актуальности объявлений, то необходимо остановить обработку страницы на этом объявлении
        if ($parser->isTooLateDate()) {
            // Сохраняем список из 3-х идентификаторов, с которых начался этот успешный парсинг
            $parser->saveNewLastSuccessfulHandledAdvertsId();
            DBconnect::closeConnectToDB();
            exit();
        }

        /*************************************************************************************
         * ПОЛУЧАЕМ ПОДРОБНЫЕ СВЕДЕНИЯ ПО ОБЪЯВЛЕНИЮ
         ************************************************************************************/

        if (!$parser->loadFullAdvertDescription()) continue;

        /*************************************************************************************
         * ОБРАБАТЫВАЕМ НОМЕР ТЕЛЕФОНА КОНТАКТНОГО ЛИЦА ПО ОБЪЯВЛЕНИЮ
         ************************************************************************************/

        // Получим телефон контактного лица по объявлению
        if (!$parser->getPhoneNumber()) {
            // Оповещаем операторов о новом объявлении, у которого не удалось определить номер телефона
            $subject = 'Объявление на ' . $mode . ' без телефонного номера';
            $msgHTML = "Новое объявление на " . $mode . " без телефонного номера: http://www.e1.ru/business/realty/" . $parser->getId();
            GlobFunc::sendEmailToOperator($subject, $msgHTML);
            continue;
        }

        // Проверяем телефонный номер по БД
        $dataAboutPhoneNumber = $parser->getDataAboutPhoneNumber();

        /*************************************************************************************
         * ВЫБИРАЕМ СЦЕНАРИЙ ДЕЙСТВИЙ В ЗАВИСИМОСТИ ОТ СТАТУСА ТЕЛЕФОННОГО НОМЕРА
         ************************************************************************************/

        // Классифицируем текущую ситуацию, чтобы выбрать дальнейший алгоритм действий
        $methodOfAction = "";
        if (count($dataAboutPhoneNumber) == 0) {
            // Номер еще не известен БД
            if ($parser->hasSignsAgent()) {
                // В объявлении обнаружены признаки агента
                $methodOfAction = "newAgent";
            } else {
                // В объявлении не обнаружены признаки агента
                $methodOfAction = "newNotDefined";
            }
        } elseif ($dataAboutPhoneNumber['status'] == "агент") {
            // Номер телефона принадлежал агенту
            if ($dataAboutPhoneNumber['dateOfLastPublication'] >= time() - 3456000) {
                // Телефон агента, который использовался последний раз менее 40 дней назад
                $methodOfAction = "agentInPresent";
            } else {
                // Телефон агента, который использовался им последний раз более 40 дней назад
                if ($parser->hasSignsAgent()) {
                    // В объявлении обнаружены признаки агента
                    $methodOfAction = "agentInPastAndPresent";
                } else {
                    // В объявлении не обнаружены признаки агента
                    $methodOfAction = "agentInPastAndNotDefinedInPresent";
                }
            }
        } elseif ($dataAboutPhoneNumber['status'] == "собственник" || $dataAboutPhoneNumber['status'] == "арендатор" || $dataAboutPhoneNumber['status'] == "не определен") {
            // Объявления с таким номером телефона уже встречались роботу (есть в БД), номер телефона не классифицирован как агентский
            // Получим объявления из текущей базы с таким же контактным номером телефона
            $propertiesId = DBconnect::selectPropertiesIdForContactTelephonNumber($dataAboutPhoneNumber['phoneNumber']);
            if (count($propertiesId) == 0) {
                // Сейчас в базе нет объявлений с таким же контактным номером телефона (возможно такие объявления есть в архиве)
                $methodOfAction = "ownerInPastNewAdvert";
            } else {
                // Сейчас в базе уже есть объявления с таким же контактным номером телефона
                $methodOfAction = "ownerInPastExistingAdvert";
            }
        }

        /*************************************************************************************
         * СЦЕНАРИИ ДЕЙСТВИЙ ДЛЯ РАЗЛИЧНЫХ СИТУАЦИЙ
         ************************************************************************************/

        // Номер еще не известен БД, в объявлении обнаружены признаки агента
        if ($methodOfAction == "newAgent") {
            // Если в объявлении есть признаки агентства, то добавляем телефон в БД как агента и переходим к следующему объявлению
            $parser->newKnownPhoneNumber("агент");
            $parser->setAdvertIsHandled();
            continue;
        }

        // Номер еще не известен БД, в объявлении НЕ обнаружены признаки агента
        if ($methodOfAction == "newNotDefined") {
            // Если в объявлении нет признаков агентства, то добавляем телефон в БД как телефон со статусом "не определен"
            $parser->newKnownPhoneNumber("не определен");
        }

        // Номер телефона принадлежал агенту и использовался последний раз менее 40 дней назад
        if ($methodOfAction == "agentInPresent") {
            // Если это телефонный номер агента и он использовался последний раз менее 40 дней назад (3456000 секунд) - игнорируем объявление. Проверка на срок 40 дней нужна, так как агент мог отказаться от данного номера, и номер мог быть переназначен хорошему человеку
            // Актуализируем дату последнего использования телефонного номера
            $parser->updateDateKnownPhoneNumber();
            $parser->setAdvertIsHandled();
            continue;
        }

        // Номер телефона принадлежал агенту, использовался им последний раз более 40 дней назад. В объявлении обнаружены признаки агента
        if ($methodOfAction == "agentInPastAndPresent") {
            // Если в объявлении есть признаки агентства, то добавляем телефон в БД как агента и переходим к следующему объявлению
            $parser->updateDateKnownPhoneNumber();
            $parser->setAdvertIsHandled();
            continue;
        }

        // Номер телефона принадлежал агенту, использовался им последний раз более 40 дней назад. В объявлении НЕ обнаружены признаки агента
        if ($methodOfAction == "agentInPastAndNotDefinedInPresent") {
            // Обновить статус (с агента на не определен) и дату последнего использования телефонного номера
            $parser->changeStatusKnownPhoneNumber("не определен");
            $parser->updateDateKnownPhoneNumber();
        }

        // Объявления с таким номером телефона уже встречались роботу (есть в БД), номер телефона не классифицирован как агентский.
        // В базе нет объявлений с таким же контактным номером телефона (возможно такие объявления есть в архиве)
        if ($methodOfAction == "ownerInPastNewAdvert") {
            // Запишем обнаруженный дублирующий телефонный номер в специальную таблицу. Так как письма зачастую на e-mail не доходят, то оператору нужно работать с этой таблицей
            DBconnect::insertDuplicatePhoneNumber($dataAboutPhoneNumber['phoneNumber']);
            // Обновляем дату последнего использования телефонного номера
            $parser->updateDateKnownPhoneNumber();
        }

        // Объявления с таким номером телефона уже встречались роботу (есть в БД), номер телефона не классифицирован как агентский.
        // В базе уже есть объявления с таким же контактным номером телефона
        if ($methodOfAction == "ownerInPastExistingAdvert") {

            // TODO: такое может случиться по одной из следующих причин:
            // 1. Это объявление-дубликат от собственника
            // 2. Собственник сдает недвижимость заново
            // 3. У собственника есть другая квартира, которую он сейчас тоже стал сдавать
            // 4. Номер уже не принадлежит собственнику (или никогда ему не принадлежал) - это агент или другой собственник
            // 5. Какой-то агент злонамеренно публикует объявление с телефоном этого собственника
            // Если статус не определен, то скорее всего это просто агент, а не собственник
            // Для классификации ситуации нужно использовать следующие параметры:
            // 1. Есть ли еще опубликованные объявления с таким контактным номером
            // 2. Проверка совпадения букв в адресе в ранее опубликованном объявлении и в текущем (адреса могут незначительно отличаться по написанию для дубликатов и значительно для разных объявлений)
            // 3. Сколько времени прошло с последней публикации. Собственник скорее всего в следующий раз будет публиковать через несколько месяцев, а агент через несколько дней
            // 4. Источник объявления тот же самый?
            // TODO: есть ли опубликованные объявления с таким номером телефона.
            // TODO: если есть с полнотой 1 - игнорируем это объявление
            // TODO: если есть с полнотой 0 - дополняем исходное объявление

            // Получим ассоциативный массив со сведениями по объявлению
            if (!($paramsArr = $parser->parseFullAdvert())) continue;

            // Проверим, что объявление относится к Екатеринбургу. Если нет, считаем его успешно обработанным и не добавляем в базу
            if (!isset($paramsArr['city']) || $paramsArr['city'] != "Екатеринбург") {
                $parser->setAdvertIsHandled();
                continue;
            }

            // Инициализируем флаг, обозначающий объявление-дубликат
            $isDuplicate = FALSE;

            // Проверяем объявление на дубликат.
            // Если уже есть в базе объявление с похожим адресом и такими же ключевыми параметрами, то считаем данное объявление дубликатом и лишь дополняем первоначальное объявление
            foreach ($propertiesId as $value) {

                // Инициализируем очередное объявление с таким же контактным номером
                $tempProperty = new Property($value);
                if (!$tempProperty->readCharacteristicFromDB() || !$tempProperty->readFotoInformationFromDB()) {
                    Logger::getLogger(GlobFunc::$loggerName)->log("parseURL.php:4 Не удалось инициализировать модель с id = '" . $value . "' и проверить на дубликат объявление по телефонному номеру " . $dataAboutPhoneNumber['phoneNumber']);
                }

                // Проверяем на совпадение url источника ИЛИ на совпадение адреса и ключевых параметров 2-х объявлений
                if (($paramsArr['sourceOfAdvert'] == $tempProperty->getSourceOfAdvert()) OR
                    ((isset($paramsArr['address'])) AND
                    ($parser->isAddressSimilar($paramsArr['address'], $tempProperty->getAddress())) AND
                    ($paramsArr['typeOfObject'] == $tempProperty->getTypeOfObject()) AND
                    (!isset($paramsArr['floor']) || $tempProperty->getFloor() == "" || $paramsArr['floor'] == 0 || $paramsArr['floor'] == $tempProperty->getFloor()) AND
                    (!isset($paramsArr['amountOfRooms']) || $tempProperty->getAmountOfRooms() == "0" || $paramsArr['amountOfRooms'] == 0 || $paramsArr['amountOfRooms'] == $tempProperty->getAmountOfRooms()))
                ) {

                    // TODO: Если ключевые параметры и адрес совпали, то дополняем исходное объявление данными текущего обрабатываемого объявления
                    // TODO: Пока же временное решение - отбрасываем и не обрабатываем далее объявление-дубликат
                    // TODO: если ссылка на объявление источник отличается от текущей, то занести ее новым членом в массив ссылок на объявления источники для данного объявления

                    // Оповещаем операторов о вычислении дубликата, если адреса объявлений источников отличаются
                    if ($paramsArr['sourceOfAdvert'] != $tempProperty->getSourceOfAdvert()) {
                        $subject = 'Дубликат!';
                        $msgHTML = "Объявление дубликат (с различными источниками) по адресу <a href='" . $paramsArr['sourceOfAdvert'] . "'> " . $paramsArr['address'] . "</a> . Дубликат для объявления <a href='http://svobodno.org/editadvert.php?propertyId=" . $tempProperty->getId() . "'>" . $tempProperty->getAddress() . "</a>";
                        GlobFunc::sendEmailToOperator($subject, $msgHTML);
                    }

                    // Поднимаем флаг, обозначающий объявление-дубликат
                    $isDuplicate = TRUE;

                    // После первого сопадения заканчиваем перебор объявлений с таким же контактным номером телефона
                    break;
                }

            }

            // Если полученное объявление не дублирует ни одно из имеющихся, то запишем обнаруженный дублирующий телефонный номер в специальную таблицу. Так как письма зачастую на e-mail не доходят, то оператору нужно работать с этой таблицей
            // Кроме того, необходимо сохранить объявление в БД
            if (!$isDuplicate) {

                // Добавим телефонный номер в список дублирующих
                DBconnect::insertDuplicatePhoneNumber($dataAboutPhoneNumber['phoneNumber']);

                // Сохраним объявление в БД
                $property = new Property($paramsArr);
                $property->setCompleteness("0");
                $property->setStatus("не опубликовано");
                $property->setOwnerLogin("owner"); // Используем в качестве логина собственника логин служебного собственника owner, на которого сохраняются все чужие объявления
                $correctSaveCharacteristicToDB = $property->saveCharacteristicToDB("new");
            }

            // Обновляем дату последнего использования телефонного номера
            $parser->updateDateKnownPhoneNumber();

            // Добавим объявление в список успешно обработанных, чтобы избежать в будущем его повторной обработки
            if (!$parser->setAdvertIsHandled()) {
                DBconnect::closeConnectToDB();
                exit();
            }

            // Переходим к следующему объявлению
            continue;
        }

        if ($methodOfAction == "") {
            Logger::getLogger(GlobFunc::$loggerName)->log("parseURL.php:5 не удалось выбрать сценарий обработки объявления по статусу телефонного номера");
            continue;
        }

        /*************************************************************************************
         * ПОЛУЧИМ АССОЦИАТИВНЫЙ МАССИВ СО СВЕДЕНИЯМИ ПО ОБЪЯВЛЕНИЮ
         ************************************************************************************/

        if (!($paramsArr = $parser->parseFullAdvert())) continue;

        /*************************************************************************************
         * УСЛОВИЯ УСПЕШНОГО ОКОНЧАНИЯ РАЗБОРА ОБЪЯВЛЕНИЯ
         ************************************************************************************/

        // Проверим, что объявление относится к Екатеринбургу. Если нет, считаем его успешно обработанным и не добавляем в базу
        if (!isset($paramsArr['city']) || $paramsArr['city'] != "Екатеринбург") {
            $parser->setAdvertIsHandled();
            continue;
        }

        /*************************************************************************************
         * СОХРАНЕНИЕ ОБЪЯВЛЕНИЯ В БД
         ************************************************************************************/

        // Инициализируем модель и сохраняем данные в БД
        $property = new Property($paramsArr);
        $property->setCompleteness("0");
        $property->setStatus("не опубликовано");
        $property->setOwnerLogin("owner"); // Используем в качестве логина собственника логин служебного собственника owner, на которого сохраняются все чужие объявления
        $correctSaveCharacteristicToDB = $property->saveCharacteristicToDB("new");

        // Добавим объявление в список успешно обработанных, чтобы избежать в будущем его повторной обработки
        if (!$parser->setAdvertIsHandled()) {
            DBconnect::closeConnectToDB();
            exit();
        }

        // Оповещаем операторов о новом объявлении
        /*$subject = 'Объявление на ' . $mode;
        $msgHTML = "Новое объявление на " . $mode . ": <a href='http://svobodno.org/editadvert.php?propertyId=" . $property->getId() . "'>" . $property->getAddress() . "</a>";
        GlobFunc::sendEmailToOperator($subject, $msgHTML);*/
    }

    // Когда мы переберем все объявления на текущей странице списка объявлений, то следующим шагом автоматически загрузится следующая страница - и все по новой
}

/********************************************************************************
 * Закрываем соединение с БД
 *******************************************************************************/

DBconnect::closeConnectToDB();