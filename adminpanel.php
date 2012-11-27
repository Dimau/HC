<?php
    // Стартуем сессию с пользователем - сделать доступными переменные сессии
    session_start();

    // Подключаем нужные модели и представления
    include 'models/DBconnect.php';
    include 'models/GlobFunc.php';
    include 'models/Logger.php';
    include 'models/IncomingUser.php';
    include 'views/View.php';
    include 'models/User.php';

    // Удалось ли подключиться к БД?
    if (DBconnect::get() == FALSE) die('Ошибка подключения к базе данных (. Попробуйте зайти к нам немного позже.');

    // Инициализируем модель для запросившего страницу пользователя
    $incomingUser = new IncomingUser();

    $action = "";
    if (isset($_GET['action']) && $_GET['action'] != "") {
        $action = $_GET['action']; // Получаем команду из строки запроса
    }

    /*************************************************************************************
     * Проверяем - может ли данный пользователь просматривать данную страницу
     ************************************************************************************/

    // Если пользователь не авторизирован, то пересылаем юзера на страницу авторизации
    if (!$incomingUser->login()) {
        header('Location: login.php');
    }

    //TODO: ограничить доступ только администраторами
    //TODO: вкладками решать проблемы контроля доступа

    /*************************************************************************************
     * НОВЫЙ СОБСТВЕННИК: регистрация пользователя
     ************************************************************************************/

    if ($action == "registrationNewOwner") {

        // Сначала "разавторизовываем" пользователя
        if (!isset($_SESSION)) {
            session_start();
        }
        unset($_SESSION['id']); //удаляем переменную сессии
        $_SESSION = array();
        session_unset();
        session_destroy();
        SetCookie("login", "", time() - 3600, '/'); //удаляем cookie с логином
        SetCookie("password", "", time() - 3600, '/'); //удаляем cookie с паролем

        // Затем перекидываем его на страницу регистрации собственника
        header("Location: registration.php?typeOwner=true");
    }

    /********************************************************************************
     * ФОРМИРОВАНИЕ ПРЕДСТАВЛЕНИЯ (View)
     *******************************************************************************/

    // Инициализируем используемые в шаблоне(ах) переменные
    $isLoggedIn = $incomingUser->login(); // Используется в templ_header.php
    $amountUnreadMessages = $incomingUser->getAmountUnreadMessages();

    // Подсоединяем нужный основной шаблон
    include "templates/"."templ_adminpanel.php";

    /********************************************************************************
     * Закрываем соединение с БД
     *******************************************************************************/

    DBconnect::closeConnectToDB();