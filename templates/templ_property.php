<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <!-- Use the .htaccess and remove these lines to avoid edge case issues.
         More info: h5bp.com/i/378 -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title><?php echo $strHeaderOfPage; ?></title>
    <meta name="description" content="<?php echo $strHeaderOfPage; ?>">

    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="initialscale=1.0, width=device-width">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

    <link rel="stylesheet" href="css/jquery-ui-1.8.22.custom.css">
    <link rel="stylesheet" href="css/colorbox.css">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .setOfInstructions {
            text-align: left;
            margin: 10px 0 20px 10px;
        }

        .setOfInstructions li {
            margin: 6px 0 6px 0;
        }

        .setOfInstructions li:first-child {
            margin-top: 0;
        }

        .setOfInstructions li:last-child {
            margin-bottom: 0;
        }

            /* Стиль блока с информацией о текущем статусе Заявки на просмотр */
        .signUpToViewStatusBlock {
            display: inline-block;
            border: 2px solid #ff6f00;
            border-radius: 5px;
            padding: 5px 8px 5px 8px;
            text-align: center;
        }

        .signUpToViewStatusBlock.inProgress,
        .signUpToViewStatusBlock.confirmed {
            color: #6A9D02;
            font-weight: bold;
        }

        .signUpToViewStatusBlock.error,
        .signUpToViewStatusBlock.failure {
            color: red;
            font-weight: bold;
        }

        .furnitureList {
            margin: 0;
            padding: 0;
            list-style: square;
        }
    </style>

</head>

<body>
<div class="page_without_footer">

<!-- Всплывающее поле для отображения списка ошибок, полученных при проверке данных на сервере (PHP)-->
<div id="userMistakesBlock" class="ui-widget">
    <div class="ui-state-highlight ui-corner-all">
        <div>
            <p>
                <span class="icon-mistake ui-icon ui-icon-info"></span>
                <span
                        id="userMistakesText">Для продолжения, пожалуйста, дополните или исправьте следующие данные:</span>
            </p>
            <ol><?php
				if (isset($errors) && count($errors) != 0) {
					foreach ($errors as $value) {
						echo "<li>$value</li>";
					}
				}
				?></ol>
        </div>
    </div>
</div>

<?php
// Сформируем и вставим заголовок страницы
include("templates/templ_header.php");
?>

<!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<!-- Если jQuery с сервера Google недоступна, то загружаем с моего локального сервера -->
<script>
    if (typeof jQuery === 'undefined') document.write("<scr" + "ipt src='js/vendor/jquery-1.7.2.min.js'></scr" + "ipt>");
