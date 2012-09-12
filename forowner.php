<!DOCTYPE html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!-- Consider specifying the language of your content by adding the `lang` attribute to <html> -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
	<!--<![endif]-->
	<head>
		<meta charset="utf-8">

		<!-- Use the .htaccess and remove these lines to avoid edge case issues.
		More info: h5bp.com/i/378 -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title>Как мы работаем с собственником</title>
		<meta name="description" content="Как мы работаем с собственником">

		<!-- Mobile viewport optimized: h5bp.com/viewport -->
		<meta name="viewport" content="initialscale=1.0, width=device-width">

		<!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

		<link rel="stylesheet" href="css/jquery-ui-1.8.22.custom.css">
		<link rel="stylesheet" href="css/main.css">
		<style>
			/* Стиль для панели табов - ul*/
			/* Важно, что пока он плавающий и основной текст из .ui-tabs-panel его обтекает*/
			.ui-tabs-vertical .ui-tabs-nav {
				padding: .2em .1em .2em .2em;
				float: left;
			}

			/* Стиль для не выбранного таба - элемента li*/
			.ui-tabs-vertical .ui-tabs-nav li {
				clear: left;
				width: 100%;
				border-bottom-width: 1px !important;
				border-right-width: 0 !important;
				margin: 0px 0px 0px 0px;
				padding: 0;
				cursor: pointer;
			}
			.ui-tabs-vertical .ui-tabs-nav li a {
				display: block;
			}
			/* Стиль для текущего выбранного таба - элемента li*/
			.ui-tabs-vertical .ui-tabs-nav li.ui-tabs-selected {
				margin: 0px 0px 0px 0px;
				padding: 0;
				cursor: text;
			}
			/* Стиль для основного текста, отображаемого справа от табов*/
			.ui-tabs-vertical .ui-tabs-panel {
				padding: 1em;
				overflow: auto;
			}
		</style>
	</head>

	<body>
		<div class="page_without_footer">
            <!-- Сформируем и вставим заголовок страницы -->
            <?php
                include("header.php");
            ?>

			<div class="page_main_content">

				<div class="wrapperOfTabs">
					<div class="headerOfPage">
						Современный способ сдать недвижимость - с Хани Хом за 9 шагов
					</div>
					<div id="tabs">
						<ul>
							<li>
								<a href="#tabs-1">1. Заявка</a>
							</li>
							<li>
								<a href="#tabs-2">2. Выезд специалиста</a>
							</li>
							<li>
								<a href="#tabs-3">3. Оплата</a>
							</li>
							<li>
								<a href="#tabs-4">4. Публикация</a>
							</li>
							<li>
								<a href="#tabs-5">5. Выбор арендатора</a>
							</li>
							<li>
								<a href="#tabs-6">6. Показ</a>
							</li>
							<li>
								<a href="#tabs-7">7. Договор</a>
							</li>
							<li>
								<a href="#tabs-8">8. Расчет</a>
							</li>
							<li>
								<a href="#tabs-9">9. Успех!</a>
							</li>
						</ul>
						<div id="tabs-1">
							<p>
								Оставьте заявку на выезд специалиста ниже на нашем сайте, либо по тел: 8-922-143-16-15.
							</p>
							<p>
								После получения заявки в течение рабочего дня с Вами свяжется оператор и уточнит время встречи со специалистом Хани Хом.
							</p>
						</div>
						<div id="tabs-2">
							<p>
								В условленное с Вами время приедет наш специалист и выполнит следующие работы:
								<ul>
									<li>
										Подробно зафиксирует параметры сдаваемого объекта
									</li>
									<li>
										Сфотографирует комнаты и другие помещения
									</li>
									<li>
										Сформирует для Вас договор аренды, который нужно будет подписать с арендатором
									</li>
									<li>
										Проконсультирует по любым вопросам, связанным со сдачей объекта в аренду
									</li>
									<li>
										Зафиксирует предъявляемые Вами требования к арендаторам
									</li>
									<li>
										Все работы будут подтверждены договором на оказание услуг, который с Вами подпишет специалист
									</li>
								</ul>
							</p>
						</div>
						<div id="tabs-3">
							<p>
								После выполнения работ Вам нужно будет оплатить услуги специалиста. Стоимость составляет 15% от месячной цены аренды объекта.
							</p>
							<p>
								Не беспокойтесь: работая с нами Вы не только вернете эти деньги, но и получите дополнительный заработок. Арендатор, привлекаемый ресурсом Хани Хом, при подписании договора выплатит Вам не только арендную плату за первый месяц, но и единоразовую комиссию в размере 30% от месячной стоимости аренды объекта. Это условие прописывается в договоре аренды, сформированным нашим специалистом.
							</p>
							<p>
								Пример: Вы хотите сдать квартиру за 20 000 руб./мес. Стоимость услуг нашего специалиста составит 15% от 20 000 = 3 000 руб.
								Арендатор при подписании договора выплатит Вам единоразовую комиссию 30% от 20 000 = 6 000 рублей.
								Таким образом, сдавая недвижимость с нашей помощью, Вы заработаете дополнительно 6 000 - 3 000 = 3 000 рублей.
							</p>
						</div>
						<div id="tabs-4">
							<p>
								Информация о Вашем объекте ежедневно перепубликуется на основных порталах города: e1.ru, 66.ru. Это обеспечивает привлечение максимального количества соответствующих Вашему запросу арендаторов, из которых Вы сможете выбрать наиболее достойного.
							</p>
						</div>
						<div id="tabs-5">
							<p>
								Информация о каждом заинтересовавшемся потенциальном арендаторе сохраняется для Вас в личном кабинете на портале (возраст, работа, образование, наличие детей, животных и т.д.). Это позволяет Вам лично выбрать наиболее подходящего нанимателя для своей недвижимости.
							</p>
							<p>
								В отличие от обычных агентств мы стараемся представить Вам максимум информации о желающих снять недвижимость еще до показа, чтобы сэкономить и Ваше время, и время арендаторов.
							</p>
						</div>
						<div id="tabs-6">
							<p>
								Каждый потенциальный арендатор, заинтересовавшийся объявлением, после отправки нам запроса получит Ваш контактный телефон (а Вы информацию об этом арендаторе в личном кабинете на сайте).
							</p>
							<p>
								Ознакомившись с информацией о потенциальном арендаторе в личном кабинете, Вы сможете получить представление об этом человеке и принять решение: готовы ли Вы ему сдать свою недвижимость. Если да, то Вам останется договориться с ним о времени показа объекта.  
							</p>
							<p>
								Удобно, что, договариваясь о времени показа, Вам с арендатором совершенно не нужно ориентироваться на свободное время наших сотрудников, так как они не участвуют в показе.  
							</p>
							<p>
								Зачастую достаточно одного показа, так как и наниматель достаточно много знает о Вашем объекте для принятия решения об его аренде, и Вы достаточно много знаете об арендаторе, познакомившись с информацией о нем, в том числе с фотографиями этого человека, в личном кабинете на нашем сайте.  
							</p>
						</div>
						<div id="tabs-7">
							<p>
								После показа, если и Вы, и арендатор остались довольны, нужно подписать с нанимателем договор аренды. Бланк договора был подготовлен для Вас еще во время визита нашего специалиста, необходимо лишь дозаполнить поля с данными арендатора и подписать документ.
							</p>
						</div>
						<div id="tabs-8">
							<p>
								При подписании договора производится расчет с нанимателем: кроме платы за первый месяц проживания и залога (если Вы его требуете от арендатора), наниматель, согласно договору аренды, выплатит Вам единоразовую комиссию - 30% от месячной стоимости аренды объекта. Данная комиссия призвана покрыть расходы на услуги нашего специалиста, а также принести Вам дополнительный доход - бонус за работу с нашей компанией.
							</p>
						</div>
						<div id="tabs-9">
							<p>
								Поздравляем, Вы сдали недвижимость порядочным людям максимально быстро, кроме того, получив дополнительный доход, с помощью Хани Хом!
							</p>
						</div>
					</div>
				</div>

                <form name="requestNewOwner" method="post">
                    <fieldset class="edited" style="margin-top: 15px; float: left;">
                        <legend>
                            Заполните заявку
                        </legend>
                        Как к Вам обращаться?
                        <input type="text" size="30" name="Name">
                        <br>
                        На какой номер Вам перезвонить?
                        <input type="text" size="15" name="telNumber">
                        <br>
                        По какому адресу собираетесь сдать объект?
                        <input type="text" size="40" name="address">
                        <br>
                        Комментарии (например, в какое время Вам будет удобно принять наш звонок)?
                        <br>
                        <textarea rows="3" cols="90" name="comment"></textarea>
                        <div class="clearBoth"></div>
                        <button type="submit" style="float: right; margin-top: 10px;">
                            Отправить заявку
                        </button>
                    </fieldset>
                </form>

			</div><!-- /end.page_main_content -->
			<!-- Блок для прижатия подвала к низу страницы без закрытия части контента, его CSS высота доллжна быть = высоте футера -->
			<div class="page-buffer"></div>
		</div><!-- /end.page_without_footer -->
		<div class="footer">
			2012 «Хани Хом», вопросы и пожелания по работе портала можно передавать по телефону 8-922-143-16-15
		</div><!-- /end.footer -->

		<!-- JavaScript at the bottom for fast page loading: http://developer.yahoo.com/performance/rules.html#js_bottom -->

		<!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>

		<!-- jQuery UI с моей темой оформления -->
		<script src="js/vendor/jquery-ui-1.8.22.custom.min.js"></script>

		<!-- scripts concatenated and minified via build script -->
		<script src="js/main.js"></script>
		<script src="js/forowner.js"></script>

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