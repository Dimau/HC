<?php
/**
 * Класс представляет собой модель поискового запроса пользователя.
 * Правила класса:
 * 1. Все параметры при создании объекта инициализуются значениями по умолчанию. Обычно это пустые значения (пустые строки, пустые массивы или 0 если это option у селекта). В отличие от инициализации параметров объекта значением NULL или отказа от инициализации такое решение позволяет при использовании параметров сразу проверять их на тип (is_int, например), без проверки на существование (без использования isset())
 * 2. Методы по получению параметров из БД и по записи параметров в БД методами самого объекта не запускаются - только при их явном вызове снаружи объекта. Это позволяет отслеживать успех этих операций и соответствующим образом реагировать коду, который манипулирует объектом.
 */

class SearchRequest {
    private $id = "";
    private $userId = "";
    private $typeOfObject = "0";
    private $amountOfRooms = array();
    private $adjacentRooms = "0";
    private $floor = "0";
    private $minCost = "";
    private $maxCost = "";
    private $pledge = "";
    private $prepayment = "0";
    private $district = array();
    private $withWho = "0";
    private $linksToFriends = "";
    private $children = "0";
    private $howManyChildren = "";
    private $animals = "0";
    private $howManyAnimals = "";
    private $termOfLease = "0";
    private $additionalDescriptionOfSearch = "";
    private $regDate = "";
    private $needEmail = 1;
    private $needSMS = 0;
    private $typeOfSorting = "costAscending";

    private $propertyLightArr; // Массив массивов. После выполнения метода searchProperties содержит минимальные данные по ВСЕМ объектам, соответствующим условиям поиска
    private $propertyFullArr; // Массив массивов. После выполнения метода searchProperties содержит полные данные, включая фотографии, по нескольким первым в выборке объектам (количество указывается в качестве первого параметра к методу searchProperties)

    /**
     * КОНСТРУКТОР
     *
     * @param int|null $userId идентификатор пользователя, используется если необходимо инициализировать объект его параметрами поиска
     */
    public function __construct($userId) {
        if (isset($userId)) {
            $this->userId = $userId;
        }
    }

    public function getNeedEmail() {
        return $this->needEmail;
    }

    public function getTypeOfSorting() {
        return $this->typeOfSorting;
    }

    // Используется для установки id пользователя, которому принадлежит данный поисковый запрос
    public function setUserId($userId) {
        if (!isset($userId) || !is_int($userId)) return FALSE;
        $this->userId = $userId;
        return TRUE;
    }

    /**
     * Метод для сохранения параметров поискового запроса в БД
     *
     * @param string $mode режим сохранения данных
     * $mode = "new" режим сохранения нового поискового запроса
     * $mode = "edit" режим сохранения отредактированных параметров имеющегося в БД поискового запроса
     * @return bool TRUE в случае успешного сохранения данных и FALSE в противном случае
     */
    public function saveToDB($mode) {

        // Валидация входных параметров
        if ($mode != "edit" && $mode != "new") return FALSE;
        if (!is_int($this->userId)) return FALSE;

        // Сохранение в БД обновленных параметров поискового запроса
        if ($mode == "edit") {
            if (!DBconnect::updateSearchRequestForUser($this->getSearchRequestData())) return FALSE;
        }

        if ($mode == "new") {
            // Получим текущее время для того, чтобы пометить когда был создан поисковый запрос
            $this->regDate = time();
            if (!DBconnect::insertSearchRequestForUser($this->getSearchRequestData())) return FALSE;
            // Обновляем статус пользователя - теперь он арендатор
            if (!DBconnect::updateUserCharacteristicTypeUser($this->userId, "typeTenant", "TRUE")) return FALSE;
        }

        return TRUE;
    }