</script>
<!-- jQuery UI с моей темой оформления -->
<script src="js/vendor/jquery-ui-1.8.22.custom.min.js"></script>
<!-- ColorBox - плагин jQuery, позволяющий делать модальное окно для просмотра фотографий -->
<script src="js/vendor/jquery.colorbox-min.js"></script>
<!-- Загружаем библиотеку для работы с картой от Яндекса -->
<script src="http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU" type="text/javascript"></script>
<script src="js/main.js"></script>
<script>
    $(document).ready(function () {

        $("#signUpToViewDialog").dialog({
            autoOpen:false,
            modal:true,
            width:600,
            dialogClass:"edited"
        });

        $(".signUpToViewButton").click(function () {
            $("#signUpToViewDialog").dialog("open");
        });

        $("#signUpToViewDialogCancel").on('click', function () {
            $("#signUpToViewDialog").dialog("close");
        });

    });

    /* Как только будет загружен API и готов DOM, выполняем инициализацию */
    ymaps.ready(init);
    function init() {
        // Создание экземпляра карты и его привязка к контейнеру с
        // заданным id ("mapForAdvertView")
        // Получаем координаты объекта недвижимости
        var coordX = $("#coordX").val();
        var coordY = $("#coordY").val();

        // Непосредственно инициализируем карту
        if (coordX != "" && coordY != "") {
            var map = new ymaps.Map('mapForAdvertView', {
                // При инициализации карты, обязательно нужно указать
                // ее центр и коэффициент масштабирования
                center:[$("#coordX").val(), $("#coordY").val()],
                zoom:16,
                // Включим поведения по умолчанию (default) и,
                // дополнительно, масштабирование колесом мыши.
                // дополнительно включаем измеритель расстояний по клику левой кнопки мыши
                behaviors:['default', 'scrollZoom', 'ruler']
            });

            // Добавляем на карту метку объекта недвижимости
            currentPlacemark = new ymaps.Placemark([coordX, coordY]);
            map.geoObjects.add(currentPlacemark);

        } else {
            var map = new ymaps.Map('mapForAdvertView', {
                // При инициализации карты, обязательно нужно указать
                // ее центр и коэффициент масштабирования
                center:[56.829748, 60.617435], // Екатеринбург
                zoom:11,
                // Включим поведения по умолчанию (default) и,
                // дополнительно, масштабирование колесом мыши.
                // дополнительно включаем измеритель расстояний по клику левой кнопки мыши
                behaviors:['default', 'scrollZoom', 'ruler']
            });
        }

        /***** Добавляем элементы управления на карту *****/
            // Для добавления элемента управления на карту используется поле controls, ссылающееся на
            // коллекцию элементов управления картой. Добавление элемента в коллекцию производится с помощью метода add().
            // В метод add можно передать строковый идентификатор элемента управления и его параметры.
            // Список типов карты
        map.controls.add('typeSelector');
        // Кнопка изменения масштаба - компактный вариант
        // Расположим её ниже и левее левого верхнего угла
        map.controls.add('smallZoomControl', {
            left:5,
            top:55
        });
        // Стандартный набор кнопок
        map.controls.add('mapTools');

        // При переключении вкладки карту нужно перестраивать
        $('#tabs').bind('tabsshow', reDrawMap);

        /***** Функция перестроения карты - используется при изменении размеров блока *****/
        function reDrawMap() {
            //map.setCenter([56.829748, 60.617435]);
            map.container.fitToViewport();
        }
    }
</script>

<div class="page_main_content">

<div class="headerOfPage">
	<?php echo $strHeaderOfPage; ?>
</div>

<div id="tabs">
<ul>
    <li>
        <a href="#tabs-1">Описание</a>
    </li>
    <li>
        <a href="#tabs-2">Местоположение</a>
    </li>
</ul>
<div id="tabs-1">

<div>
	<?php
	// Формируем и размещаем на странице блок для фотографий объекта недвижимости
	echo View::getHTMLfotosWrapper("middle", TRUE, FALSE, $propertyFotoInformation['uploadedFoto']);
	?>

    <ul class="setOfInstructions">
		<?php
		/* Оформляем пункт меню о Заявке на просмотр */
		include ("templates/signUpToViewBlocks/templ_signUpToViewItem.php");
		?>
        <li>
			<?php
			echo View::getHTMLforFavorites($propertyCharacteristic["id"], $favoritesPropertysId, "stringWithIcon");
			?>
        </li>
        <!-- TODO: добавить функциональность!
		<li>
			<a href="#"> отправить по e-mail</a>
		</li>
		<li>
			<a href="#"> похожие объявления</a>
		</li>-->
    </ul>

    <div class="clearBoth"></div>

</div>

<?php
// Подключаем нужное модальное окно для Запроса на просмотр
if ($isLoggedIn === FALSE) include "templates/signUpToViewBlocks/templ_signUpToViewDialog_ForLoggedOut.php";
if ($isLoggedIn === TRUE && $userCharacteristic['typeTenant'] !== TRUE) include "templates/signUpToViewBlocks/templ_signUpToViewDialog_ForOwner.php";
if ($isLoggedIn === TRUE && $userCharacteristic['typeTenant'] === TRUE) include "templates/signUpToViewBlocks/templ_signUpToViewDialog_ForTenant.php";
?>

