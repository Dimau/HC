<?
    // Инициализируем используемые в шаблоне переменные
    $isLoggedIn = $dataArr['isLoggedIn']; // Используется в templ_header.php
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <!-- Use the .htaccess and remove these lines to avoid edge case issues.
         More info: h5bp.com/i/378 -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>Хани Хом</title>
    <meta name="description" content="Аренда недвижимости">

    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="initialscale=1.0, width=device-width">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

    <link rel="stylesheet" href="css/jquery-ui-1.8.22.custom.css">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .blockHeader {
            font-family: Georgia, "Times New Roman", Times, serif;
            font-size: 1.3em;
            margin-bottom: 8px;
            text-align: center;
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
    <!-- Сформируем и вставим заголовок страницы -->
    <?php
        include("templates/templ_header.php");
    ?>

    <div class="page_main_content">

        <div class="bigAndBeatifullAnimation" style="height: 250px;"></div>

        <div id="tabs">

            <ul>
                <li>
                    <a href="#tabs-1">Собственнику</a>
                </li>
                <li>
                    <a href="#tabs-2">Арендатору</a>
                </li>
            </ul>

            <div id="tabs-1">
                <div class="benefitsBlock"
                     style="width: 49.5%; display: inline-block; vertical-align: top; text-align: center;">
                    <div class="blockHeader">Почему мы - лучший выбор</div>
                    <div class="accordion">
                        <h3><a href="#">Бесплатность услуг</a></h3>

                        <div class="accordionContentUnit">
                            <p>
                                Мы помогаем сдавать недвижимость бесплатно для собственников.
                            </p>
                        </div>
                        <h3><a href="#">Минимум действий – наш сотрудник приедет и все сделает</a></h3>

                        <div class="accordionContentUnit">
                            <p>
                                Мы составим подробное объявление для Вашей недвижимости.
                            </p>

                            <p>
                                Мы опубликуем объявление на всех интернет-ресурсах города для привлечения к нему
                                максимального внимания потенциальных нанимателей (в том числе, на e1.ru, на 66.ru, на
                                avito.ru и других ресурсах). Более того, мы ежедневно будем поднимать Ваше объявление в
                                рейтинге для того, чтобы оно оставалось заметным для наибольшего числа потенциальных
                                арендаторов. Также подробные данные о Вашей недвижимости будут размещены на нашем
                                портале.
                            </p>
                        </div>
                        <h3><a href="#">Легко найти порядочных нанимателей</a></h3>

                        <div class="accordionContentUnit">
                            <p>
                                Все арендаторы, желающие посмотреть Ваш объект недвижимости, заполняют на нашем сайте
                                подробную анкету, которая сразу же передается Вам. Мы просим арендаторов указывать, в
                                том числе, свои фотографии и ссылки на страницы в социальных сетях (одноклассники, в
                                контакте, facebook, twitter).
                            </p>

                            <p>
                                Таким образом, Вы сможете заранее составить свое мнение о потенциальном арендаторе и
                                решить еще до показа: хотите Вы ему сдавать свою недвижимость или нет.
                            </p>
                        </div>
                        <h3><a href="#">Просто сдать недвижимость с первого показа</a></h3>

                        <div class="accordionContentUnit">
                            <p>
                                Так как арендатор заранее подробно ознакомлен с Вашим объектом недвижимости, а Вы
                                ознакомлены с его данными из анкеты, то с большой долей вероятности первый же показ
                                закончится заключением договора аренды. Таким образом, Вы экономите свое время и силы
                                при поиске порядочных нанимателей.
                            </p>
                        </div>
                        <h3><a href="#">Профессиональный договор аренды</a></h3>

                        <div class="accordionContentUnit">
                            <p>
                                Договор аренды будет составлен нашим специалистом с учетом Ваших потребностей и
                                особенностей Вашей недвижимости на основе шаблона, учитывающего лучшие практики в этой
                                области.
                            </p>
                        </div>
                        <h3><a href="#">Полный контроль над Вашим объявлением </a></h3>

                        <div class="accordionContentUnit">
                            <p>
                                Начав работать с нами, Вы получите личный кабинет на сайте, который позволит Вам в любой
                                момент времени самостоятельно редактировать объявление, снимать его с публикации и
                                наоборот – заново публиковать, а также просматривать анкеты всех потенциальных
                                арендаторов, которые им заинтересовались.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="nextActionBlock"
                     style="width: 49.5%; display: inline-block; vertical-align: top; text-align: center;">
                    <div class="blockHeader">Что дальше?</div>
                    <a class="button" href="#"><span style="font-size: 1.1em;">Подайте заявку</span><br><span
                        style="font-size: 0.9em;">наш специалист свяжется с Вами</span></a>
                </div>

                <div class="clearBoth"></div>
            </div>
            <!-- /end.tabs-1 -->

            <div id="tabs-2">
                <div class="benefitsBlock" style="width: 49.5%; display: inline-block; vertical-align: top;">
                    <div class="blockHeader">Почему мы - лучший выбор</div>
                    <div class="accordion">
                        <h3><a href="#">Оплата только по факту заселения</a></h3>

                        <div class="accordionContentUnit">
                            <p>
                                Мы не берем никаких авансов за свои услуги. Только после того, как Вы выберете
                                подходящий объект недвижимости, посмотрите его вживую и при нашей поддержке заключите
                                договор аренды с собственником, мы попросим выплатить соответствующую комиссию.
                            </p>
                        </div>
                        <h3><a href="#">Подробная и достоверная информация по каждому объекту</a></h3>

                        <div class="accordionContentUnit">
                            <p>
                                Первый раз для каждого объекта недвижимости объявление формируется специалистом нашей
                                компании во время осмотра. Вторая и последующие публикации осуществляются после
                                редактирования (актуализации) объявления самим собственником, либо сотрудником нашей
                                компании.
                            </p>

                            <p>
                                Это гарантирует достоверность сведений и достаточную степень подробности каждого
                                объявления.
                            </p>
                        </div>
                        <h3><a href="#">Низкий размер комиссии</a></h3>

                        <div class="accordionContentUnit">
                            <p>
                                Размер нашей комиссии составляет 30% от месячной стоимости аренды. Это в 2 раза меньше,
                                чем средняя цена на такие услуги в городе (от 50% до 100%).
                            </p>
                        </div>
                        <h3><a href="#">Эксклюзивные предложения от собственников</a></h3>

                        <div class="accordionContentUnit">
                            <p>
                                Многие из объявлений сдаются эксклюзивно через нашу компанию, Вы их не найдете на других
                                ресурсах.
                            </p>
                        </div>
                        <h3><a href="#">Гарантия юридической безопасности сделки</a></h3>

                        <div class="accordionContentUnit">
                            <p>
                                Прежде чем объявление будет первый раз опубликовано, наши специалисты проверяют
                                документы собственника, подтверждающие его право сдавать данную недвижимость. Во время
                                подписания договора аренды, документы проверяются повторно.
                            </p>

                            <p>
                                Кроме того, договор аренды составляется с учетом лучших практик в этой области.
                            </p>
                        </div>
                    </div>
                </div>
                <!-- /end.benefitsBlock -->

                <div class="nextActionBlock"
                     style="width: 49.5%; display: inline-block; vertical-align: top; text-align: center;">
                    <div class="blockHeader">Что дальше?</div>
                    <a class="button" href="registration.php"><span
                        style="font-size: 1.1em;">Зарегистрируйтесь</span><br><span style="font-size: 0.9em;">и получите новые возможности</span></a>
                    <ul style="text-align: left;">
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
                    или воспользуйтесь <a href="search.php">Поиском недвижимости</a>
                </div>

                <div class="clearBoth"></div>
            </div>
            <!-- /end.tabs-2 -->
        </div>
        <!-- /end.tabs -->
    </div>
    <!-- /end.page_main_content -->

    <!-- Блок для прижатия подвала к низу страницы без закрытия части контента, его CSS высота доллжна быть = высоте футера -->
    <div class="page-buffer"></div>
</div>
<!-- /end.page_without_footer -->
<div class="footer">
    2012 «Хани Хом», вопросы и пожелания по работе портала можно передавать по телефону 8-922-143-16-15
</div>
<!-- /end.footer -->

<!-- JavaScript at the bottom for fast page loading: http://developer.yahoo.com/performance/rules.html#js_bottom -->
<script src="js/main.js"></script>
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