    // Перезаписать параметры объекта данными поискового запроса пользователя с id, указанным в $this->userId
    public function writeFromDB() {

        // Если идентификатор пользователя неизвестен, то дальнейшие действия не имеют смысла
        if ($this->userId == "") return FALSE;

        // Получим из БД данные ($res) по поисковому запросу пользователя с идентификатором = $this->id
        $res = DBconnect::selectSearchRequestForUser($this->userId);

        // Если получен пустой массив, то сообщаем о невозможности записи параметров поискового запроса из БД - у данного пользователя нет поискового запроса
        if (count($res) == 0) return FALSE;

        // Если данные по поисковому запросу есть в БД, присваиваем их соответствующим переменным, иначе - у них останутся значения по умолчанию.
        if (isset($res['userId'])) $this->userId = $res['userId'];
        if (isset($res['typeOfObject'])) $this->typeOfObject = $res['typeOfObject'];
        if (isset($res['amountOfRooms'])) $this->amountOfRooms = $res['amountOfRooms'];
        if (isset($res['adjacentRooms'])) $this->adjacentRooms = $res['adjacentRooms'];
        if (isset($res['floor'])) $this->floor = $res['floor'];
        if (isset($res['minCost'])) $this->minCost = $res['minCost'];
        if (isset($res['maxCost'])) $this->maxCost = $res['maxCost'];
        if (isset($res['pledge'])) $this->pledge = $res['pledge'];
        if (isset($res['prepayment'])) $this->prepayment = $res['prepayment'];
        if (isset($res['district'])) $this->district = $res['district'];
        if (isset($res['withWho'])) $this->withWho = $res['withWho'];
        if (isset($res['linksToFriends'])) $this->linksToFriends = $res['linksToFriends'];
        if (isset($res['children'])) $this->children = $res['children'];
        if (isset($res['howManyChildren'])) $this->howManyChildren = $res['howManyChildren'];
        if (isset($res['animals'])) $this->animals = $res['animals'];
        if (isset($res['howManyAnimals'])) $this->howManyAnimals = $res['howManyAnimals'];
        if (isset($res['termOfLease'])) $this->termOfLease = $res['termOfLease'];
        if (isset($res['additionalDescriptionOfSearch'])) $this->additionalDescriptionOfSearch = $res['additionalDescriptionOfSearch'];
        if (isset($res['regDate'])) $this->regDate = $res['regDate'];
        if (isset($res['needEmail'])) $this->needEmail = $res['needEmail'];
        if (isset($res['needSMS'])) $this->needSMS = $res['needSMS'];
        if (isset($res['typeOfSorting'])) $this->typeOfSorting = $res['typeOfSorting'];

        return TRUE;
    }

    // Инициализировать параметры поискового запроса данными из POST запроса пользователя (форма быстрого поиска)
    public function writeParamsFastFromGET() {
        if (isset($_GET['typeOfObjectFast'])) $this->typeOfObject = htmlspecialchars($_GET['typeOfObjectFast'], ENT_QUOTES);
        if (isset($_GET['districtFast']) && $_GET['districtFast'] != "0") $this->district = array(htmlspecialchars($_GET['districtFast'], ENT_QUOTES));
        if (isset($_GET['districtFast']) && $_GET['districtFast'] == "0") $this->district = array();
        if (isset($_GET['minCostFast']) && preg_match("/^\d{0,8}$/", $_GET['minCostFast'])) $this->minCost = htmlspecialchars($_GET['minCostFast'], ENT_QUOTES); // Значение, введенное пользователем, затирает значение по умолчанию только если оно соответствует формату
        if (isset($_GET['maxCostFast']) && preg_match("/^\d{0,8}$/", $_GET['maxCostFast'])) $this->maxCost = htmlspecialchars($_GET['maxCostFast'], ENT_QUOTES); // Значение, введенное пользователем, затирает значение по умолчанию только если оно соответствует формату
        if (isset($_GET['typeOfSortingFast'])) $this->typeOfSorting = htmlspecialchars($_GET['typeOfSortingFast'], ENT_QUOTES);
    }