<div class="objectDescription">

<div class="notEdited left">
    <div class='legend'>
        Комнаты и помещения
    </div>
    <table>
        <tbody>

		<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['amountOfRooms'] != "0"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Кол-во комнат:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['amountOfRooms'];?></span>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['amountOfRooms'] != "0" && $propertyCharacteristic['amountOfRooms'] != "1" && $propertyCharacteristic['adjacentRooms'] != "0"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Комнаты смежные:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['adjacentRooms'];?></span>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "комната" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['adjacentRooms'] != "0" && $propertyCharacteristic['adjacentRooms'] != "нет" && $propertyCharacteristic['amountOfRooms'] != "0" && $propertyCharacteristic['amountOfRooms'] != "1" && $propertyCharacteristic['amountOfRooms'] != "2" && $propertyCharacteristic['amountOfAdjacentRooms'] != "0"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Кол-во смежных комнат:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['amountOfAdjacentRooms'];?></span>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['typeOfBathrooms'] != "0"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Санузел:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['typeOfBathrooms'];?></span>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['typeOfBalcony'] != "0"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Балкон/лоджия:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['typeOfBalcony'];?></span>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['typeOfBalcony'] != "0" && $propertyCharacteristic['typeOfBalcony'] != "нет" && $propertyCharacteristic['typeOfBalcony'] != "эркер" && $propertyCharacteristic['typeOfBalcony'] != "2 эркера и более" && $propertyCharacteristic['balconyGlazed'] != "0"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Остекление:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['balconyGlazed'];?></span>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "квартира" && $propertyCharacteristic['typeOfObject'] != "дом" && $propertyCharacteristic['typeOfObject'] != "таунхаус" && $propertyCharacteristic['typeOfObject'] != "дача" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['roomSpace'] != "0.00"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Площадь комнаты:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['roomSpace'];?> м²</span>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "комната" && $propertyCharacteristic['totalArea'] != "0.00"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Площадь общая:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['totalArea'];?> м²</span>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "комната" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['livingSpace'] != "0.00"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Площадь жилая:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['livingSpace'];?> м²</span>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "дача" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['kitchenSpace'] != "0.00"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Площадь кухни:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['kitchenSpace'];?> м²</span>
            </td>
        </tr>
			<?php endif; ?>

        </tbody>
    </table>
</div>

