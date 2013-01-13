<?php
/**
 * Формируем тестовые записи в БД о пользователях и объявлениях
 * Важно не забывать копировать соответствующие тестовые картинки
 */

// Подключаем нужные модели и представления
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/DBconnect.php';

// Удалось ли подключиться к БД?
if (DBconnect::get() == FALSE) die('Ошибка подключения к базе данных (. Попробуйте зайти к нам немного позже.');

// Функция возвращает "1", если операция над БД была выполнена успешно и FALSE с расшифровкой ошибки, если выполнить ее не удалось
// $typeRes = "1" - выдача результата по отдельной операции с базой данных, крезультат по каждой из которых выводится в отдельную строку
// $typeRes = "2" - выдача результата по набору однотипных операций с БД - в одну строку!
function returnResultMySql($rez) {
	if ($rez == FALSE) {
		echo " <span style='color: red;'>FALSE(" . DBconnect::get()->errno . " " . DBconnect::get()->error . ")</span> ";
	} else {
		echo 1;
	}
	echo "<br>";
}

// Создаем пользователей
DBconnect::get()->query("INSERT INTO users (id, typeTenant, typeOwner, typeAdmin, name, secondName, surname, sex, nationality, birthday, login, password, telephon, emailReg, email, currentStatusEducation, almamater, speciality, kurs, ochnoZaochno, yearOfEnd, statusWork, placeOfWork, workPosition, regionOfBorn, cityOfBorn, shortlyAboutMe, vkontakte, odnoklassniki, facebook, twitter, lic, user_hash, last_act, reg_date, favoritePropertiesId) VALUES
(1, 'FALSE', 'TRUE', NULL, 'Ирина', 'Леонидовна', 'Пупкина', 'женский', 'европейская', '1992-08-16', 'owner', 'owner', '9831541618', '', '', '0', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', 'yes', '84fae0ea32a834378531e9b0e7c63e11', 1348563523, 1348563523, 0x613a303a7b7d),
(2, 'TRUE', 'FALSE', NULL, 'Андрей', 'Евстигнеевич', 'Комаров', 'мужской', 'славянская', '1987-01-27', 'tenant', 'tenant', '9221431615', 'dimau777@gmail.com', 'dimau777@gmail.com', 'закончил', 'УГТУ-УПИ', 'Менеджмент в спорте', '', 'очно', '2011', 'работаю', 'Банк ПромИмпериал', 'менеджер проектов', 'Свердловская область', 'Екатеринбург', 'Немного люблю спорт, бегаю по утрам.', 'http://vk.com/maxim', '', '', '', 'yes', '9c9cdf2a467b90d5d5f8135af3543882', 1348590806, 1348590806, 0x613a303a7b7d),
(3, 'FALSE', 'FALSE', '111111', 'Дмитрий', 'Владимирович', 'Ушаков', 'мужской', 'славянская', '1987-01-27', '9221431615', 'stru4OK', '9221431615', 'dimau777@gmail.com', 'dimau777@gmail.com', 'закончил', 'УГТУ-УПИ', 'Управление и информатика в технических системах', '', '', '2009', 'работаю', 'СКБ Контур', 'менеджер проектов', 'Пермский край', 'Лысьва', 'Немного люблю спорт, бегаю по утрам.', '', '', '', '', 'yes', '9c9cdf2a467b90d5d5f8135af3543882', 1348590806, 1348590806, 0x613a303a7b7d)
");

echo "Статус регистрации пользователей: ";
if (DBconnect::get()->errno) returnResultMySql(FALSE); else returnResultMySql(TRUE);

// Заполняем таблицу о поисковых запросах пользователей
DBconnect::get()->query("INSERT INTO searchRequests (userId, typeOfObject, amountOfRooms, adjacentRooms, floor, minCost, maxCost, pledge, prepayment, district, withWho, linksToFriends, children, howManyChildren, animals, howManyAnimals, termOfLease, additionalDescriptionOfSearch, regDate, needEmail, needSMS) VALUES
(2, 'квартира', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}', 'не имеет значения', 'не первый и не последний', 11000, 20000, 15000, 'нет', 'a:3:{i:0;s:24:\"Ботанический\";i:1;s:6:\"ВИЗ\";i:2;s:17:\"Юго-запад\";}', 'пара', 'Анна Дрей', 'без детей', '', 'без животных', '', 'длительный срок', 'Рядом с парком, чтобы бегать утром можно было на свежем воздухе.', 1348563523, 1, 0)
");

echo "Статус регистрации поисковых запросов пользователей: ";
if (DBconnect::get()->errno) returnResultMySql(FALSE); else returnResultMySql(TRUE);

// Регистрируем данные о фотках пользователей
DBconnect::get()->query("INSERT INTO userFotos (id, folder, filename, extension, filesizeMb, userId, status, regDate) VALUES
('1aa240c376365dd8c4a38493a8dcff64', 'uploaded_files/1', 'DSCN0319.JPG', 'jpeg', 6.7, 2, 'основная', 1348563523),
('8e26607a95ae215bd4831d2b9b0be1b0', 'uploaded_files/8', 'Ольга Андреева.jpg', 'jpeg', 0.1, 1, 'основная', 1348563523)
");

echo "Статус регистрации фотографий пользователей: ";
if (DBconnect::get()->errno) returnResultMySql(FALSE); else returnResultMySql(TRUE);

// Создаем несколько объектов недвижимости
DBconnect::get()->query("INSERT INTO property (id, userId, typeOfObject, dateOfEntry, termOfLease, dateOfCheckOut, amountOfRooms, adjacentRooms, amountOfAdjacentRooms, typeOfBathrooms, typeOfBalcony, balconyGlazed, roomSpace, totalArea, livingSpace, kitchenSpace, floor, totalAmountFloor, numberOfFloor, concierge, intercom, parking, city, district, coordX, coordY, address, apartmentNumber, subwayStation, distanceToMetroStation, currency, costOfRenting, realCostOfRenting, utilities, costInSummer, costInWinter, electricPower, bail, bailCost, prepayment, compensationMoney, compensationPercent, repair, furnish, windows, internet, telephoneLine, cableTV, furnitureInLivingArea, furnitureInLivingAreaExtra, furnitureInKitchen, furnitureInKitchenExtra, appliances, appliancesExtra, sexOfTenant, relations, children, animals, contactTelephonNumber, timeForRingBegin, timeForRingEnd, checking, comment, last_act, reg_date, status, earliestDate, earliestTimeHours, earliestTimeMinutes, adminComment, completeness) VALUES
(1, 1, 'квартира', '2012-09-25', 'длительный срок', '0000-00-00', '1', 'да', '4', 'раздельный', 'нет', 'да', 0, 68.5, 56, 12, 3, 4, 0, 'есть', 'нет', 'охраняемая', 'Екатеринбург', 'Юго-запад', '56.825483', '60.57357', 'улица Гурзуфская, 38', '64', 'нет', 0, 'руб.', 23000, 23000, 'да', 1590, 6450, 'нет', 'есть', 4600, '1 месяц', 2760, 12, 'меньше 1 года назад', 'евростандарт', 'деревянные', 'проведен', 'не проведен', 'проведено', 0x613a343a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a33373a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b2d183d185d0bcd0b5d181d182d0bdd0b0d18f223b693a323b733a33333a22d0bad180d0b5d181d0bbd0be20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b5223b693a333b733a31303a22d0bad0bed0bcd0bed0b4223b7d, 'пуффик', 0x613a303a7b7d, 'чморик-конторик, стеллаж под инструменты,дуван', 0x613a303a7b7d, 'пипикалка, мумукалка', 'a:0:{}', 'a:0:{}', 'не имеет значения', 'только без животных', '9221431645', '8:00', '16:00', 'отдельно', '', 1348684721, 1348684721, 'опубликовано', '', '', '', '', '1'),
(2, 1, 'квартира', '2012-09-26', 'несколько месяцев', '2012-12-31', '5', 'да', '3', '2 шт.', 'балкон и лоджия', 'нет', 0, 125.8, 67.89, 15, 4, 40, 0, 'нет', 'есть', 'подземная', 'Екатеринбург', 'Академический', '56.791911', '60.521028', 'улица Вильгельма Де Геннина, 31', '23', 'Площадь 1905 г.', 234, 'руб.', 36500, 36500, 'да', 2300, 3600, 'да', 'есть', 56000, '2 месяца', 10950, 30, 'не выполнялся (новый дом)', 'свежая (новые обои, побелка потолков)', 'пластиковые', 'проведен', 'не проведен', 'не проведено', 0x613a333a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a32393a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b5d182d181d0bad0b0d18f223b693a323b733a31303a22d0bad0bed0bcd0bed0b4223b7d, 'Стол компьютерный, пистолет', 0x613a323a7b693a303b733a32373a22d181d182d0bed0bb20d0bed0b1d0b5d0b4d0b5d0bdd0bdd18bd0b9223b693a313b733a33323a22d181d182d183d0bbd18cd18f2c20d182d0b0d0b1d183d180d0b5d182d0bad0b8223b7d, 'Портмоне', 0x613a323a7b693a303b733a32323a22d185d0bed0bbd0bed0b4d0b8d0bbd18cd0bdd0b8d0ba223b693a313b733a31383a22d182d0b5d0bbd0b5d0b2d0b8d0b7d0bed180223b7d, '', 'a:0:{}', 'a:0:{}', 'только без детей', 'только без животных', '9221431645', '9:00', '13:00', 'отдельно', '', 1348683429, 1348683429, 'опубликовано', '', '', '', '', '1'),
(3, 1, 'квартира', '2012-09-25', 'несколько месяцев', '2012-12-31', '1', 'да', '4', '3 шт.', 'лоджия', 'да', 0, 68.5, 56, 12, 1, 3, 0, 'нет', 'нет', 'неохраняемая', 'Екатеринбург', 'Шарташ', '56.819927', '60.539264', 'улица Репина, 105', '124', 'Машиностроителей', 12, 'руб.', 18000, 18000, 'да', 1500, 2500, 'да', 'есть', 5000, '2 месяца', 0, 0, 'меньше 1 года назад', 'евростандарт', 'деревянные', 'проведен', 'не проведен', 'проведено', 0x613a343a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a33373a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b2d183d185d0bcd0b5d181d182d0bdd0b0d18f223b693a323b733a33333a22d0bad180d0b5d181d0bbd0be20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b5223b693a333b733a31303a22d0bad0bed0bcd0bed0b4223b7d, 'пуффик', 0x613a303a7b7d, 'чморик-конторик, стеллаж под инструменты,дуван', 0x613a303a7b7d, 'пипикалка, мумукалка', 'a:0:{}', 'a:0:{}', 'не имеет значения', 'только без животных', '9221431645', '8:00', '16:00', 'отдельно', '', 1348564004, 1348564004, 'опубликовано', '', '', '', '', '1'),
(4, 1, 'дом', '2012-09-26', 'несколько месяцев', '2012-12-31', '2', 'да', '0', 'совмещенный', '2 эркера и более', '0', 0, 125.8, 67.89, 15, 0, 0, 2, '0', 'нет', 'неохраняемая', 'Екатеринбург', 'ВИЗ', '56.824951', '60.549792', 'улица Репина, 75', '', 'нет', 0, 'дол. США', 2000, 60000, 'да', 120, 230, 'нет', 'нет', 5600, '6 месяцев', 200, 10, 'выполнялся давно', 'требует обновления', 'деревянные', 'не проведен', 'проведен', 'проведено', 0x613a333a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a32393a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b5d182d181d0bad0b0d18f223b693a323b733a31303a22d0bad0bed0bcd0bed0b4223b7d, '', 0x613a323a7b693a303b733a32373a22d181d182d0bed0bb20d0bed0b1d0b5d0b4d0b5d0bdd0bdd18bd0b9223b693a313b733a33323a22d181d182d183d0bbd18cd18f2c20d182d0b0d0b1d183d180d0b5d182d0bad0b8223b7d, '', 0x613a323a7b693a303b733a32323a22d185d0bed0bbd0bed0b4d0b8d0bbd18cd0bdd0b8d0ba223b693a313b733a31383a22d182d0b5d0bbd0b5d0b2d0b8d0b7d0bed180223b7d, '', 'a:0:{}', 'a:0:{}', 'не имеет значения', 'не имеет значения', '9221431645', '6:00', '23:00', 'в другом городе', '', 1348683411, 1348683411, 'опубликовано', '', '', '', 'Несколько раз перезванивать - с первого никогда не получается', '1'),
(5, 1, 'гараж', '2012-09-25', 'несколько месяцев', '2012-12-12', '0', '0', '0', '0', '0', '0', 0, 68.5, 0, 0, 0, 0, 0, '0', '0', '0', 'Екатеринбург', 'Автовокзал (южный)', '56.817089', '60.577199', 'улица Ясная, 16', '', '0', 0, 'руб.', 4600, 4600, 'нет', 1500, 2500, 'да', 'нет', 5000, '1 месяц', 1104, 24, '0', '0', '0', '0', '0', '0', 0x613a343a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a33373a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b2d183d185d0bcd0b5d181d182d0bdd0b0d18f223b693a323b733a33333a22d0bad180d0b5d181d0bbd0be20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b5223b693a333b733a31303a22d0bad0bed0bcd0bed0b4223b7d, '', 0x613a303a7b7d, '', 0x613a303a7b7d, '', 'a:0:{}', 'a:0:{}', '0', '0', '9221431645', '8:00', '16:00', 'отдельно', '', 1348683874, 1348683874, 'опубликовано', '', '', '', '', '1'),
(6, 1, 'дача', '2012-09-26', 'длительный срок', '0000-00-00', '2', 'нет', '0', 'раздельный', 'эркер', '0', 0, 23.57, 12.8, 0, 0, 0, 4, '0', '0', '0', 'Екатеринбург', 'Уралмаш', '56.848409', '60.601786', 'улица Мельковская, 3б', '', '0', 0, 'руб.', 12000, 12000, 'нет', 2300, 3600, 'да', 'нет', 56000, 'нет', 1200, 10, 'сделан только что', 'евростандарт', 'иное', 'не проведен', 'не проведен', 'не проведено', 0x613a333a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a32393a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b5d182d181d0bad0b0d18f223b693a323b733a31303a22d0bad0bed0bcd0bed0b4223b7d, 'cтол компьютерный, пистолет', 0x613a323a7b693a303b733a32373a22d181d182d0bed0bb20d0bed0b1d0b5d0b4d0b5d0bdd0bdd18bd0b9223b693a313b733a33323a22d181d182d183d0bbd18cd18f2c20d182d0b0d0b1d183d180d0b5d182d0bad0b8223b7d, 'портмоне', 0x613a323a7b693a303b733a32323a22d185d0bed0bbd0bed0b4d0b8d0bbd18cd0bdd0b8d0ba223b693a313b733a31383a22d182d0b5d0bbd0b5d0b2d0b8d0b7d0bed180223b7d, '', 'a:0:{}', 'a:0:{}', 'с детьми старше 4-х лет', 'не имеет значения', '9221431645', '9:00', '13:00', 'рядом (в качестве соседа)', '', 1348679739, 1348679739, 'опубликовано', '', '', '', '', '1'),
(7, 1, 'таунхаус', '2012-09-25', 'длительный срок', '0000-00-00', '6', 'да', '3', 'совмещенный', '2 лоджии и более', 'нет', 0, 68.5, 56, 12, 0, 0, 2, '0', 'нет', 'отсутствует', 'Екатеринбург', 'Юго-запад', '56.849817', '60.620489', 'улица Шевченко, 29', '2', 'Ботаническая', 23, 'руб.', 18000, 18000, 'да', 1500, 2500, 'да', 'есть', 5000, '2 месяца', 0, 0, 'больше года назад', 'евростандарт', 'деревянные', 'проведен', 'не проведен', 'проведено', 0x613a343a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a33373a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b2d183d185d0bcd0b5d181d182d0bdd0b0d18f223b693a323b733a33333a22d0bad180d0b5d181d0bbd0be20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b5223b693a333b733a31303a22d0bad0bed0bcd0bed0b4223b7d, 'пуффик', 0x613a303a7b7d, '', 0x613a313a7b693a303b733a34313a22d0bed185d180d0b0d0bdd0bdd0b0d18f20d181d0b8d0b3d0bdd0b0d0bbd0b8d0b7d0b0d186d0b8d18f223b7d, '', 'a:0:{}', 'a:0:{}', 'не имеет значения', 'не имеет значения', '9221431645', '8:00', '16:00', 'в другом городе', '', 1348683998, 1348683998, 'опубликовано', '', '', '', '', '1'),
(8, 1, 'комната', '2012-09-26', 'длительный срок', '0000-00-00', '2', 'да', '0', 'раздельный', 'нет', '0', 12.3, 0, 0, 15, 40, 40, 0, 'есть', 'есть', 'отсутствует', 'Екатеринбург', 'Эльмаш', '56.849905', '60.621433', 'улица Шевченко, 31', '25', 'Уралмаш', 10, 'евро', 120, 4800, 'нет', 0, 0, 'нет', 'есть', 250, '3 месяца', 30, 25, 'сделан только что', 'бабушкин вариант', 'деревянные', 'проведен', 'не проведен', 'не проведено', 0x613a333a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a32393a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b5d182d181d0bad0b0d18f223b693a323b733a31303a22d0bad0bed0bcd0bed0b4223b7d, '', 0x613a323a7b693a303b733a32373a22d181d182d0bed0bb20d0bed0b1d0b5d0b4d0b5d0bdd0bdd18bd0b9223b693a313b733a33323a22d181d182d183d0bbd18cd18f2c20d182d0b0d0b1d183d180d0b5d182d0bad0b8223b7d, '', 0x613a323a7b693a303b733a32323a22d185d0bed0bbd0bed0b4d0b8d0bbd18cd0bdd0b8d0ba223b693a313b733a31383a22d182d0b5d0bbd0b5d0b2d0b8d0b7d0bed180223b7d, '', 'a:0:{}', 'a:0:{}', 'с детьми старше 4-х лет', 'не имеет значения', '9221431645', '10:00', '20:00', 'отдельно', '', 1348686239, 1348686239, 'опубликовано', '', '', '', '', '1'),
(9, 1, 'квартира', '2012-09-25', 'длительный срок', '0000-00-00', '1', 'да', '4', 'раздельный', 'нет', 'да', 0, 68.5, 56, 12, 3, 4, 0, 'есть', 'нет', 'охраняемая', 'Екатеринбург', 'Юго-запад', '56.845343', '60.56467', 'улица Пензенская, 132', '64', 'нет', 0, 'руб.', 23000, 23000, 'да', 1590, 6450, 'нет', 'есть', 4600, '1 месяц', 2760, 12, 'меньше 1 года назад', 'евростандарт', 'деревянные', 'проведен', 'не проведен', 'проведено', 0x613a343a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a33373a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b2d183d185d0bcd0b5d181d182d0bdd0b0d18f223b693a323b733a33333a22d0bad180d0b5d181d0bbd0be20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b5223b693a333b733a31303a22d0bad0bed0bcd0bed0b4223b7d, 'пуффик', 0x613a303a7b7d, 'чморик-конторик, стеллаж под инструменты,дуван', 0x613a303a7b7d, 'пипикалка, мумукалка', 'a:0:{}', 'a:0:{}', 'не имеет значения', 'только без животных', '9221431645', '8:00', '16:00', 'отдельно', '', 1348684721, 1348684721, 'опубликовано', '', '', '', '', '1'),
(10, 1, 'квартира', '2012-09-26', 'несколько месяцев', '2012-12-31', '5', 'да', '3', '2 шт.', 'балкон и лоджия', 'нет', 0, 125.8, 67.89, 15, 4, 40, 0, 'нет', 'есть', 'подземная', 'Екатеринбург', 'Академический', '56.781918', '60.541628', 'улица Пушкина, 34', '23', 'Площадь 1905 г.', 234, 'руб.', 36500, 36500, 'да', 2300, 3600, 'да', 'есть', 56000, '2 месяца', 10950, 30, 'не выполнялся (новый дом)', 'свежая (новые обои, побелка потолков)', 'пластиковые', 'проведен', 'не проведен', 'не проведено', 0x613a333a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a32393a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b5d182d181d0bad0b0d18f223b693a323b733a31303a22d0bad0bed0bcd0bed0b4223b7d, 'Стол компьютерный, пистолет', 0x613a323a7b693a303b733a32373a22d181d182d0bed0bb20d0bed0b1d0b5d0b4d0b5d0bdd0bdd18bd0b9223b693a313b733a33323a22d181d182d183d0bbd18cd18f2c20d182d0b0d0b1d183d180d0b5d182d0bad0b8223b7d, 'Портмоне', 0x613a323a7b693a303b733a32323a22d185d0bed0bbd0bed0b4d0b8d0bbd18cd0bdd0b8d0ba223b693a313b733a31383a22d182d0b5d0bbd0b5d0b2d0b8d0b7d0bed180223b7d, '', 'a:0:{}', 'a:0:{}', 'только без детей', 'только без животных', '9221431645', '9:00', '13:00', 'отдельно', '', 1348683429, 1348683429, 'опубликовано', '', '', '', '', '1'),
(11, 1, 'квартира', '2012-09-25', 'несколько месяцев', '2012-12-31', '1', 'да', '4', '3 шт.', 'лоджия', 'да', 0, 68.5, 56, 12, 1, 3, 0, 'нет', 'нет', 'неохраняемая', 'Екатеринбург', 'Шарташ', '56.829727', '60.559764', 'улица Репина, 18', '124', 'Машиностроителей', 12, 'руб.', 18000, 18000, 'да', 1500, 2500, 'да', 'есть', 5000, '2 месяца', 0, 0, 'меньше 1 года назад', 'евростандарт', 'деревянные', 'проведен', 'не проведен', 'проведено', 0x613a343a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a33373a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b2d183d185d0bcd0b5d181d182d0bdd0b0d18f223b693a323b733a33333a22d0bad180d0b5d181d0bbd0be20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b5223b693a333b733a31303a22d0bad0bed0bcd0bed0b4223b7d, 'пуффик', 0x613a303a7b7d, 'чморик-конторик, стеллаж под инструменты,дуван', 0x613a303a7b7d, 'пипикалка, мумукалка', 'a:0:{}', 'a:0:{}', 'не имеет значения', 'только без животных', '9221431645', '8:00', '16:00', 'отдельно', '', 1348564004, 1348564004, 'опубликовано', '', '', '', '', '1'),
(12, 1, 'дом', '2012-09-26', 'несколько месяцев', '2012-12-31', '2', 'да', '0', 'совмещенный', '2 эркера и более', '0', 0, 125.8, 67.89, 15, 0, 0, 2, '0', 'нет', 'неохраняемая', 'Екатеринбург', 'ВИЗ', '56.834921', '60.559742', 'улица Мамина-Сибиряка, 75', '', 'нет', 0, 'дол. США', 2000, 60000, 'да', 120, 230, 'нет', 'нет', 5600, '6 месяцев', 200, 10, 'выполнялся давно', 'требует обновления', 'деревянные', 'не проведен', 'проведен', 'проведено', 0x613a333a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a32393a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b5d182d181d0bad0b0d18f223b693a323b733a31303a22d0bad0bed0bcd0bed0b4223b7d, '', 0x613a323a7b693a303b733a32373a22d181d182d0bed0bb20d0bed0b1d0b5d0b4d0b5d0bdd0bdd18bd0b9223b693a313b733a33323a22d181d182d183d0bbd18cd18f2c20d182d0b0d0b1d183d180d0b5d182d0bad0b8223b7d, '', 0x613a323a7b693a303b733a32323a22d185d0bed0bbd0bed0b4d0b8d0bbd18cd0bdd0b8d0ba223b693a313b733a31383a22d182d0b5d0bbd0b5d0b2d0b8d0b7d0bed180223b7d, '', 'a:0:{}', 'a:0:{}', 'не имеет значения', 'не имеет значения', '9221431645', '6:00', '23:00', 'в другом городе', '', 1348683411, 1348683411, 'опубликовано', '', '', '', 'Что-то долго не отвечает собственник', '1'),
(13, 1, 'гараж', '2012-09-25', 'несколько месяцев', '2012-12-12', '0', '0', '0', '0', '0', '0', 0, 68.5, 0, 0, 0, 0, 0, '0', '0', '0', 'Екатеринбург', 'Автовокзал (южный)', '56.807243', '60.587456', 'улица Неясная, 7', '', '0', 0, 'руб.', 4600, 4600, 'нет', 1500, 2500, 'да', 'нет', 5000, '1 месяц', 1104, 24, '0', '0', '0', '0', '0', '0', 0x613a343a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a33373a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b2d183d185d0bcd0b5d181d182d0bdd0b0d18f223b693a323b733a33333a22d0bad180d0b5d181d0bbd0be20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b5223b693a333b733a31303a22d0bad0bed0bcd0bed0b4223b7d, '', 0x613a303a7b7d, '', 0x613a303a7b7d, '', 'a:0:{}', 'a:0:{}', '0', '0', '9221431645', '8:00', '16:00', 'отдельно', '', 1348683874, 1348683874, 'опубликовано', '', '', '', '', '1'),
(14, 1, 'дача', '2012-09-26', 'длительный срок', '0000-00-00', '2', 'нет', '0', 'раздельный', 'эркер', '0', 0, 23.57, 12.8, 0, 0, 0, 4, '0', '0', '0', 'Екатеринбург', 'Уралмаш', '56.828429', '60.611457', 'улица Маньяковская, 56', '', '0', 0, 'руб.', 12000, 12000, 'нет', 2300, 3600, 'да', 'нет', 56000, 'нет', 1200, 10, 'сделан только что', 'евростандарт', 'иное', 'не проведен', 'не проведен', 'не проведено', 0x613a333a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a32393a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b5d182d181d0bad0b0d18f223b693a323b733a31303a22d0bad0bed0bcd0bed0b4223b7d, 'cтол компьютерный, пистолет', 0x613a323a7b693a303b733a32373a22d181d182d0bed0bb20d0bed0b1d0b5d0b4d0b5d0bdd0bdd18bd0b9223b693a313b733a33323a22d181d182d183d0bbd18cd18f2c20d182d0b0d0b1d183d180d0b5d182d0bad0b8223b7d, 'портмоне', 0x613a323a7b693a303b733a32323a22d185d0bed0bbd0bed0b4d0b8d0bbd18cd0bdd0b8d0ba223b693a313b733a31383a22d182d0b5d0bbd0b5d0b2d0b8d0b7d0bed180223b7d, '', 'a:0:{}', 'a:0:{}', 'с детьми старше 4-х лет', 'не имеет значения', '9221431645', '9:00', '13:00', 'рядом (в качестве соседа)', '', 1348679739, 1348679739, 'опубликовано', '', '', '', '', '1'),
(15, 1, 'таунхаус', '2012-09-25', 'длительный срок', '0000-00-00', '6', 'да', '3', 'совмещенный', '2 лоджии и более', 'нет', 0, 68.5, 56, 12, 0, 0, 2, '0', 'нет', 'отсутствует', 'Екатеринбург', 'Юго-запад', '56.859568', '60.610786', 'улица Тьмутараканская, 12', '2', 'Ботаническая', 23, 'руб.', 18000, 18000, 'да', 1500, 2500, 'да', 'есть', 5000, '2 месяца', 0, 0, 'больше года назад', 'евростандарт', 'деревянные', 'проведен', 'не проведен', 'проведено', 0x613a343a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a33373a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b2d183d185d0bcd0b5d181d182d0bdd0b0d18f223b693a323b733a33333a22d0bad180d0b5d181d0bbd0be20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b5223b693a333b733a31303a22d0bad0bed0bcd0bed0b4223b7d, 'пуффик', 0x613a303a7b7d, '', 0x613a313a7b693a303b733a34313a22d0bed185d180d0b0d0bdd0bdd0b0d18f20d181d0b8d0b3d0bdd0b0d0bbd0b8d0b7d0b0d186d0b8d18f223b7d, '', 'a:0:{}', 'a:0:{}', 'не имеет значения', 'не имеет значения', '9221431645', '8:00', '16:00', 'в другом городе', '', 1348683998, 1348683998, 'опубликовано', '', '', '', '', '1'),
(16, 1, 'комната', '2012-09-26', 'длительный срок', '0000-00-00', '2', 'да', '0', 'раздельный', 'нет', '0', 12.3, 0, 0, 15, 40, 40, 0, 'есть', 'есть', 'отсутствует', 'Екатеринбург', 'Эльмаш', '56.849705', '60.621833', 'улица Гоголя, 31', '25', 'Уралмаш', 10, 'евро', 120, 4800, 'нет', 0, 0, 'нет', 'есть', 250, '3 месяца', 30, 25, 'сделан только что', 'бабушкин вариант', 'деревянные', 'проведен', 'не проведен', 'не проведено', 0x613a333a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a32393a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b5d182d181d0bad0b0d18f223b693a323b733a31303a22d0bad0bed0bcd0bed0b4223b7d, '', 0x613a323a7b693a303b733a32373a22d181d182d0bed0bb20d0bed0b1d0b5d0b4d0b5d0bdd0bdd18bd0b9223b693a313b733a33323a22d181d182d183d0bbd18cd18f2c20d182d0b0d0b1d183d180d0b5d182d0bad0b8223b7d, '', 0x613a323a7b693a303b733a32323a22d185d0bed0bbd0bed0b4d0b8d0bbd18cd0bdd0b8d0ba223b693a313b733a31383a22d182d0b5d0bbd0b5d0b2d0b8d0b7d0bed180223b7d, '', 'a:0:{}', 'a:0:{}', 'с детьми старше 4-х лет', 'не имеет значения', '9221431645', '10:00', '20:00', 'отдельно', '', 1348686239, 1348686239, 'опубликовано', '', '', '', '', '1'),
(17, 1, 'квартира', '2012-09-26', 'несколько месяцев', '2012-12-31', '5', 'да', '3', '2 шт.', 'балкон и лоджия', 'нет', 0, 125.8, 67.89, 15, 4, 40, 0, 'нет', 'есть', 'подземная', 'Екатеринбург', 'Академический', '56.795656', '60.524567', 'улица Оконная, 9', '23', 'Площадь 1905 г.', 234, 'руб.', 36500, 36500, 'да', 2300, 3600, 'да', 'есть', 56000, '2 месяца', 10950, 30, 'не выполнялся (новый дом)', 'свежая (новые обои, побелка потолков)', 'пластиковые', 'проведен', 'не проведен', 'не проведено', 0x613a333a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a32393a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b5d182d181d0bad0b0d18f223b693a323b733a31303a22d0bad0bed0bcd0bed0b4223b7d, 'Стол компьютерный, пистолет', 0x613a323a7b693a303b733a32373a22d181d182d0bed0bb20d0bed0b1d0b5d0b4d0b5d0bdd0bdd18bd0b9223b693a313b733a33323a22d181d182d183d0bbd18cd18f2c20d182d0b0d0b1d183d180d0b5d182d0bad0b8223b7d, 'Портмоне', 0x613a323a7b693a303b733a32323a22d185d0bed0bbd0bed0b4d0b8d0bbd18cd0bdd0b8d0ba223b693a313b733a31383a22d182d0b5d0bbd0b5d0b2d0b8d0b7d0bed180223b7d, '', 'a:0:{}', 'a:0:{}', 'только без детей', 'только без животных', '9221431645', '9:00', '13:00', 'отдельно', 'Все решаю сам', '', 1348683429, 1348683429, 'опубликовано', '', '', '', '', '1'),
(18, 1, 'квартира', '2012-09-25', 'несколько месяцев', '2012-12-31', '1', 'да', '4', '3 шт.', 'лоджия', 'да', 0, 68.5, 56, 12, 1, 3, 0, 'нет', 'нет', 'неохраняемая', 'Екатеринбург', 'Шарташ', '56.811227', '60.531264', 'улица Дверная, 58', '124', 'Машиностроителей', 12, 'руб.', 18000, 18000, 'да', 1500, 2500, 'да', 'есть', 5000, '2 месяца', 0, 0, 'меньше 1 года назад', 'евростандарт', 'деревянные', 'проведен', 'не проведен', 'проведено', 0x613a343a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a33373a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b2d183d185d0bcd0b5d181d182d0bdd0b0d18f223b693a323b733a33333a22d0bad180d0b5d181d0bbd0be20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b5223b693a333b733a31303a22d0bad0bed0bcd0bed0b4223b7d, 'пуффик', 0x613a303a7b7d, 'чморик-конторик, стеллаж под инструменты,дуван', 0x613a303a7b7d, 'пипикалка, мумукалка', 'a:0:{}', 'a:0:{}', 'не имеет значения', 'только без животных', '9221431645', '8:00', '16:00', 'отдельно', '', 1348564004, 1348564004, 'опубликовано', '', '', '', '', '1'),
(19, 1, 'дом', '2012-09-26', 'несколько месяцев', '2012-12-31', '2', 'да', '0', 'совмещенный', '2 эркера и более', '0', 0, 125.8, 67.89, 15, 0, 0, 2, '0', 'нет', 'неохраняемая', 'Екатеринбург', 'ВИЗ', '56.829921', '60.541342', 'улица Хорошая, 14', '', 'нет', 0, 'дол. США', 2000, 60000, 'да', 120, 230, 'нет', 'нет', 5600, '6 месяцев', 200, 10, 'выполнялся давно', 'требует обновления', 'деревянные', 'не проведен', 'проведен', 'проведено', 0x613a333a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a32393a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b5d182d181d0bad0b0d18f223b693a323b733a31303a22d0bad0bed0bcd0bed0b4223b7d, '', 0x613a323a7b693a303b733a32373a22d181d182d0bed0bb20d0bed0b1d0b5d0b4d0b5d0bdd0bdd18bd0b9223b693a313b733a33323a22d181d182d183d0bbd18cd18f2c20d182d0b0d0b1d183d180d0b5d182d0bad0b8223b7d, '', 0x613a323a7b693a303b733a32323a22d185d0bed0bbd0bed0b4d0b8d0bbd18cd0bdd0b8d0ba223b693a313b733a31383a22d182d0b5d0bbd0b5d0b2d0b8d0b7d0bed180223b7d, '', 'a:0:{}', 'a:0:{}', 'не имеет значения', 'не имеет значения', '9221431645', '6:00', '23:00', 'в другом городе', '', 1348683411, 1348683411, 'опубликовано', '', '', '', 'Иван Иванович, звонить после 18 8934627653', '1'),
(20, 1, 'гараж', '2012-09-25', 'несколько месяцев', '2012-12-12', '0', '0', '0', '0', '0', '0', 0, 68.5, 0, 0, 0, 0, 0, '0', '0', '0', 'Екатеринбург', 'Автовокзал (южный)', '56.813443', '60.570056', 'улица Отрадненькая, 67', '', '0', 0, 'руб.', 4600, 4600, 'нет', 1500, 2500, 'да', 'нет', 5000, '1 месяц', 1104, 24, '0', '0', '0', '0', '0', '0', 0x613a343a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a33373a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b2d183d185d0bcd0b5d181d182d0bdd0b0d18f223b693a323b733a33333a22d0bad180d0b5d181d0bbd0be20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b5223b693a333b733a31303a22d0bad0bed0bcd0bed0b4223b7d, '', 0x613a303a7b7d, '', 0x613a303a7b7d, '', 'a:0:{}', 'a:0:{}', '0', '0', '9221431645', '8:00', '16:00', 'отдельно', '', 1348683874, 1348683874, 'опубликовано', '', '', '', '', '1'),
(21, 1, 'дача', '2012-09-26', 'длительный срок', '0000-00-00', '2', 'нет', '0', 'раздельный', 'эркер', '0', 0, 23.57, 12.8, 0, 0, 0, 4, '0', '0', '0', 'Екатеринбург', 'Уралмаш', '56.843429', '60.607657', 'улица Шмонькина, 2', '', '0', 0, 'руб.', 12000, 12000, 'нет', 2300, 3600, 'да', 'нет', 56000, 'нет', 1200, 10, 'сделан только что', 'евростандарт', 'иное', 'не проведен', 'не проведен', 'не проведено', 0x613a333a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a32393a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b5d182d181d0bad0b0d18f223b693a323b733a31303a22d0bad0bed0bcd0bed0b4223b7d, 'cтол компьютерный, пистолет', 0x613a323a7b693a303b733a32373a22d181d182d0bed0bb20d0bed0b1d0b5d0b4d0b5d0bdd0bdd18bd0b9223b693a313b733a33323a22d181d182d183d0bbd18cd18f2c20d182d0b0d0b1d183d180d0b5d182d0bad0b8223b7d, 'портмоне', 0x613a323a7b693a303b733a32323a22d185d0bed0bbd0bed0b4d0b8d0bbd18cd0bdd0b8d0ba223b693a313b733a31383a22d182d0b5d0bbd0b5d0b2d0b8d0b7d0bed180223b7d, '', 'a:0:{}', 'a:0:{}', 'с детьми старше 4-х лет', 'не имеет значения', '9221431645', '9:00', '13:00', 'рядом (в качестве соседа)', '', 1348679739, 1348679739, 'опубликовано', '', '', '', '', '1'),
(22, 1, 'таунхаус', '2012-09-25', 'длительный срок', '0000-00-00', '6', 'да', '3', 'совмещенный', '2 лоджии и более', 'нет', 0, 68.5, 56, 12, 0, 0, 2, '0', 'нет', 'отсутствует', 'Екатеринбург', 'Юго-запад', '56.842368', '60.628786', 'улица Больничная, 19', '2', 'Ботаническая', 23, 'руб.', 18000, 18000, 'да', 1500, 2500, 'да', 'есть', 5000, '2 месяца', 0, 0, 'больше года назад', 'евростандарт', 'деревянные', 'проведен', 'не проведен', 'проведено', 0x613a343a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a33373a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b2d183d185d0bcd0b5d181d182d0bdd0b0d18f223b693a323b733a33333a22d0bad180d0b5d181d0bbd0be20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b5223b693a333b733a31303a22d0bad0bed0bcd0bed0b4223b7d, 'пуффик', 0x613a303a7b7d, '', 0x613a313a7b693a303b733a34313a22d0bed185d180d0b0d0bdd0bdd0b0d18f20d181d0b8d0b3d0bdd0b0d0bbd0b8d0b7d0b0d186d0b8d18f223b7d, '', 'a:0:{}', 'a:0:{}', 'не имеет значения', 'не имеет значения', '9221431645', '8:00', '16:00', 'в другом городе', '', 1348683998, 1348683998, 'опубликовано', '', '', '', '', '1'),
(23, 1, 'комната', '2012-09-26', 'длительный срок', '0000-00-00', '2', 'да', '0', 'раздельный', 'нет', '0', 12.3, 0, 0, 15, 40, 40, 0, 'есть', 'есть', 'отсутствует', 'Екатеринбург', 'Эльмаш', '56.859705', '60.611833', 'улица Любимая, 44', '25', 'Уралмаш', 10, 'евро', 120, 4800, 'нет', 0, 0, 'нет', 'есть', 250, '3 месяца', 30, 25, 'сделан только что', 'бабушкин вариант', 'деревянные', 'проведен', 'не проведен', 'не проведено', 0x613a333a7b693a303b733a33313a22d0b4d0b8d0b2d0b0d0bd20d180d0b0d181d0bad0bbd0b0d0b4d0bdd0bed0b9223b693a313b733a32393a22d0bad180d0bed0b2d0b0d182d18c20d0b4d0b5d182d181d0bad0b0d18f223b693a323b733a31303a22d0bad0bed0bcd0bed0b4223b7d, '', 0x613a323a7b693a303b733a32373a22d181d182d0bed0bb20d0bed0b1d0b5d0b4d0b5d0bdd0bdd18bd0b9223b693a313b733a33323a22d181d182d183d0bbd18cd18f2c20d182d0b0d0b1d183d180d0b5d182d0bad0b8223b7d, '', 0x613a323a7b693a303b733a32323a22d185d0bed0bbd0bed0b4d0b8d0bbd18cd0bdd0b8d0ba223b693a313b733a31383a22d182d0b5d0bbd0b5d0b2d0b8d0b7d0bed180223b7d, '', 'a:0:{}', 'a:0:{}', 'с детьми старше 4-х лет', 'не имеет значения', '9221431645', '10:00', '20:00', 'отдельно', '', 1348686239, 1348686239, 'опубликовано', '', '', '', '', '1')
");

echo "Статус регистрации объектов недвижимости: ";
if (DBconnect::get()->errno) returnResultMySql(FALSE); else returnResultMySql(TRUE);

// Создаем таблицу для постоянного хранения информации о ФОТОГРАФИЯХ объектов недвижимости
DBconnect::get()->query("INSERT INTO propertyFotos (id, folder, filename, extension, filesizeMb, propertyId, status, regDate) VALUES
('03b141bbb26e42040b96fb9176ae28c4', 'uploaded_files/0', 'Квартира4.jpg', 'jpeg', 0.1, 1, 'основная', 1348563523),
('08c1da5a4849d70e2e109219382c9dfc', 'uploaded_files/0', '291.JPG', 'jpeg', 0.1, 1, '', 1348563523),
('11d92a3d33d510ecb02f9ad89ba842df', 'uploaded_files/1', 'Квартира2.jpg', 'jpeg', 0, 4, 'основная', 1348563523),
('155d3a797c42f233afc305954ce9bc3b', 'uploaded_files/1', 'Квартира2.jpg', 'jpeg', 0, 2, 'основная', 1348563523),
('1e01eb19077606729cd208ef8f1813c4', 'uploaded_files/1', 'Квартира5.jpg', 'jpeg', 0, 5, 'основная', 1348563523),
('4cbb15f69dee3c789e5cc421da294d39', 'uploaded_files/4', 'Квартира1.jpg', 'jpeg', 0, 2, '', 1348563523),
('6ec697030d123ac8903e7b579f827fd1', 'uploaded_files/6', 'Квартира2.jpg', 'jpeg', 0, 7, 'основная', 1348563523),
('a2ae107cba37fa06d99c31e67e028421', 'uploaded_files/a', 'Квартира4.jpg', 'jpeg', 0.1, 6, 'основная', 1348563523),
('b3185f04cd6cbd1b832e16be6162b234', 'uploaded_files/b', 'Квартира5.jpg', 'jpeg', 0, 4, '', 1348563523),
('b6ac5310bb479949a37940ef97d2d50d', 'uploaded_files/b', 'Квартира3.jpg', 'jpeg', 0.1, 3, 'основная', 1348563523),
('c130dad1e123737c7325f797cbeda477', 'uploaded_files/c', 'Квартира4.jpg', 'jpeg', 0.1, 7, '', 1348563523),
('c3f0e2ec40c21d7ea821e20995389137', 'uploaded_files/c', 'Квартира3.jpg', 'jpeg', 0.1, 2, '', 1348563523),
('d0541fa5096a2b58cc6dc99118409ec3', 'uploaded_files/d', '291.JPG', 'jpeg', 0.1, 8, 'основная', 1348563523),
('e3a4fba78730181de8a9037e13a5b848', 'uploaded_files/e', 'Квартира5.jpg', 'jpeg', 0, 6, '', 1348563523),
('fb7caff3b35748fbf629fca0f6f679b7', 'uploaded_files/f', 'Квартира1.jpg', 'jpeg', 0, 7, '', 1348563523),
('fbd5e8e558fb7e78c4d689f34ca82b55', 'uploaded_files/f', 'Квартира5.jpg', 'jpeg', 0, 7, '', 1348563523),
('03b141bbb26e42040b96fb9176ae28c5', 'uploaded_files/0', 'Квартира4.jpg', 'jpeg', 0.1, 9, 'основная', 1348563523),
('08c1da5a4849d70e2e109219382c9dfd', 'uploaded_files/0', '291.JPG', 'jpeg', 0.1, 10, 'основная', 1348563523),
('11d92a3d33d510ecb02f9ad89ba842d0', 'uploaded_files/1', 'Квартира2.jpg', 'jpeg', 0, 11, 'основная', 1348563523),
('155d3a797c42f233afc305954ce9bc3c', 'uploaded_files/1', 'Квартира2.jpg', 'jpeg', 0, 12, 'основная', 1348563523),
('1e01eb19077606729cd208ef8f1813c5', 'uploaded_files/1', 'Квартира5.jpg', 'jpeg', 0, 13, 'основная', 1348563523),
('4cbb15f69dee3c789e5cc421da294d3a', 'uploaded_files/4', 'Квартира1.jpg', 'jpeg', 0, 14, 'основная', 1348563523),
('6ec697030d123ac8903e7b579f827fd2', 'uploaded_files/6', 'Квартира2.jpg', 'jpeg', 0, 15, 'основная', 1348563523),
('a2ae107cba37fa06d99c31e67e028422', 'uploaded_files/a', 'Квартира4.jpg', 'jpeg', 0.1, 16, 'основная', 1348563523),
('b3185f04cd6cbd1b832e16be6162b235', 'uploaded_files/b', 'Квартира5.jpg', 'jpeg', 0, 17, 'основная', 1348563523),
('b6ac5310bb479949a37940ef97d2d50e', 'uploaded_files/b', 'Квартира3.jpg', 'jpeg', 0.1, 18, 'основная', 1348563523),
('c130dad1e123737c7325f797cbeda478', 'uploaded_files/c', 'Квартира4.jpg', 'jpeg', 0.1, 19, 'основная', 1348563523),
('c3f0e2ec40c21d7ea821e20995389138', 'uploaded_files/c', 'Квартира3.jpg', 'jpeg', 0.1, 20, 'основная', 1348563523),
('d0541fa5096a2b58cc6dc99118409ec4', 'uploaded_files/d', '291.JPG', 'jpeg', 0.1, 21, 'основная', 1348563523),
('e3a4fba78730181de8a9037e13a5b849', 'uploaded_files/e', 'Квартира5.jpg', 'jpeg', 0, 22, 'основная', 1348563523),
('fb7caff3b35748fbf629fca0f6f679b8', 'uploaded_files/f', 'Квартира1.jpg', 'jpeg', 0, 23, 'основная', 1348563523),
('fbd5e8e558fb7e78c4d689f34ca82b56', 'uploaded_files/f', 'Квартира5.jpg', 'jpeg', 0, 23, '', 1348563523)
");

echo "Статус регистрации фотографий недвижимости: ";
if (DBconnect::get()->errno) returnResultMySql(FALSE); else returnResultMySql(TRUE);

// Закрываем соединение с БД
DBconnect::closeConnectToDB();