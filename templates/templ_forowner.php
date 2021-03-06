<!DOCTYPE html>
<html>
<head>

    <!-- meta -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Content-language" content="ru">
    <meta name="description" content="Подать объявление о сдаче в аренду недвижимости">
    <!-- Если у пользователя IE: использовать последний доступный стандартный режим отображения независимо от <!DOCTYPE> -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <!-- Информация для поисковых систем об индексации страницы -->
    <meta name="document-state" content="Dynamic">
    <meta name="keywords" content="Сдать, недвижимость, в Екатеринбурге">
    <meta name="robots" content="index,follow">
    <!-- Оптимизация отображения на мобильных устройствах -->
    <!--<meta name="viewport" content="initialscale=1.0, width=device-width">-->
    <!-- end meta -->

    <title>Подать объявление</title>

    <!-- CSS -->
    <link rel="stylesheet" href="css/jquery-ui-1.8.22.custom.css">
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
    <!-- end JS -->

</head>

<body>
<div class="pageWithoutFooter">

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

    <?php
    // Сформируем и вставим заголовок страницы
    require $websiteRoot . "/templates/templ_header.php";
    ?>

    <div class="headerOfPage">
        Поможем сдать Вашу недвижимость!
    </div>

    <div class="edited left mainContentBlock" style="min-width: 430px;">

        <?php if (!isset($errors) || (is_array($errors) && count($errors) != 0)): ?>
        <form name="requestFromOwnerForm" id="requestFromOwnerForm" method="post"
              action="forowner.php?action=takeRequest">
            <table>
                <tbody>
                <tr>
                    <td class="itemLabel">
                        Как к Вам обращаться
                    </td>
                    <td class="itemRequired">
                        *
                    </td>
                    <td class="itemBody">
                        <input type="text" name="name" id="name" maxlength="100"
                               value="<?php echo $requestFromOwnerData['name']; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="itemLabel">
                        Ваш контактный номер
                    </td>
                    <td class="itemRequired">
                        *
                    </td>
                    <td class="itemBody">
                        <input type="text" name="telephon" id="telephon" maxlength="20"
                               value="<?php echo $requestFromOwnerData['telephon']; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="itemLabel">
                        Адрес недвижимости
                    </td>
                    <td class="itemRequired">
                        *
                    </td>
                    <td class="itemBody">
                        <input type="text" name="address" id="address" maxlength="60"
                               value="<?php echo $requestFromOwnerData['address']; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="itemLabel">
                        Комментарий:
                    </td>
                    <td class="itemRequired">
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <textarea name="commentOwner" rows="4"
                                  title="Например, в какое время Вам будет удобно принять наш звонок"><?php echo $requestFromOwnerData['commentOwner']; ?></textarea>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="bottomButton">
                <button type="submit" name="submitButton" id="submitButton" class="mainButton">
                    Отправить заявку
                </button>
            </div>

            <div class="clearBoth"></div>
        </form>
        <?php endif; ?>

        <?php if (is_array($errors) && count($errors) == 0): ?>
        <div>
            <span style="font-size: 14px;">Запрос успешно передан</span><br><br>
            <span>Спасибо за Ваше доверие, мы приложим все усилия, чтобы его оправдать!</span>
        </div>
        <?php endif; ?>

    </div>

    <div class="mainContentBlock"
         style="float: left; width: 48.5%; min-width: 430px; margin-top: 10px; margin-bottom: 10px; margin-left: 1.4%; text-align: left;">
        <div class="localHeader">
            Что будет дальше?
        </div>
        <ul class="simpleTextList">
            <li>
                В течение дня Вам перезвонит оператор и зафиксирует параметры недвижимости
            </li>
            <li>
                Как только объявление будет сформировано, мы его опубликуем на портале Svobodno.org
            </li>
            <li>
                Арендаторы с условиями поиска, подходящими под Вашу недвижимость, автоматически получат уведомления. Они смогут связаться с Вами
            </li>
            <li>
                С интересными претендентами Вы сможете договориться о просмотре и аренде недвижимости
            </li>
            <li>
                В итоге: недвижимость сдана порядочным людям, которых Вы выберете сами с минимальными усилиями!
            </li>
        </ul>
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
<script>
    // JS проверка формы перед отправкой на сервер
    $("#requestFromOwnerForm").on('submit', function () {
        if (executeValidation("forowner") != 0) {
            return false;
        } else {
            return true;
        }
    });
</script>
<!-- end scripts -->

</body>
</html>