<div class="notEdited right">
    <div class='legend'>
        Стоимость, условия оплаты
    </div>
    <table>
        <tbody>

		<?php if ($propertyCharacteristic['costOfRenting'] != "" && $propertyCharacteristic['costOfRenting'] != "0"): ?>
        <tr>
            <td class="objectDescriptionItemLabel">Плата за аренду:</td>
            <td class="objectDescriptionBody"><?php echo "<span>" . $propertyCharacteristic['costOfRenting'] . "</span>" . " " . $propertyCharacteristic['currency'] . " в месяц" ?></td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['compensationMoney'] != "" && $propertyCharacteristic['currency'] != "" && $propertyCharacteristic['compensationPercent'] != "" && $propertyCharacteristic['compensationMoney'] != "0" && $propertyCharacteristic['currency'] != "0" && $propertyCharacteristic['compensationPercent'] != "0.00"): ?>
        <tr>
            <td class="objectDescriptionItemLabel">
                Единовременная комиссия:
            </td>
            <td class="objectDescriptionBody">
                <span><?php echo $propertyCharacteristic['compensationMoney'] . " " . $propertyCharacteristic['currency'] . " (" . $propertyCharacteristic['compensationPercent'] . "%)" ?></span>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['utilities'] != "" && $propertyCharacteristic['utilities'] != "0"): ?>
        <tr>
            <td class="objectDescriptionItemLabel">
                Коммунальные услуги:
            </td>
            <td class="objectDescriptionBody">
				<?php if ($propertyCharacteristic['utilities'] == "да"): ?>
                <span>оплачиваются дополнительно<?php if ($propertyCharacteristic['costInSummer'] != "" && $propertyCharacteristic['costInWinter'] != "" && $propertyCharacteristic['currency'] != "" && $propertyCharacteristic['costInSummer'] != "0" && $propertyCharacteristic['costInWinter'] != "0" && $propertyCharacteristic['currency'] != "0") echo ",<br>от " . $propertyCharacteristic['costInSummer'] . " до " . $propertyCharacteristic['costInWinter'] . " " . $propertyCharacteristic['currency'];?></span>
				<?php endif; ?>
				<?php if ($propertyCharacteristic['utilities'] == "нет"): ?>
                <span>включены в стоимость</span>
				<?php endif; ?>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['electricPower'] == "да"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Электроэнергия:
            </td>
            <td class='objectDescriptionBody'>
                <span>оплачивается дополнительно</span>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['bail'] != "" && $propertyCharacteristic['bail'] != "0"): ?>
        <tr>
            <td class="objectDescriptionItemLabel">Залог:</td>
            <td class="objectDescriptionBody">
                                        <span><?php if ($propertyCharacteristic['bail'] == "есть" && $propertyCharacteristic['bailCost'] != "" && $propertyCharacteristic['currency'] != "" && $propertyCharacteristic['bailCost'] != "0" && $propertyCharacteristic['currency'] != "0") echo $propertyCharacteristic['bailCost'] . " " . $propertyCharacteristic['currency'];
											if ($propertyCharacteristic['bail'] == "нет") echo "нет"; ?></span>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['prepayment'] != "" && $propertyCharacteristic['prepayment'] != "0"): ?>
        <tr>
            <td class="objectDescriptionItemLabel">
                Предоплата:
            </td>
            <td class="objectDescriptionBody">
                <span><?php echo $propertyCharacteristic['prepayment']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

        </tbody>
    </table>
</div>

<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "гараж"): ?>
<div class="notEdited left">
    <div class='legend'>
        Этаж и подъезд
    </div>
    <table>
        <tbody>

			<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "дом" && $propertyCharacteristic['typeOfObject'] != "таунхаус" && $propertyCharacteristic['typeOfObject'] != "дача" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['floor'] != "" && $propertyCharacteristic['totalAmountFloor'] != "" && $propertyCharacteristic['floor'] != "0" && $propertyCharacteristic['totalAmountFloor'] != "0"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Этаж:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['floor'] . " из " . $propertyCharacteristic['totalAmountFloor']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

			<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "квартира" && $propertyCharacteristic['typeOfObject'] != "комната" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['numberOfFloor'] != "" && $propertyCharacteristic['numberOfFloor'] != "0"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Этажность дома:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['numberOfFloor']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

			<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "дом" && $propertyCharacteristic['typeOfObject'] != "таунхаус" && $propertyCharacteristic['typeOfObject'] != "дача" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['concierge'] != "" && $propertyCharacteristic['concierge'] != "0"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Консьерж:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['concierge']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

			<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "дача" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['intercom'] != "" && $propertyCharacteristic['intercom'] != "0"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Домофон:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['intercom']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

			<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "дача" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['parking'] != "" && $propertyCharacteristic['parking'] != "0"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Парковка во дворе:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['parking']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

        </tbody>
    </table>
</div>
	<?php endif; ?>

<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "гараж"): ?>
<div class="notEdited right">
    <div class='legend'>
        Текущее состояние
    </div>
    <table>
        <tbody>

			<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['repair'] != "" && $propertyCharacteristic['repair'] != "0"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Ремонт:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['repair']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

			<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['furnish'] != "" && $propertyCharacteristic['furnish'] != "0"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Отделка:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['furnish']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

			<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['windows'] != "" && $propertyCharacteristic['windows'] != "0"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Окна:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['windows']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

        </tbody>
    </table>
</div>
	<?php endif; ?>

