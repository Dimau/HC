<?php
// Стартуем сессию с пользователем - сделать доступными переменные сессии
session_start();

// Подключаем нужные модели и представления
include 'models/DBconnect.php';
include 'models/GlobFunc.php';
include 'models/Logger.php';
include 'models/IncomingUser.php';
include 'views/View.php';
include 'models/Property.php';

// Удалось ли подключиться к БД?
if (DBconnect::get() == FALSE) die('Ошибка подключения к базе данных (. Попробуйте зайти к нам немного позже.');

// Инициализируем модель для запросившего страницу пользователя
$incomingUser = new IncomingUser();

// Если пользователь не авторизирован, то пересылаем юзера на страницу авторизации
if (!$incomingUser->login()) {
	header('Location: login.php');
}

/*************************************************************************************
 * Если в строке не указан идентификатор объявления для редактирования, то пересылаем пользователя в личный кабинет
 ************************************************************************************/

if (isset($_GET['propertyId']) && $_GET['propertyId'] != "") {
	$propertyId = $_GET['propertyId']; // Получаем идентификатор объявления для редактирования из строки запроса
} else {
	header('Location: personal.php?tabsId=3'); // Если в запросе не указан идентификатор объявления для редактирования, то пересылаем пользователя в личный кабинет к списку его объявлений
}

/*************************************************************************************
 * Инициализируем объект для работы с параметрами недвижимости
 ************************************************************************************/

$property = new Property($propertyId);
if (!$property->writeCharacteristicFromDB() || !$property->writeFotoInformationFromDB()) {
	die('Ошибка при работе с базой данных (. Попробуйте зайти к нам немного позже.'); // Если получить данные из БД не удалось, то просим пользователя зайти к нам немного позже
}

// Готовим массив со списком районов в городе пользователя
$allDistrictsInCity = GlobFunc::getAllDistrictsInCity("Екатеринбург");

// Инициализируем массив для хранения ошибок проверки данных объекта недвижимости
$errors = array();

/**************************************************************************************************************
 * Проверяем, что пользователь имеет право редактировать данное объявление - он является собственником данного объекта недвижимости или админом
 **************************************************************************************************************/

$isAdmin = $incomingUser->isAdmin();
if ($property->userId != $incomingUser->getId() AND !($isAdmin && $isAdmin['searchUser'])) {
	header('Location: personal.php?tabsId=3');
}

/*************************************************************************************
 * Если пользователь заполнил и отослал форму - проверяем ее
 ************************************************************************************/

if (isset($_GET['action']) && $_GET['action'] == "saveAdvert") {

	$property->writeCharacteristicFromPOST("edit");
	$property->writeFotoInformationFromPOST();

	// Проверяем корректность данных объявления. Функции isAdvertCorrect() возвращает пустой array, если введённые данные верны и array с описанием ошибок в противном случае
	$errors = $property->isAdvertCorrect("editAdvert");

	// Если данные, указанные пользователем, корректны, сохраним данные объявления в базу данных
	if (is_array($errors) && count($errors) == 0) {

		// Сохраняем отредактированные параметры объявления на текущего пользователя
		$correctSaveCharacteristicToDB = $property->saveCharacteristicToDB("edit");

		if ($correctSaveCharacteristicToDB) {

			// Сохраним информацию о фотографиях объекта недвижимости
			$correctSaveFotoInformationToDB = $property->saveFotoInformationToDB();

			if ($correctSaveFotoInformationToDB) {

				// Пересылаем пользователя на страницу с подробным описанием его объявления - хороший способ убедиться в том, что все данные указаны верно
				header('Location: objdescription.php?propertyId=' . $property->id);

			} else {

				$errors[] = 'К сожалению, при сохранении данных о фотографиях произошла ошибка: проверьте, пожалуйста, еще раз корректность Вашей информации и повторите попытку';
				// Сохранении данных о фотках в БД не прошло - сами изменения в объявлении сохранене, но изменения в данных о фотографиях не сохранены.
			}


		} else {

			$errors[] = 'К сожалению, при сохранении данных произошла ошибка: проверьте, пожалуйста, еще раз корректность Вашей информации и повторите попытку';
			// Сохранении данных в БД не прошло - объявление не сохранено
		}

	}
}

/********************************************************************************
 * ФОРМИРОВАНИЕ ПРЕДСТАВЛЕНИЯ (View)
 *******************************************************************************/

// Инициализируем используемые в шаблоне(ах) переменные
$isLoggedIn = $incomingUser->login(); // Используется в templ_header.php
$amountUnreadMessages = $incomingUser->getAmountUnreadMessages(); // Количество непрочитанных сообщений пользователя
$propertyCharacteristic = $property->getCharacteristicData();
$propertyFotoInformation = $property->getFotoInformationData();
$compId = $propertyCharacteristic['userId'];
//$allDistrictsInCity
//$errors

// Подсоединяем нужный основной шаблон
include "templates/" . "templ_editadvert.php";

/********************************************************************************
 * Закрываем соединение с БД
 *******************************************************************************/

DBconnect::closeConnectToDB();

//TODO: В будущем необходимо будет проверять личные данные пользователя на полноту для его работы в качестве собственника, если у него typeOwner != "true"