    // Инициализировать параметры поискового запроса данными из POST запроса пользователя (форма поиска с подробными параметрами)
    public function writeParamsExtendedFromGET() {
        if (isset($_GET['typeOfObject'])) $this->typeOfObject = htmlspecialchars($_GET['typeOfObject'], ENT_QUOTES);
        if (isset($_GET['amountOfRooms']) && is_array($_GET['amountOfRooms'])) {
            $this->amountOfRooms = array();
            foreach ($_GET['amountOfRooms'] as $value) $this->amountOfRooms[] = htmlspecialchars($value, ENT_QUOTES);
        } else $this->amountOfRooms = array();
        if (isset($_GET['adjacentRooms'])) $this->adjacentRooms = htmlspecialchars($_GET['adjacentRooms'], ENT_QUOTES);
        if (isset($_GET['floor'])) $this->floor = htmlspecialchars($_GET['floor'], ENT_QUOTES);
        if (isset($_GET['minCost']) && preg_match("/^\d{0,8}$/", $_GET['minCost'])) $this->minCost = htmlspecialchars($_GET['minCost'], ENT_QUOTES); // Значение, введенное пользователем, затирает значение по умолчанию только если оно соответствует формату
        if (isset($_GET['maxCost']) && preg_match("/^\d{0,8}$/", $_GET['maxCost'])) $this->maxCost = htmlspecialchars($_GET['maxCost'], ENT_QUOTES); // Значение, введенное пользователем, затирает значение по умолчанию только если оно соответствует формату
        if (isset($_GET['pledge']) && preg_match("/^\d{0,8}$/", $_GET['pledge'])) $this->pledge = htmlspecialchars($_GET['pledge'], ENT_QUOTES); // Значение, введенное пользователем, затирает значение по умолчанию только если оно соответствует формату
        if (isset($_GET['prepayment'])) $this->prepayment = htmlspecialchars($_GET['prepayment'], ENT_QUOTES);
        if (isset($_GET['district']) && is_array($_GET['district'])) {
            $this->district = array();
            foreach ($_GET['district'] as $value) $this->district[] = htmlspecialchars($value, ENT_QUOTES);
        } else $this->district = array();
        if (isset($_GET['withWho'])) $this->withWho = htmlspecialchars($_GET['withWho'], ENT_QUOTES);
        if (isset($_GET['children'])) $this->children = htmlspecialchars($_GET['children'], ENT_QUOTES);
        if (isset($_GET['animals'])) $this->animals = htmlspecialchars($_GET['animals'], ENT_QUOTES);
        if (isset($_GET['termOfLease'])) $this->termOfLease = htmlspecialchars($_GET['termOfLease'], ENT_QUOTES);
        if (isset($_GET['typeOfSorting'])) $this->typeOfSorting = htmlspecialchars($_GET['typeOfSorting'], ENT_QUOTES);
    }