<div class="notEdited left">
    <div class='legend'>
        Тип и сроки
    </div>
    <table>
        <tbody>

		<?php if ($propertyCharacteristic['typeOfObject'] != "" && $propertyCharacteristic['typeOfObject'] != "0"): ?>
        <tr>
            <td class="objectDescriptionItemLabel">
                Тип объекта:
            </td>
            <td class="objectDescriptionBody">
                <span><?php echo $propertyCharacteristic['typeOfObject']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['dateOfEntry'] != "" && $propertyCharacteristic['dateOfEntry'] != "0000-00-00"): ?>
        <tr>
            <td class="objectDescriptionItemLabel">
                С какого числа можно въезжать:
            </td>
            <td class="objectDescriptionBody">
                <span><?php echo $propertyCharacteristic['dateOfEntry']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['termOfLease'] != "" && $propertyCharacteristic['termOfLease'] != "0"): ?>
        <tr>
            <td class="objectDescriptionItemLabel">
                Срок аренды:
            </td>
            <td class="objectDescriptionBody">
                <span><?php echo $propertyCharacteristic['termOfLease']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['termOfLease'] != "0" && $propertyCharacteristic['termOfLease'] != "длительный срок" && $propertyCharacteristic['dateOfCheckOut'] != "" && $propertyCharacteristic['dateOfCheckOut'] != "0000-00-00"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Крайний срок выезда:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['dateOfCheckOut']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

        </tbody>
    </table>
</div>

<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "гараж"): ?>
<div class="notEdited right">
    <div class='legend'>
        Связь
    </div>
    <table>
        <tbody>

			<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['internet'] != "" && $propertyCharacteristic['internet'] != "0"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Интернет:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['internet']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

			<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['telephoneLine'] != "" && $propertyCharacteristic['telephoneLine'] != "0"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Телефон:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['telephoneLine']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

			<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "гараж" && $propertyCharacteristic['cableTV'] != "" && $propertyCharacteristic['cableTV'] != "0"): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Кабельное ТВ:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['cableTV']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

        </tbody>
    </table>
</div>
	<?php endif; ?>

<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "гараж"): ?>
<div class="notEdited left">
    <div class='legend'>
        Мебель и бытовая техника
    </div>
    <table>
        <tbody>

			<?php if (is_array($furnitureInLivingArea) && (count($furnitureInLivingArea) != 0 || $propertyCharacteristic['completeness'] == "1")): ?>
        <tr>
            <td class="objectDescriptionItemLabel">
                Мебель в жилой зоне:
            </td>
            <td class="objectDescriptionBody">
                <ul class="furnitureList">
					<?php foreach ($furnitureInLivingArea as $value): ?>
                    <li>
						<?php echo $value; ?>
                    </li>
					<?php endforeach; ?>
					<?php if ($propertyCharacteristic['completeness'] == "1" && count($furnitureInLivingArea) == 0): ?>
                    <li>
                        <span>нет</span>
                    </li>
					<?php endif; ?>
                </ul>
            </td>
        </tr>
			<?php endif; ?>

			<?php if (is_array($furnitureInKitchen) && (count($furnitureInKitchen) != 0 || $propertyCharacteristic['completeness'] == "1")): ?>
        <tr>
            <td class="objectDescriptionItemLabel">
                Мебель на кухне:
            </td>
            <td class="objectDescriptionBody">
                <ul class="furnitureList">
					<?php foreach ($furnitureInKitchen as $value): ?>
                    <li>
						<?php echo $value; ?>
                    </li>
					<?php endforeach; ?>
					<?php if ($propertyCharacteristic['completeness'] == "1" && count($furnitureInKitchen) == 0): ?>
                    <li>
                        <span>нет</span>
                    </li>
					<?php endif; ?>
                </ul>
            </td>
        </tr>
			<?php endif; ?>

			<?php if (is_array($appliances) && (count($appliances) != 0 || $propertyCharacteristic['completeness'] == "1")): ?>
        <tr>
            <td class="objectDescriptionItemLabel">
                Бытовая техника:
            </td>
            <td class="objectDescriptionBody">
                <ul class="furnitureList">
					<?php foreach ($appliances as $value): ?>
                    <li>
						<?php echo $value; ?>
                    </li>
					<?php endforeach; ?>
					<?php if ($propertyCharacteristic['completeness'] == "1" && count($appliances) == 0): ?>
                    <li>
                        <span>нет</span>
                    </li>
					<?php endif; ?>
                </ul>
            </td>
        </tr>
			<?php endif; ?>

        </tbody>
    </table>
