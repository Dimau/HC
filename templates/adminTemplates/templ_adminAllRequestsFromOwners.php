<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <!-- Use the .htaccess and remove these lines to avoid edge case issues.
         More info: h5bp.com/i/378 -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>Админка</title>
    <meta name="description" content="Админка">

    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="initialscale=1.0, width=device-width">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

    <link rel="stylesheet" href="css/jquery-ui-1.8.22.custom.css">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .simpleBlockForAnyContent {
            margin: 10px 0 10px 0;
            font-size: 0.9em;
            line-height: 2em;
        }

        .simpleBlockForAnyContent .content {
            font-size: 1.1em;
            color: #6A9D02;
            font-weight: bold;
        }

        .simpleBlockForAnyContent .setOfInstructions {
            float: left;
            margin-left: 15px;
            list-style: none;
        }

        .simpleBlockForAnyContent .setOfInstructions li {
            display: inline-block;
            margin-left: 10px;
            margin-right: 10px;
            font-size: 1em;
        }

            /* Используется для выделения описания той заявки на просмотр, что интересует админа */
        .highlightedBlock {
            padding: 5px;
            border: 2px solid red;
        }

    </style>

    <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <!-- Если jQuery с сервера Google недоступна, то загружаем с моего локального сервера -->
    <script>
        if (typeof jQuery === 'undefined') document.write("<scr" + "ipt src='js/vendor/jquery-1.7.2.min.js'></scr" + "ipt>");
    </script>
    <!-- jQuery UI с моей темой оформления -->
    <script src="js/vendor/jquery-ui-1.8.22.custom.min.js"></script>

</head>

<body>
<div class="page_without_footer">
    <div class="page_main_content">
        <div class="headerOfPage">
            Панель администратора -> Заявки от собственников
        </div>

        <div class="simpleBlockForAnyContent">
			<?php foreach ($allRequestsFromOwners as $requestFromOwner): ?>
			<?php include "templates/adminTemplates/templ_adminRequestFromOwnerItem.php"; ?>
            <hr>
			<?php endforeach; ?>
        </div>

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

<!-- JavaScript at the bottom for fast page loading: http://developer.yahoo.com/performance/rules.html#js_bottom -->
<script src="js/main.js"></script>
<script src="js/admin.js"></script>
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