    // Записать в качестве параметров поискового запроса данные, полученные через POST запрос
    public function writeParamsFromPOST() {

        if (isset($_POST['typeOfObject'])) $this->typeOfObject = htmlspecialchars($_POST['typeOfObject'], ENT_QUOTES);
        if (isset($_POST['amountOfRooms']) && is_array($_POST['amountOfRooms'])) {
            $this->amountOfRooms = array();
            foreach ($_POST['amountOfRooms'] as $value) $this->amountOfRooms[] = htmlspecialchars($value, ENT_QUOTES);
        } else $this->amountOfRooms = array(); // Если пользователь отправил форму submit, и в параметрах нет значения amountOfRooms, значит пользователь не отметил ни один чекбокс из группы, чему соответствует пустой массив
        if (isset($_POST['district']) && is_array($_POST['district'])) {
            $this->district = array();
            foreach ($_POST['district'] as $value) $this->district[] = htmlspecialchars($value, ENT_QUOTES);
        } else $this->district = array(); // Если пользователь отправил форму submit, и в параметрах нет значения district, значит пользователь не отметил ни один чекбокс из группы, чему соответствует пустой массив
        if (isset($_POST['adjacentRooms'])) $this->adjacentRooms = htmlspecialchars($_POST['adjacentRooms'], ENT_QUOTES);
        if (isset($_POST['floor'])) $this->floor = htmlspecialchars($_POST['floor'], ENT_QUOTES);
        if (isset($_POST['minCost'])) $this->minCost = htmlspecialchars($_POST['minCost'], ENT_QUOTES);
        if (isset($_POST['maxCost'])) $this->maxCost = htmlspecialchars($_POST['maxCost'], ENT_QUOTES);
        if (isset($_POST['pledge'])) $this->pledge = htmlspecialchars($_POST['pledge'], ENT_QUOTES);
        if (isset($_POST['prepayment'])) $this->prepayment = htmlspecialchars($_POST['prepayment'], ENT_QUOTES);
        if (isset($_POST['withWho'])) $this->withWho = htmlspecialchars($_POST['withWho'], ENT_QUOTES);
        if (isset($_POST['linksToFriends'])) $this->linksToFriends = htmlspecialchars($_POST['linksToFriends'], ENT_QUOTES);
        if (isset($_POST['children'])) $this->children = htmlspecialchars($_POST['children'], ENT_QUOTES);
        if (isset($_POST['howManyChildren'])) $this->howManyChildren = htmlspecialchars($_POST['howManyChildren'], ENT_QUOTES);
        if (isset($_POST['animals'])) $this->animals = htmlspecialchars($_POST['animals'], ENT_QUOTES);
        if (isset($_POST['howManyAnimals'])) $this->howManyAnimals = htmlspecialchars($_POST['howManyAnimals'], ENT_QUOTES);
        if (isset($_POST['termOfLease'])) $this->termOfLease = htmlspecialchars($_POST['termOfLease'], ENT_QUOTES);
        if (isset($_POST['additionalDescriptionOfSearch'])) $this->additionalDescriptionOfSearch = htmlspecialchars($_POST['additionalDescriptionOfSearch'], ENT_QUOTES);
        if (isset($_POST['needEmail'])) $this->needEmail = intval(htmlspecialchars($_POST['needEmail'], ENT_QUOTES)); else $this->needEmail = 0;
        if (isset($_POST['needSMS'])) $this->needSMS = intval(htmlspecialchars($_POST['needSMS'], ENT_QUOTES)); else $this->needSMS = 0;
        if (isset($_POST['typeOfSorting'])) $this->typeOfSorting = htmlspecialchars($_POST['typeOfSorting'], ENT_QUOTES);
    }

    /**
     * Проверка корректности параметров поискового запроса
     * @param string $typeOfValidation режим проверки параметров поискового запроса
     * $typeOfValidation = personalRequest - режим строгой (полной) проверки поискового запроса конкретного пользователя, параметры которого мы сохраним в БД и будем показывать собственникам вместе с анкетой пользователя (используется при регистрации арендатора и при редактировании параметров в личном кабинете)
     * $typeOfValidation = oneTimeRequest - режим проверки указанных пользователем параметров поиска для единоразового использования на странице поиска (search.php), без перспективы сохранения в БД.
     * @return array $errors массив строк, каждая из которых представляет собой сообщение об 1 ошибке. Если валидация ошибок не обнаружила, то метод вернет пустой массив
     */
    public function validate($typeOfValidation) {

        // Подготовим массив для сохранения сообщений об ошибках
        $errors = array();

        if ($typeOfValidation == "personalRequest") {
            if (!preg_match("/^\d{0,8}$/", $this->minCost)) $errors[] = 'Неправильный формат числа в поле минимальной величины арендной платы (проверьте: только числа, не более 8 символов)';
        }
        if ($typeOfValidation == "personalRequest") {
            if (!preg_match("/^\d{0,8}$/", $this->maxCost)) $errors[] = 'Неправильный формат числа в поле максимальной величины арендной платы (проверьте: только числа, не более 8 символов)';
        }
        if ($typeOfValidation == "personalRequest") {
            if (!preg_match("/^\d{0,8}$/", $this->pledge)) $errors[] = 'Неправильный формат числа в поле максимальной величины залога (проверьте: только числа, не более 8 символов)';
        }
        if ($typeOfValidation == "personalRequest") {
            if ($this->minCost > $this->maxCost) $errors[] = 'Минимальная стоимость аренды не может быть больше, чем максимальная. Исправьте поля, в которых указаны Ваши требования к диапазону стоимости аренды';
        }

        return $errors; // Возвращаем список ошибок, если все в порядке, то он будет пуст
    }

