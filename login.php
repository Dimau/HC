<?php
// Стартуем сессию с пользователем - сделать доступными переменные сессии
session_start();

// Подключаем нужные модели и представления
$websiteRoot = $_SERVER['DOCUMENT_ROOT'];
require_once $websiteRoot . '/models/DBconnect.php';
require_once $websiteRoot . '/models/GlobFunc.php';
require_once $websiteRoot . '/models/Logger.php';
require_once $websiteRoot . '/models/User.php';
require_once $websiteRoot . '/models/UserIncoming.php';
require_once $websiteRoot . '/views/View.php';

// Удалось ли подключиться к БД?
if (DBconnect::get() == FALSE) die('Ошибка подключения к базе данных (. Попробуйте зайти к нам немного позже.');

// Инициализируем модель для запросившего страницу пользователя
$userIncoming = new UserIncoming();

// Если пользователь уже авторизирован, перенаправим его в личный кабинет
if ($userIncoming->login()) {
    header('Location: personal.php');
    exit();
}

// Инициализируем массив для хранения ошибок входа (авторизации)
$errors = array();

/*************************************************************************************
 * ПОЛУЧИМ GET ПАРАМЕТРЫ
 ************************************************************************************/
// Для защиты от XSS атаки и для использования в коде более простого имени для переменной

// Получаем команду из строки запроса
$action = "";
if (isset($_GET['action'])) $action = htmlspecialchars($_GET['action'], ENT_QUOTES);

/********************************************************************************
 * ОТКУДА К НАМ ПРИШЕЛ ПОЛЬЗОВАТЕЛЬ
 *******************************************************************************/

// Запоминаем в параметры сессии адрес URL, с которого на авторизацию пришел пользователь (это позволит вернуть его после авторизации на ту же страницу)
if (isset($_SERVER['HTTP_REFERER'])) {
    $hostName = explode("?", $_SERVER['HTTP_REFERER']);
    // Важно модифицировать адрес, с которого пользователь попал на страницу авторизации только, если он не перегружал саму страницу авторизации (например, в случае отправки ошибочной формы)
    if ($hostName[0] != "http://svobodno.org/login.php" && $hostName[0] != "http://localhost/login.php") {
        $_SESSION['url_initial'] = $_SERVER['HTTP_REFERER'];
    }
}
// Если вдруг в переменную сессии попал адрес, который не относится к нашему домену, то удалим его от греха подальше
if (isset($_SESSION['url_initial']) && !preg_match('~((http://svobodno.org)|(http://localhost))~', $_SESSION['url_initial'])) {
    unlink($_SESSION['url_initial']);
}

/********************************************************************************
 * НАЖАЛ НА КНОПКУ ВОЙТИ
 *******************************************************************************/

if ($action == "signIn") {

    $errors = $userIncoming->enter(); //функция входа на сайт

    if (is_array($errors) && count($errors) == 0) // Если нет ошибок - пользователь успешно авторизован, понимаем, куда теперь его нужно переслать
    {
        // Уточняем - имеет ли пользователь права админа
        // Строка расположена не вверху страницы как обычно, а после выполнения метода $userIncoming->enter(), так как только после ее выполнения мы узнаем настоящие права на администрирование у пользователя
        $isAdmin = $userIncoming->isAdmin();

        // Если мы знаем страницу, с которой пришел пользователь и это не главная, то перешлем его на исходную страницу, с которой он и попал на авторизацию
        if (isset($_SESSION['url_initial']) && $_SESSION['url_initial'] != "http://svobodno.org/index.php" && $_SESSION['url_initial'] != "http://localhost/index.php" && $_SESSION['url_initial'] != "http://svobodno.org/" && $_SESSION['url_initial'] != "http://localhost/") {
            header('Location: ' . $_SESSION['url_initial']);
            exit();
        } elseif ($isAdmin['newOwner'] || $isAdmin['newAdvertAlien'] || $isAdmin['searchUser']) {
            // Если авторизовавшийся пользователь администратор, то пересылаем его в админку
            header('Location: adminpanel.php');
            exit();
        } else {
            // Если авторизовавшийся пользователь не администратор, то пересылаем его в личный кабинет
            header('Location: personal.php');
            exit();
        }
    }
    // Если при авторизации возникли ошибки, мы их покажем в специальном всплывающем сверху блоке вместе со страницей авторизации
}

/********************************************************************************
 * ФОРМИРОВАНИЕ ПРЕДСТАВЛЕНИЯ (View)
 *******************************************************************************/

// Инициализируем используемые в шаблоне(ах) переменные
$isLoggedIn = $userIncoming->login(); // Используется в templ_header.php
$amountUnreadMessages = $userIncoming->getAmountUnreadMessages(); // Количество непрочитанных уведомлений пользователя
//$errors

// Подсоединяем нужный основной шаблон
require $websiteRoot . "/templates/templ_login.php";

/********************************************************************************
 * Закрываем соединение с БД
 *******************************************************************************/

DBconnect::closeConnectToDB();