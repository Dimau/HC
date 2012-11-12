<?php

    /*************************************************************************************
     * Если в строке не указан идентификатор объявления, то пересылаем пользователя на спец. страницу
     ************************************************************************************/

    $propertyId = "0";
    if (isset($_GET['propertyId']) && $_GET['propertyId'] != "") {
        $propertyId = $_GET['propertyId']; // Получаем идентификатор объявления для показа из строки запроса
    } else {
        header('Location: 404.html'); // Если в запросе не указан идентификатор объявления для редактирования, то пересылаем пользователя в личный кабинет к списку его объявлений
    }

    /*************************************************************************************
     * Инициализируем требуемые модели
     ************************************************************************************/

    // Стартуем сессию с пользователем - сделать доступными переменные сессии
    session_start();

    // Подключаем нужные модели и представления
    include 'models/GlobFunc.php';
    include 'models/Logger.php';
    include 'models/IncomingUser.php';
    include 'views/View.php';
    include 'models/Property.php';

    // Создаем объект-хранилище глобальных функций
    $globFunc = new GlobFunc();

    // Подключаемся к БД
    $DBlink = $globFunc->connectToDB();
    // Удалось ли подключиться к БД?
    if ($DBlink == FALSE) die('Ошибка подключения к базе данных (. Попробуйте зайти к нам немного позже.');

    // Инициализируем модель для запросившего страницу пользователя
    $incomingUser = new IncomingUser($globFunc, $DBlink);

    // Инициализируем переменную для хранения статуса запроса на просмотр данного объекта недвижимости
    // Может принимать значения:
    // "new" - запрос еще не отправлен данным пользователем,
    // "waitingForConfirmation" - был отправлен, но еще не подтвержден собственником,
    // "error" - был отправлен, но при передаче произошла ошибка
    // "confirmed" - был отправлен и со стороны собственника получено подтверждение
    $signUpToViewStatus = "";

    /*************************************************************************************
     * Получаем данные объявления для просмотра, а также другие данные из БД
     ************************************************************************************/

    $property = new Property($globFunc, $DBlink, $propertyId);

    // Анкетные данные и данные о фотографиях объекта недвижимости
    $property->writeCharacteristicFromDB();
    $property->writeFotoInformationFromDB();

    /*************************************************************************************
     * Проверяем - может ли данный пользователь просматривать данное объявление
     ************************************************************************************/

    // Если объявление опубликовано, то его может просматривать каждый
    // Если объявление закрыто (снято с публикации), то его может просматривать только сам собственник
    if ($property->status == "не опубликовано" && $property->userId != $incomingUser->getId()) header('Location: 404.html');
    //TODO: реализовать соответствующую 404 страницу

    /*************************************************************************************
     * Получаем заголовок страницы
     ************************************************************************************/
    $strHeaderOfPage = $globFunc->getFirstCharUpper($property->typeOfObject)." по адресу: ".$property->address;

    /************************************************************************************
     * НОВЫЙ ЗАПРОС НА ПРОСМОТР. Если пользователь отправил форму запроса на просмотр объекта
     ***********************************************************************************/

    if (isset($_POST['signUpToViewDialogButton'])) {


    }

    /********************************************************************************
     * ФОРМИРОВАНИЕ ПРЕДСТАВЛЕНИЯ (View)
     *******************************************************************************/

    $view = new View($globFunc, $DBlink);
    $view->generate("templ_objdescription.php", array('userCharacteristic' => array('name' => $incomingUser->name, 'secondName' => $incomingUser->secondName, 'surname' => $incomingUser->surname, 'telephon' => $incomingUser->telephon),
                                                 'propertyCharacteristic' => $property->getCharacteristicData(),
                                                 'propertyFotoInformation' => $property->getFotoInformationData(),
                                                 'isLoggedIn' => $incomingUser->login(),
                                                 'favoritesPropertysId' => $incomingUser->getFavoritesPropertysId(),
                                                 'strHeaderOfPage' => $strHeaderOfPage));

    /********************************************************************************
     * Закрываем соединение с БД
     *******************************************************************************/

    $globFunc->closeConnectToDB($DBlink);