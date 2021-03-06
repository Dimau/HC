<!DOCTYPE html>
<html>
<head>

    <!-- meta -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Content-language" content="ru">
    <meta name="description" content="Успешная оплата">
    <!-- Если у пользователя IE: использовать последний доступный стандартный режим отображения независимо от <!DOCTYPE> -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <!-- Оптимизация отображения на мобильных устройствах -->
    <!--<meta name="viewport" content="initialscale=1.0, width=device-width">-->
    <!-- end meta -->

    <title>Успешная оплата</title>

    <!-- CSS -->
    <link rel="stylesheet" href="css/jquery-ui-1.8.22.custom.css">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .miniBlockContent {
            text-align: center;
        }

        .miniBlockContent .text {
            display: inline-block;
            text-align: center;
            line-height: normal;
        }
    </style>
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
    <!-- end JS -->

</head>

<body>
<div class="pageWithoutFooter">

    <?php
    // Сформируем и вставим заголовок страницы
    require $websiteRoot . "/templates/templ_header.php";
    ?>

    <div class="miniBlock">
        <div class="miniBlockHeader">
            Успешная оплата премиум доступа к сервису Svobodno.org!
        </div>
        <div class="miniBlockContent">
            <div class="text">
                <p style="text-align: left;">
                    Вы успешно оплатили премиум доступ к сервису Svobodno.org. Мы благодарны Вам за то, что Вы выбрали наш сервис!
                </p>

                <p style="text-align: left;">
                    По любым вопросам обращайтесь к нам в техническую поддержку по e-mail: support@svobodno.org
                </p>

                <p style="text-align: left;">
                    Если Вы указывали e-mail при регистрации (или в настройках профиля в <a href="personal.php">Личном кабинете</a>), Вам будет автоматически активировано e-mail оповещение о новых объявлениях. Отписаться от него Вы можете также в Личном кабинете.
                </p>
            </div>
        </div>
        <div class="clearBoth"></div>
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