</div>
	<?php endif; ?>

<?php if ($propertyCharacteristic['typeOfObject'] != "0" && $propertyCharacteristic['typeOfObject'] != "гараж"): ?>
<div class="notEdited right">
    <div class='legend'>
        Требования к арендатору
    </div>
    <table>
        <tbody>

			<?php if (is_array($propertyCharacteristic['sexOfTenant']) && (count($propertyCharacteristic['sexOfTenant']) != 0 || $propertyCharacteristic['completeness'] == "1")): ?>
        <tr>
            <td class="objectDescriptionItemLabel">
                Пол:
            </td>
            <td class="objectDescriptionBody">
                <ul class="furnitureList">
					<?php if (count($propertyCharacteristic['sexOfTenant']) == 2): // Если указаны оба пола - пишем фразу "не имеет значения" ?>
                    <li>
                        <span>не имеет значения</span>
                    </li>
					<?php else: // Если собственник указал только один пол в качестве предпочтительного, то выводим его на страницу ?>
					<?php foreach ($propertyCharacteristic['sexOfTenant'] as $value): ?>
                        <li>
							<?php echo $value; ?>
                        </li>
						<?php endforeach; ?>
					<?php endif; ?>
                </ul>
            </td>
        </tr>
			<?php endif; ?>

			<?php if (is_array($propertyCharacteristic['relations']) && (count($propertyCharacteristic['relations']) != 0 || $propertyCharacteristic['completeness'] == "1")): ?>
        <tr>
            <td class="objectDescriptionItemLabel">
                Отношения между арендаторами:
            </td>
            <td class="objectDescriptionBody">
                <ul class="furnitureList">
					<?php foreach ($propertyCharacteristic['relations'] as $value): ?>
                    <li>
						<?php echo $value; ?>
                    </li>
					<?php endforeach; ?>
                </ul>
            </td>
        </tr>
			<?php endif; ?>

			<?php if ($propertyCharacteristic['children'] != "" && $propertyCharacteristic['children'] != "0"): ?>
        <tr>
            <td class="objectDescriptionItemLabel">
                Дети:
            </td>
            <td class="objectDescriptionBody">
                <span><?php echo $propertyCharacteristic['children']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

			<?php if ($propertyCharacteristic['animals'] != "" && $propertyCharacteristic['animals'] != "0"): ?>
        <tr>
            <td class="objectDescriptionItemLabel">
                Животные:
            </td>
            <td class="objectDescriptionBody">
                <span><?php echo $propertyCharacteristic['animals']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

        </tbody>
    </table>
</div>
	<?php endif; ?>

<div class="notEdited left">
    <div class='legend'>
        Особые условия
    </div>
    <table>
        <tbody>

		<?php if ($propertyCharacteristic['checking'] != "" && $propertyCharacteristic['checking'] != "0"): ?>
        <tr>
            <td class="objectDescriptionItemLabel">
                Где проживает собственник:
            </td>
            <td class="objectDescriptionBody">
                <span><?php echo $propertyCharacteristic['checking']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['responsibility'] != "" && $propertyCharacteristic['responsibility'] != "0"): ?>
        <tr>
            <td class="objectDescriptionItemLabel">
                Ответственность за состояние и ремонт:
            </td>
            <td class="objectDescriptionBody">
                <span><?php echo $propertyCharacteristic['responsibility']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

		<?php if ($propertyCharacteristic['comment'] != ""): ?>
        <tr>
            <td class='objectDescriptionItemLabel'>
                Дополнительный комментарий:
            </td>
            <td class='objectDescriptionBody'>
                <span><?php echo $propertyCharacteristic['comment']; ?></span>
            </td>
        </tr>
			<?php endif; ?>

        </tbody>
    </table>