    /**
     * Метод удаляет параметры поискового запроса пользователя из БД, сбрасывает соответствующие настройки объекта на "по-умолчанию", а также меняет статус isTenant на FALSE.
     *     1. Проверяем, что у данного пользователя нет заявок на просмотр со статусом "Назначен просмотр", в противном случае, не позволяем удалять поисковый запрос
     *  2. Удаляем параметры поискового запроса пользователя из БД
     *     3. Сбрасывает статус арендатора на FALSE (значение typeTenant в таблице users)
     *     4. Удаляет все уведомления типа "Новый подходящий объект недвижимости"
     *     5. Изменяем параметры текущего объекта
     * При этом нетронутым остается список избранных объектов пользователя - он ему пригодиться в следующий раз
     *
     * @return array массив строк, каждая из которых представляет сведения по 1 ошибке, не позволившей удалить поисковый запрос. В случае успеха - возвращаемый массив пуст
     */
    public function remove() {

        // Проверка на наличие id пользователя
        if ($this->userId == "") return array("Не удалось удалить поисковый запрос: недостаточно данных. Попробуйте повторить немного позже или свяжитесь с нами: 8-922-179-59-07");

        // Удалим данные поискового запроса по данному пользователю из БД
        if (!DBconnect::deleteSearchRequestsForUser($this->userId)) return array("Не удалось удалить поисковый запрос: ошибка обращения к базе. Попробуйте повторить немного позже или свяжитесь с нами: 8-922-179-59-07");

        // Обновляем статус данного пользователю (он больше не арендатор)
        if (!DBconnect::updateUserCharacteristicTypeUser($this->userId, "typeTenant", "FALSE")) return array("Не удалось удалить поисковый запрос: ошибка обращения к базе. Попробуйте повторить немного позже или свяжитесь с нами: 8-922-179-59-07");

        // Удалим все уведомления типа "Новый подходящий объект недвижимости" у данного арендатора
        DBconnect::deleteMessagesNewPropertyForUser($this->userId);

        // Скинем на дефолтные параметры поискового запроса данного пользователя
        $this->id = "";
        $this->typeOfObject = "0";
        $this->amountOfRooms = array();
        $this->adjacentRooms = "0";
        $this->floor = "0";
        $this->minCost = "";
        $this->maxCost = "";
        $this->pledge = "";
        $this->prepayment = "0";
        $this->district = array();
        $this->withWho = "0";
        $this->linksToFriends = "";
        $this->children = "0";
        $this->howManyChildren = "";
        $this->animals = "0";
        $this->howManyAnimals = "";
        $this->termOfLease = "0";
        $this->additionalDescriptionOfSearch = "";
        $this->regDate = "";
        $this->needEmail = 1;
        $this->needSMS = 0;
        $this->typeOfSorting = "costAscending";

        return array();
    }

    /**
     * Вычисляет массивы:
     * 1. C краткими данными (id, coordX, coordY) о ВСЕХ объектах недвижимости, соответствующих параметрам поискового запроса
     * 2. Кроме того по первым $amountFullProperties объектам недвижимости вычисляет полные данные, даже с фотографиями.
     * @param $amountFullProperties количество объявлений, по которым нужно получить полные данные
     * @return bool TRUE в случае успешного завершения выполнения алгоритма и FALSE в противном случае
     */
    public function searchProperties($amountFullProperties) {

        // Валидация входных данных
        if (!isset($amountFullProperties)) return FALSE;

        // Получим минимальные данные (id, coordX, coordY) по всем объектам недвижимости, подходящим под параметры поискового запроса
        $this->findPropertyLightArr();

        // Получим полные данные по первым $amountFullProperties объектам недвижимости
        $this->findPropertyFullArr($amountFullProperties);

        // Если по каким-то объектам из $this->propertyLightArr получить полные данные не удалось, удалим их
        if ($amountFullProperties <= count($this->propertyLightArr)) $limit = $amountFullProperties; else $limit = count($this->propertyLightArr);
        $markForSort = FALSE; // Метка, говорящая, требуется ли пересортировка массиву $this->propertyLightArr (если будет удален хотя бы 1 элемент из него), или нет
        for ($i = 0; $i < $limit; $i++) {
            $markForRemove = TRUE;
            foreach ($this->propertyFullArr as $value) {
                if ($this->propertyLightArr[$i]['id'] == $value['id']) {
                    $markForRemove = FALSE;
                    break;
                }
            }
            // Проверяем метку на удаление. Если для элемент $this->propertyLightArr[$i] не нашелся соответствующий в $this->propertyFullArr, значит не удалось получить данные по этому объекту недвижимости
            if ($markForRemove) {
                unset($this->propertyLightArr[$i]);
                $markForSort = TRUE;
            }
        }
        // Если был удален хотя бы 1 элемент, переиндексируем массив
        if ($markForSort) $this->propertyLightArr = array_values($this->propertyLightArr);

        return TRUE;
    }

