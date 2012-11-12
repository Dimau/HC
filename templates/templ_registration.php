<?php
    // Инициализируем используемые в шаблоне переменные
    $userCharacteristic = $dataArr['userCharacteristic'];
    $userFotoInformation = $dataArr['userFotoInformation'];
    $userSearchRequest = $dataArr['userSearchRequest'];
    $errors = $dataArr['errors'];
    $allDistrictsInCity = $dataArr['allDistrictsInCity'];
    $isLoggedIn = $dataArr['isLoggedIn'];
    $whatPage = $dataArr['whatPage'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <!-- Use the .htaccess and remove these lines to avoid edge case issues.
         More info: h5bp.com/i/378 -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>Форма регистрации</title>
    <meta name="description" content="Форма регистрации">

    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="initialscale=1.0, width=device-width">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

    <link rel="stylesheet" href="css/jquery-ui-1.8.22.custom.css">
    <link rel="stylesheet" href="css/fileuploader.css">
    <link rel="stylesheet" href="css/main.css">
    <style>

            /* Основные стили для элементов управления формы */
        .bottomControls {
            padding: 10px 0px 0px 0px;
        }

        .backButton {
            float: left;
        }

        .forwardButton, .submitButton {
            float: right;
        }

            /* Стили для страницы социальных сетей */
        fieldset.edited.social {
            width: auto;
        }

        fieldset.edited.social input[type=text] {
            width: 400px;
        }

    </style>

    <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <!-- Если jQuery с сервера Google недоступна, то загружаем с моего локального сервера -->
    <script>
        if (typeof jQuery === 'undefined') document.write("<scr" + "ipt src='js/vendor/jquery-1.7.2.min.js'></scr" + "ipt>");
    </script>
    <!-- Если jQuery с сервера Google недоступна, то загружаем с моего локального сервера -->
    <!-- jQuery UI с моей темой оформления -->
    <script src="js/vendor/jquery-ui-1.8.22.custom.min.js"></script>
    <!-- Русификатор виджета календарь -->
    <script src="js/vendor/jquery.ui.datepicker-ru.js"></script>
    <!-- Загрузчик фотографий на AJAX -->
    <script src="js/vendor/fileuploader.js" type="text/javascript"></script>

</head>

<body>
<div class="page_without_footer">

<!-- Добавялем невидимый input для того, чтобы передать тип пользователя (собственник/арендатор) - это используется в JS для простановки обязательности полей для заполнения -->
<input type='hidden' class='userType' typeTenant='<?php if ($userCharacteristic['typeTenant']) echo "TRUE"; else echo "FALSE";?>' typeOwner='<?php if ($userCharacteristic['typeOwner']) echo "TRUE"; else echo "FALSE";?>'>

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
                if (is_array($errors) && count($errors) != 0) {
                    foreach ($errors as $key => $value) {
                        echo "<li>$value</li>";
                    }
                }
                ?></ol>
        </div>
    </div>
</div>

<!-- Сформируем и вставим заголовок страницы -->
<?php
    include("templates/templ_header.php");
?>

<div class="page_main_content">

<div class="headerOfPageContentBlock">
    <div class="headerOfPage">
        Зарегистрируйтесь
    </div>

    <?php if ($userCharacteristic['typeTenant']): ?>
    <div class="importantAddInfBlock">
        <div class="importantAddInfHeader">
            Регистрация позволит бесплатно:
        </div>
        <ul class="benefits">
            <li>
                Записаться на просмотр любой недвижимости
            </li>
            <li>
                Получать уведомления о появлении подходящих вариантов недвижимости
            </li>
            <li>
                Добавлять объявления в избранные и в любой момент просматривать их
            </li>
            <li>
                Не указывать повторно условия поиска - система запомнит их
            </li>
        </ul>
    </div>
    <?php endif; ?>

    <div class="clearBoth"></div>
</div>

<form name="personalInformation" id="personalInformationForm" class="formWithFotos" method="post"
      enctype="multipart/form-data">
<div id="tabs">
<ul>
    <li>
        <a href="#tabs-1">Личные данные</a>
    </li>
    <li>
        <a href="#tabs-2">Образование / Работа</a>
    </li>
    <li>
        <a href="#tabs-3">Социальные сети</a>
    </li>
    <?php if ($userCharacteristic['typeTenant']): ?>
    <li>
        <a href="#tabs-4">Что ищете?</a>
    </li>
    <?php endif; ?>
</ul>

<div id="tabs-1">
    <div class="shadowText">
        Информация, указаннная при регистрации, необходима для того, чтобы представить Вас собственникам тех объектов,
        которыми Вы заинтересутесь.
        <br>
        <span class="required">* </span> - обязательное для заполнения поле
    </div>

    <?php
        // Подключим форму для ввода и редактирования данных о ФИО, логине, контактах пользователя, а также о фотографиях
        include "templates/templ_editablePersonalFIO.php";
    ?>

    <div class="bottomControls">
        <button class="forwardButton mainButton">Далее</button>
        <div class="clearBoth"></div>
    </div>
</div>

<div id="tabs-2">
    <div class="shadowText">
        Данные об образовании и работе арендатора - одни из самых востребованных для любого собственника жилья. Эта
        информация предоставляется собственникам только тех объектов, которыми Вы заинтересуетесь.
    </div>

    <?php
        // Подключим форму для ввода и редактирования данных об образовании, работе и месте рождения
        include "templates/templ_editablePersonalEducAndWork.php";
    ?>

    <div class="bottomControls">
        <button class="backButton">Назад</button>
        <button class="forwardButton mainButton">Далее</button>
        <div class="clearBoth"></div>
    </div>
</div>

<div id="tabs-3">
    <div class="shadowText">
        Укажите, пожалуйста, адрес Вашей личной страницы минимум в одной социальной сети. Это позволит системе
        представить Вас собственникам (только тех объектов, которыми Вы сами заинтересуетесь).
    </div>

    <?php
        // Подключим форму для ввода и редактирования данных о социальных сетях пользователя
        include "templates/templ_editablePersonalSocial.php";
    ?>

    <?php if ($userCharacteristic['typeTenant']): ?>
    <div class="bottomControls">
        <button class="backButton">Назад</button>
        <button class="forwardButton mainButton">Далее</button>
        <div class="clearBoth"></div>
    </div>
    <?php endif; ?>
    <?php if (!$userCharacteristic['typeTenant']): ?>
    <div class="bottomControls">
        <div style="float: right; margin-bottom: 10px; text-align: left;">
            <label><input type="checkbox" name="lic" id="lic" value="yes" <?php if ($userCharacteristic['lic'] == "yes") echo "checked";?>> Я
            принимаю условия <a
            href="#">лицензионного соглашения</a></label>
        </div>
        <div class="clearBoth"></div>
        <button class="backButton">Назад</button>
        <button type="submit" name="submitButton" class="submitButton mainButton">Отправить</button>
        <div class="clearBoth"></div>
    </div>
    <?php endif; ?>
</div>

<?php if ($userCharacteristic['typeTenant']): ?>
<div id="tabs-4">
    <div class="shadowText">
        Заполните форму как можно подробнее, это позволит системе подобрать для Вас наиболее интересные предложения
    </div>

    <?php
        // Подключим форму для ввода и редактирования данных о поисковом запросе пользователя
        include "templates/templ_editableSearchRequest.php";
    ?>

    <div class="bottomControls">
        <div style="float: right; margin-bottom: 10px; text-align: left;">
            <label><input type="checkbox" name="lic" id="lic" value="yes" <?php if ($userCharacteristic['lic'] == "yes") echo "checked";?>> Я
            принимаю условия <a
            href="#">лицензионного соглашения</a></label>
        </div>
        <div class="clearBoth"></div>
        <button class="backButton">Назад</button>
        <button type="submit" name="submitButton" class="submitButton mainButton">Отправить</button>
        <div class="clearBoth"></div>
    </div>

</div>
<!-- /end.tabs-4 -->
    <?php endif;?>
</div>
<!-- /end.tabs -->

</form>
</div>

<!-- /end.page_main_content -->
<!-- Блок для прижатия подвала к низу страницы без закрытия части контента, его CSS высота доллжна быть = высоте футера -->
<div class="page-buffer"></div>
</div>
<!-- /end.page_without_footer -->
<div class="footer">
    2012 г. Вопросы и пожелания по работе портала можно передавать по телефону: 8-922-143-16-15, e-mail: support@svobodno.org
</div>
<!-- /end.footer -->

<!-- JavaScript -->
<script>
    // Сервер сохранит в эту переменную данные о загруженных фотографиях в формате JSON
    // Переменная uploadedFoto содержит массив объектов, каждый из которых представляет информацию по 1 фотографии
    var uploadedFoto = JSON.parse('<?php echo json_encode($userFotoInformation['uploadedFoto']);?>');
</script>
<script src="js/main.js"></script>
<script src="js/registration.js"></script>
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