<?php
/* Страница администратора для отображения данных о всем множестве заявок на просмотр, сгруппированных по статусу, либо иному признаку */

// Стартуем сессию с пользователем - сделать доступными переменные сессии
session_start();

// Подключаем нужные модели и представления
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/DBconnect.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/GlobFunc.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/Logger.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/IncomingUser.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/views/View.php';

// Удалось ли подключиться к БД?
if (DBconnect::get() == FALSE) die('Ошибка подключения к базе данных (. Попробуйте зайти к нам немного позже.');

// Инициализируем модель для запросившего страницу пользователя
$incomingUser = new IncomingUser();

// Уточняем - имеет ли пользователь права админа.
$isAdmin = $incomingUser->isAdmin();

/*************************************************************************************
 * Проверяем - может ли данный пользователь просматривать данную страницу
 ************************************************************************************/

// Если пользователь не авторизирован, то пересылаем юзера на страницу авторизации
if (!$incomingUser->login()) {
	header('Location: login.php');
	exit();
}

// Если пользователь не является администратором, то доступ к странице ему запрещен - разавторизуем его и перекинем на главную (в идеале нужно перекидывать на login.php)
// Кроме того, проверяем, что у данного администратора есть право на поиск пользователей и вход в их Личные кабинеты
if (!$isAdmin['searchUser']) {
	header('Location: out.php');
	exit();
}

/*************************************************************************************
 * ПОЛУЧИМ GET ПАРАМЕТРЫ
 * Для защиты от XSS атаки и для использования в коде более простого имени для переменной
 ************************************************************************************/

// Команда админа
$action = "";
if (isset($_GET['action'])) $action = htmlspecialchars($_GET['action'], ENT_QUOTES);

/********************************************************************************
 * ВСЕ ЗАЯВКИ НА ПРОСМОТР СО СТАТУСОМ = $action
 *******************************************************************************/

$allRequestsToView = DBconnect::selectRequestsToViewForStatus($action);

/********************************************************************************
 * ПОЛУЧИМ СВЕДЕНИЯ ОБ АРЕНДАТОРАХ, ПОДАВШИХ НАЙДЕННЫЕ ЗАЯВКИ
 *******************************************************************************/

// Выделим идентификаторы всех арендаторов, отправивших заявки на просмотр
$allTenants = array();
foreach ($allRequestsToView as $value) {
	$allTenants[] = $value['tenantId'];
}

// Получим полные данные по всем этим арендаторам
$allTenants = DBconnect::getAllDataAboutCharacteristicUsers($allTenants);

// Дополним сведения о заявках на просмотр недостающими данными об их отправителях
for ($i = 0, $s = count($allRequestsToView); $i < $s; $i++) {
	foreach ($allTenants as $value) {
		if ($allRequestsToView[$i]['tenantId'] == $value['id']) {
			$allRequestsToView[$i]['name'] = $value['name'];
			$allRequestsToView[$i]['secondName'] = $value['secondName'];
			$allRequestsToView[$i]['surname'] = $value['surname'];
			break;
		}
	}
}

/********************************************************************************
 * ПОЛУЧИМ СВЕДЕНИЯ ОБ ОБЪЕКТАХ, НА ПРОСОМОТР КОТОРЫХ НАЦЕЛЕНЫ НАЙДЕННЫЕ ЗАЯВКИ
 *******************************************************************************/

/********************************************************************************
 * ФОРМИРОВАНИЕ ПРЕДСТАВЛЕНИЯ (View)
 *******************************************************************************/

// Инициализируем используемые в шаблоне(ах) переменные
//$allRequestsToView	массив, каждый элемент которого представляет собой еще один массив параметров конкретной заявки на просмотр
//$action	статус заявок на просмотр, выборку которых производим

// Подсоединяем нужный основной шаблон
require $_SERVER['DOCUMENT_ROOT'] . "/templates/adminTemplates/templ_adminAllRequestsToView.php";

/********************************************************************************
 * Закрываем соединение с БД
 *******************************************************************************/

DBconnect::closeConnectToDB();