    /**
     * Метод записывает в параметр $this->propertyLightArr массив массивов, содержащий минимальные данные по всем объектам недвижимости, соответствующим данным условиям поиска
     * @return bool TRUE в случае успешного завершения выполнения алгоритма и FALSE в противном случае
     */
    private function findPropertyLightArr() {

        // Выбираем вариант сортировки
        switch ($this->typeOfSorting) {
            case "costAscending":
                $typeOfSortingString = "realCostOfRenting + costInSummer * realCostOfRenting / costOfRenting ASC";
                break;
            case "costDescending":
                $typeOfSortingString = "realCostOfRenting + costInSummer * realCostOfRenting / costOfRenting DESC";
                break;
            case "publicationDateDescending":
                $typeOfSortingString = "reg_date DESC";
                break;
            default:
                return FALSE;
        }

        // Инициализируем массив, в который будем собирать условия поиска.
        $searchLimits = array();

        // Ограничение на тип объекта
        $searchLimits['typeOfObject'] = "";
        if ($this->typeOfObject == "0") $searchLimits['typeOfObject'] = "";
        if ($this->typeOfObject == "квартира" || $this->typeOfObject == "комната" || $this->typeOfObject == "дом" || $this->typeOfObject == "таунхаус" || $this->typeOfObject == "дача" || $this->typeOfObject == "гараж") {
            $searchLimits['typeOfObject'] = " (typeOfObject = '" . $this->typeOfObject . "' OR typeOfObject = '0')"; // Думаю, что с точки зрения безопасности (чтобы нельзя было подсунуть в запрос левые SQL подобные строки), нужно перечислять все доступные варианты
        }

        // Ограничение на количество комнат
        $searchLimits['amountOfRooms'] = "";
        if (count($this->amountOfRooms) != 0) {
            $searchLimits['amountOfRooms'] = " (";
            for ($i = 0, $s = count($this->amountOfRooms); $i < $s; $i++) {
                $searchLimits['amountOfRooms'] .= " amountOfRooms = '" . $this->amountOfRooms[$i] . "'";
                if ($i < $s - 1) $searchLimits['amountOfRooms'] .= " OR";
            }
            $searchLimits['amountOfRooms'] .= " OR amountOfRooms = '0')";
        }

        // Ограничение на смежность комнат
        $searchLimits['adjacentRooms'] = "";
        if ($this->adjacentRooms == "0") $searchLimits['adjacentRooms'] = "";
        if ($this->adjacentRooms == "не имеет значения") $searchLimits['adjacentRooms'] = "";
        if ($this->adjacentRooms == "только изолированные") $searchLimits['adjacentRooms'] = " (adjacentRooms != 'да')";

        // Ограничение на этаж
        $searchLimits['floor'] = "";
        if ($this->floor == "0") $searchLimits['floor'] = "";
        if ($this->floor == "любой") $searchLimits['floor'] = "";
        if ($this->floor == "не первый") $searchLimits['floor'] = " (floor != 1)";
        if ($this->floor == "не первый и не последний") $searchLimits['floor'] = " (floor != 1 AND floor != totalAmountFloor)";

        // Ограничение на минимальную сумму арендной платы
        $searchLimits['minCost'] = "";
        if ($this->minCost == "") $searchLimits['minCost'] = "";
        if ($this->minCost != "") $searchLimits['minCost'] = " (realCostOfRenting + costInSummer * realCostOfRenting / costOfRenting >= " . $this->minCost . ")";

        // Ограничение на максимальную сумму арендной платы
        $searchLimits['maxCost'] = "";
        if ($this->maxCost == "") $searchLimits['maxCost'] = "";
        if ($this->maxCost != "") $searchLimits['maxCost'] = " (realCostOfRenting + costInSummer * realCostOfRenting / costOfRenting <= " . $this->maxCost . ")";

        // Ограничение на максимальный залог
        $searchLimits['pledge'] = "";
        if ($this->pledge == "") $searchLimits['pledge'] = "";
        if ($this->pledge != "") $searchLimits['pledge'] = " (bailCost * realCostOfRenting / costOfRenting <= " . $this->pledge . ")"; // отношение realCostOfRenting / costOfRenting позволяет вычислить курс валюты, либо получить 1, если стоимость аренды указана собственником в рублях

        // Ограничение на предоплату
        $searchLimits['prepayment'] = "";
        if ($this->prepayment == "0") $searchLimits['prepayment'] = "";
        if ($this->prepayment != "0") $searchLimits['prepayment'] = " (prepayment + 0 <= '" . $this->prepayment . "')";

        // Ограничение на район
        $searchLimits['district'] = "";
        if (count($this->district) == 0) $searchLimits['district'] = "";
        if (count($this->district) != 0) {
            $searchLimits['district'] = " (";
            for ($i = 0, $s = count($this->district); $i < $s; $i++) {
                $searchLimits['district'] .= " district = '" . $this->district[$i] . "'";
                if ($i < count($this->district) - 1) $searchLimits['district'] .= " OR";
            }
            $searchLimits['district'] .= " OR district = '0')";
        }

        // Ограничение на формат проживания (с кем собираетесь проживать)
        $searchLimits['withWho'] = "";
        if ($this->withWho == "0") $searchLimits['withWho'] = "";
        if ($this->withWho == "самостоятельно") $searchLimits['withWho'] = " (relations LIKE '%один человек%' OR relations = 'a:0:{}')";
        if ($this->withWho == "семья") $searchLimits['withWho'] = " (relations LIKE '%семья%' OR relations = 'a:0:{}')";
        if ($this->withWho == "пара") $searchLimits['withWho'] = " (relations LIKE '%пара%' OR relations = 'a:0:{}')";
        if ($this->withWho == "2 мальчика") $searchLimits['withWho'] = " (relations LIKE '%2 мальчика%' OR relations = 'a:0:{}')";
        if ($this->withWho == "2 девочки") $searchLimits['withWho'] = " (relations LIKE '%2 девочки%' OR relations = 'a:0:{}')";
        if ($this->withWho == "со знакомыми") $searchLimits['withWho'] = " (relations LIKE '%группа людей%' OR relations = 'a:0:{}')";

        // Ограничение на проживание с детьми
        $searchLimits['children'] = "";
        if ($this->children == "0" || $this->children == "без детей") $searchLimits['children'] = "";
        if ($this->children == "с детьми старше 4-х лет") $searchLimits['children'] = " (children != 'только без детей')";
        if ($this->children == "с детьми младше 4-х лет") $searchLimits['children'] = " (children != 'только без детей' AND children != 'с детьми старше 4-х лет')";

        // Ограничение на проживание с животными
        $searchLimits['animals'] = "";
        if ($this->animals == "0" || $this->animals == "без животных") $searchLimits['animals'] = "";
        if ($this->animals == "с животным(ми)") $searchLimits['animals'] = " (animals != 'только без животных')";

        // Ограничение на длительность аренды
        $searchLimits['termOfLease'] = "";
        if ($this->termOfLease == "0") $searchLimits['termOfLease'] = "";
        if ($this->termOfLease == "длительный срок") $searchLimits['termOfLease'] = " (termOfLease = 'длительный срок' OR termOfLease = '0')";
        if ($this->termOfLease == "несколько месяцев") $searchLimits['termOfLease'] = " (termOfLease = 'несколько месяцев' OR termOfLease = '0')";

        // Показываем только опубликованные объявления
        $searchLimits['status'] = " (status = 'опубликовано')";

        // Собираем строку WHERE для поискового запроса к БД
        $strWHERE = "";
        foreach ($searchLimits as $value) {
            if ($value == "") continue;
            if ($strWHERE != "") $strWHERE .= " AND" . $value; else $strWHERE .= $value;
        }

        // Получаем данные из БД - ВСЕ объекты недвижимости, соответствующие поисковому запросу
        // Сортируем по стоимости аренды и не ограничиваем количество объявлений - все, подходящие под условия пользователя
        // В итоге получим массив ($propertyLightArr), каждый элемент которого представляет собой еще один массив значений конкретного объявления по недвижимости
        $res = DBconnect::get()->query("SELECT id, coordX, coordY FROM property WHERE" . $strWHERE . " ORDER BY " . $typeOfSortingString);
        if ((DBconnect::get()->errno)
            OR (($this->propertyLightArr = $res->fetch_all(MYSQLI_ASSOC)) === FALSE)
        ) {
            // Логируем ошибку
            //TODO: сделать логирование ошибки
            $this->propertyLightArr = array();
            return FALSE;
        }

        return TRUE;
    }

