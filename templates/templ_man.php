<!DOCTYPE html>
<html>
<head>

    <!-- meta -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Content-language" content="ru">
    <meta name="description"
          content="<?php echo $userCharacteristic['surname'] . " " . $userCharacteristic['name'] . " " . $userCharacteristic['secondName']; ?>">
    <!-- Если у пользователя IE: использовать последний доступный стандартный режим отображения независимо от <!DOCTYPE> -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <!-- Оптимизация отображения на мобильных устройствах -->
    <!--<meta name="viewport" content="initialscale=1.0, width=device-width">-->
    <!-- end meta -->

    <title><?php echo $userCharacteristic['surname'] . " " . $userCharacteristic['name'] . " " . $userCharacteristic['secondName']; ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="css/jquery-ui-1.8.22.custom.css">
    <link rel="stylesheet" href="css/colorbox.css">
    <link rel="stylesheet" href="css/main.css">
    <!-- end CSS -->

    <!-- JS -->
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
    <!-- end JS -->

</head>

<body>
<div class="pageWithoutFooter">

    <?php
    // Сформируем и вставим заголовок страницы
    require $websiteRoot . "/templates/templ_header.php";
    ?>

    <div class="headerOfPage">
        Характеристика пользователя
    </div>
    <div id="tabs" class="mainContentBlock">
        <ul>
            <li>
                <a href="#tabs-1">Профиль</a>
            </li>
            <li>
                <a href="#tabs-2">Условия поиска</a>
            </li>
        </ul>
        <div id="tabs-1">
            <div id="notEditingProfileParametersBlock">
                <?php
                // Формируем и размещаем на странице блок для основной фотографии пользователя
                echo View::getHTMLfotosWrapper("middle", TRUE, FALSE, $userFotoInformation['uploadedFoto']);

                // Вставляем анкетные данные пользователя
                require $websiteRoot . "/templates/notEditableBlocks/templ_notEditedProfile.php";
                ?>
            </div>
            <div class="clearBoth"></div>
        </div>
        <!-- /end.tabs-1 -->
        <div id="tabs-2">
            <?php if ($userSearchRequest == FALSE): ?>
            <div class="shadowText">
                Пользователь не ищет недвижимость в данный момент
            </div>
            <?php endif;?>
            <?php
            // Шаблон для представления нередактируемых параметров поисковго запроса пользователя
            if ($userSearchRequest != FALSE) require $websiteRoot . "/templates/notEditableBlocks/templ_notEditedSearchRequest.php";
            ?>
        </div>
        <!-- /end.tabs-2 -->
    </div>

    <!-- Блок для прижатия подвала к низу страницы без закрытия части контента, его CSS высота доллжна быть = высоте футера -->
    <div class="page-buffer"></div>
</div>
<!-- /end.pageWithoutFooter -->
<div class="footer">
    2013 г. Мы будем рады ответить на Ваши вопросы, отзывы, предложения по телефону: 8-922-179-59-07, или e-mail: support@svobodno.org
</div>
<!-- /end.footer -->

<!-- JavaScript at the bottom for fast page loading: http://developer.yahoo.com/performance/rules.html#js_bottom -->
<script src="js/main.js"></script>
<!-- end scripts -->

</body>
</html>