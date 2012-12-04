<?php
// Стартуем сессию с пользователем - сделать доступными переменные сессии
session_start();

// Подключаем нужные модели и представления
include 'models/DBconnect.php';
include 'models/GlobFunc.php';
include 'models/Logger.php';
include 'models/IncomingUser.php';
include 'views/View.php';

// Удалось ли подключиться к БД?
if (DBconnect::get() == FALSE) die('Ошибка подключения к базе данных (. Попробуйте зайти к нам немного позже.');

// Инициализируем модель для запросившего страницу пользователя
$incomingUser = new IncomingUser();

/*************************************************************************************
 * ПОЛУЧИМ GET ПАРАМЕТРЫ
 * Для защиты от XSS атаки и для использования в коде более простого имени для переменной
 ************************************************************************************/

// Команда пользователя
$action = "";
if (isset($_GET['action'])) $action = htmlspecialchars($_GET['action'], ENT_QUOTES);

/*************************************************************************************
 * ПРОВЕРКА ПРАВ ДОСТУПА К СТРАНИЦЕ
 ************************************************************************************/

// Если пользователь не авторизирован, то пересылаем юзера на страницу авторизации
if (!$incomingUser->login()) {
	header('Location: login.php');
	exit();
}

// Если пользователь не является администратором, то доступ к странице ему запрещен - разавторизуем его и перекинем на главную (в идеале нужно перекидывать на login.php)
$isAdmin = $incomingUser->isAdmin();
if (!$isAdmin['newOwner'] && !$isAdmin['newAdvertAlien'] && !$isAdmin['searchUser']) {
	header('Location: out.php');
	exit();
}

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
	exit();
}

/********************************************************************************
 * ФОРМИРОВАНИЕ ПРЕДСТАВЛЕНИЯ (View)
 *******************************************************************************/

// Подсоединяем нужный основной шаблон
include "templates/" . "adminTemplates/templ_adminpanel.php";

/********************************************************************************
 * Закрываем соединение с БД
 *******************************************************************************/

DBconnect::closeConnectToDB();