    // Метод записывает в параметр $this->propertyFullArr массив массивов, содержащий полные данные (в том числе с фото) по первым $amountFullProperties объектам недвижимости из массива $this->propertyLightArr
    private function findPropertyFullArr($amountFullProperties) {

        // Проверим входные параметры
        if (!isset($amountFullProperties) || $amountFullProperties == 0) $this->propertyFullArr = array();

        // Сколько всего будет объектов с полными данными в итоге
        if ($amountFullProperties <= count($this->propertyLightArr)) $limit = $amountFullProperties; else $limit = count($this->propertyLightArr);

        // Вычислим массив id объектов, по которым требуется получить полные данные
        $propertiesIdForFullData = array();
        for ($i = 0; $i < $limit; $i++) {
            $propertiesIdForFullData[] = $this->propertyLightArr[$i]['id'];
        }

        // Получим массив с полными данными (в том числе с фото) по требующимся объявлениям
        $this->propertyFullArr = DBconnect::getFullDataAboutProperties($propertiesIdForFullData, "all");
        // Если полные данные получить не удалось - запишем пустой массив в результат
        if ($this->propertyFullArr == FALSE) $this->propertyFullArr = array();

    }

    // Возвращает массив массивов $this->propertyLightArr
    public function getPropertyLightArr() {
        return $this->propertyLightArr;
    }