</div>

<div class="clearBoth"></div>
</div>
</div>
<div id="tabs-2">
    <!-- Карта Яндекса -->
    <div id="mapForAdvertView" style="width: 50%; min-width: 300px; height: 400px; float: left;"></div>

    <ul class="setOfInstructions">
		<?php
		/* Оформляем пункт меню о Заявке на просмотр */
		include ("templates/signUpToViewBlocks/templ_signUpToViewItem.php");
		?>
        <li>
			<?php
			echo View::getHTMLforFavorites($propertyCharacteristic["id"], $favoritesPropertysId, "stringWithIcon");
			?>
        </li>
        <!-- TODO: добавить функциональность!
		<li>
			<a href="#"> отправить по e-mail</a>
		</li>
		<li>
			<a href="#"> похожие объявления</a>
		</li>-->
    </ul>

    <div class="notEdited right">
        <input type="hidden" name="coordX"
               id="coordX" <?php echo "value='" . $propertyCharacteristic['coordX'] . "'";?>>
        <input type="hidden" name="coordY"
               id="coordY" <?php echo "value='" . $propertyCharacteristic['coordY'] . "'";?>>
        <table>
            <tbody>
            <tr>
                <td class="objectDescriptionItemLabel">
					Город:
				</td>
                <td class="objectDescriptionBody">
                    <span><?php echo $propertyCharacteristic['city'];?></span>
                </td>
            </tr>
            <tr>
                <td class="objectDescriptionItemLabel">
					Район:
				</td>
                <td class="objectDescriptionBody">
					<span><?php if ($propertyCharacteristic['district'] != "" && $propertyCharacteristic['district'] != "0") echo $propertyCharacteristic['district'];?></span>
				</td>
            </tr>
            <tr>
                <td class="objectDescriptionItemLabel">
					Адрес:
				</td>
                <td class="objectDescriptionBody">
                    <span><?php echo $propertyCharacteristic['address'];?></span>
                </td>
            </tr>
			<?php
			if ($propertyCharacteristic['subwayStation'] != "0" && $propertyCharacteristic['subwayStation'] != "нет") echo "<tr><td class='objectDescriptionItemLabel'>Станция метро рядом:</td><td class='objectDescriptionBody'><span>" . $propertyCharacteristic['subwayStation'] . ",<br>" . $propertyCharacteristic['distanceToMetroStation'] . " мин. ходьбы" . "</span></td></tr>";
			?>
            </tbody>
        </table>
    </div>

    <div class="clearBoth"></div>
</div>
</div>

<?php
// Модальное окно для незарегистрированных пользователей, которые нажимают на кнопку добавления в Избранное
if ($isLoggedIn === FALSE) include "templates/templ_addToFavotitesDialog_ForLoggedOut.php";
?>

</div>
<!-- /end.page_main_content -->
<!-- Блок для прижатия подвала к низу страницы без закрытия части контента, его CSS высота доллжна быть = высоте футера -->
<div class="page-buffer"></div>
</div>
<!-- /end.page_without_footer -->
<div class="footer">
    2012 г. Вопросы и пожелания по работе портала можно передавать по телефону: 8-922-143-16-15, e-mail:
    support@svobodno.org
</div>
<!-- /end.footer -->

<!-- scripts -->
<!-- end scripts -->

<!-- Asynchronous Google Analytics snippet. Change UA-XXXXX-X to be your site's ID.
mathiasbynens.be/notes/async-analytics-snippet -->
<!-- <script>
var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
s.parentNode.insertBefore(g,s)}(document,'script'));
</script> -->
</body>
</html>