    // Возвращает массив массивов $this->propertyFullArr
    public function getPropertyFullArr() {
        return $this->propertyFullArr;
    }

    // Возвращает ассоциированный массив с данными о поисковом запросе (для использования в представлении)
    public function getSearchRequestData() {

        $result = array();

        $result['id'] = $this->id;
        $result['userId'] = $this->userId;
        $result['typeOfObject'] = $this->typeOfObject;
        $result['amountOfRooms'] = $this->amountOfRooms;
        $result['adjacentRooms'] = $this->adjacentRooms;
        $result['floor'] = $this->floor;
        $result['minCost'] = $this->minCost;
        $result['maxCost'] = $this->maxCost;
        $result['pledge'] = $this->pledge;
        $result['prepayment'] = $this->prepayment;
        $result['district'] = $this->district;
        $result['withWho'] = $this->withWho;
        $result['linksToFriends'] = $this->linksToFriends;
        $result['children'] = $this->children;
        $result['howManyChildren'] = $this->howManyChildren;
        $result['animals'] = $this->animals;
        $result['howManyAnimals'] = $this->howManyAnimals;
        $result['termOfLease'] = $this->termOfLease;
        $result['additionalDescriptionOfSearch'] = $this->additionalDescriptionOfSearch;
        $result['regDate'] = $this->regDate;
        $result['needEmail'] = $this->needEmail;
        $result['needSMS'] = $this->needSMS;
        $result['typeOfSorting'] = $this->typeOfSorting;

        return $result;
    }
}