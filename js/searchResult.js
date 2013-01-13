/**********************************************************************************
 * Высота карты == высоте окошка браузера
 **********************************************************************************/

$(document).ready(changeMapSize);
$(window).resize(changeMapSize);
$('#tabs').bind('tabsshow', changeMapSize);

function changeMapSize() {

    // Подстраиваем высоту карты под высоту окна браузера document.documentElement.clientHeight + 'px'
    $('#map').height($(window).height());
    $('#resultOnSearchPage').css('min-height', $(window).height() + 'px');

    // Значения этих переменных пригодятся, когда карта получит положение fixed и ее размеры будут определятся уже относительно окна браузера. При этом размеры и положение карты должны остаться теми же самыми.
    // Исходим из того, что положение и ширина карты соотносятся с таблицей с краткими сведениями об объектах
    mapWidth = $("#shortListOfRealtyObjects").width();
    mapLeftCoord = $("#shortListOfRealtyObjects").offset().left + mapWidth;
}

/**********************************************************************************
 * Зафиксируем карту на всю высоту окошка браузера при прокрутке экрана
 **********************************************************************************/

/* Навешиваем обработчик на прокрутку экрана с целью зафиксировать карту и заголовок таблицы в случае достижения ими верха страницы */

var map = document.getElementById("map");
var mapWrapper = document.getElementById("resultOnSearchPage");
var mapWidth = 0;
var mapLeftCoord = 0;

function changeMapPosition() {

    if (!$("#listPlusMap a").hasClass("inUse")) return true;

// Если экран опустился ниже верхней границы карты, но карта не дошла до футера, то fixedTopBlock

    if (getPageScroll().top <= getCoords(mapWrapper).top) { // Если мы смотрим заголовок страницы

        $(map).removeClass('fixedTopBlock');
        $(map).removeClass('absoluteBottomBlock');

        // Возвращаем исходные значения
        $(map).css({'width':'', 'left':''});

    } else { // Если мы проматали ниже заголовка страницы и верх карты достиг верха экрана

        if (getPageScroll().top + map.offsetHeight >= getCoords(mapWrapper).top + mapWrapper.offsetHeight) { // Если мы дошли до подвала страницы

            $(map).addClass('absoluteBottomBlock');
            $(map).removeClass('fixedTopBlock');

            // Возвращаем исходные значения
            $(map).css({'width':'', 'left':''});

        } else { // Если мы просматриваем середину списка - фиксируем карту на экране

            $(map).addClass('fixedTopBlock');
            $(map).removeClass('absoluteBottomBlock');

            // Важно оставить карту на экране в том же местоположении и той же ширины, что она была до прокрутки
            $(map).css({'width':mapWidth, 'left':mapLeftCoord});

        }

    }

    return true;
}

$(window).scroll(changeMapPosition);

/**********************************************************************************
 * Подгрузка новых 20-ти объектов при прокрутке экрана со списком
 **********************************************************************************/

// Инициализируем переменную, которая при выполнении загрузки новых объявлений не дает повторно срабатывать событию прокрутки. В противном случае одни и те же объекты могут подгрузиться 2 раза.
var blockOfScrollHandler = false;

function getNextRealtyObjects(lastRealtyObjectsId, lastNumber) {

    // Блокируем обработку прокрутки при загрузке данных с сервера
    blockOfScrollHandler = true;

    // Проверяем корректность исходных данных
    if (typeof lastRealtyObjectsId === "undefined" || typeof lastNumber === "undefined") return false;
    if (allProperties.length == 0) return false;

    // Инициализируем массив для сохранения id объектов, по которым нам нужно получить с сервера данные
    var propertyIdArr = new Array();

    // Ищем позицию на которой в общем массиве стоит последний элемент, по которому загружены полные данные
    var lastRealtyPosition = 0;
    while (allProperties[lastRealtyPosition]['id'] != lastRealtyObjectsId) {
        // Дошли до конца массива и не нашли..
        if (lastRealtyPosition == allProperties.length - 1) return false;
        lastRealtyPosition++;
    }

    // Вычисляем массив идентификаторов объектов недвижимости, по которым запросим у сервера полные данные (это следующие 20 объектов или все до конца массива, если до конца массива осталось меньше 20 членов)
    var i = 1;
    while (i <= 20 && lastRealtyPosition + i < allProperties.length) {
        propertyIdArr.push(allProperties[lastRealtyPosition + i]['id']);
        i++;
    }

    // Запускаем вертушку, чтобы показать пользователю, что новые данные подгружаются
    if ($("#shortListOfRealtyObjects").is(":visible")) $("#upBlockShortList").css('display', 'block');
    if ($("#fullParametersListOfRealtyObjects").is(":visible")) $("#upBlockFullList").css('display', 'block');

    jQuery.post("../AJAXGetSearchResult.php", {"propertyId":propertyIdArr, "typeOperation":"FullData", "number":lastNumber}, function (data) {

        // Дополняем таблицы, содержащие списки с краткими и подробными объявлениями
        if (data.matterOfShortList != "") $("#shortListOfRealtyObjects").append(data.matterOfShortList);
        if (data.matterOfFullParametersList != "") $("#fullParametersListOfRealtyObjects").append(data.matterOfFullParametersList);

        // Присваиваем полученный HTML соответствующим баллунам, если они не были созданы ранее
        for (i = 0; i < propertyIdArr.length; i++) {
            if (!$("#allBalloons .balloonBlock[propertyId='" + propertyIdArr[i] + "']").length && typeof data.arrayOfBalloonList[propertyIdArr[i]] != "undefined") {
                $("#allBalloons").append(data.arrayOfBalloonList[propertyIdArr[i]]);
            }
        }

        // Убираем вертушку - новый контент для списков подгружен
        $("#upBlockShortList").css('display', 'none');
        $("#upBlockFullList").css('display', 'none');

        // Актуализируем местоположение карты. Чтобы карта не сдвигалась после окончания загрузки новых блоков с данными в списки
        changeMapPosition();

        /************* Активируем ColorBox для просмотра в модальном окне галереи фотографий по клику на миниатюре *************/
        // Это необходимо сделать для вновь загруженных объектов
        for (i = 0; i < propertyIdArr.length; i++) {

            /* Для представления результатов поиска в виде карты с баллунами */
            $("#allBalloons .balloonBlock[propertyId='" + propertyIdArr[i] + "'] .gallery").colorbox({ opacity:0.7, rel:currentFotoGalleryIndex, current:'№ {current} из {total}' });
            currentFotoGalleryIndex++;

            /* Для представления результатов поиска список + карта */
            $("#shortListOfRealtyObjects .realtyObject[propertyId='" + propertyIdArr[i] + "'] .gallery").colorbox({ opacity:0.7, rel:currentFotoGalleryIndex, current:'№ {current} из {total}' });
            currentFotoGalleryIndex++;

            /* Для представления результатов поиска в виде списка */
            $("#fullParametersListOfRealtyObjects .realtyObject[propertyId='" + propertyIdArr[i] + "'] .gallery").colorbox({ opacity:0.7, rel:currentFotoGalleryIndex, current:'№ {current} из {total}' });
            currentFotoGalleryIndex++;

        }

        // Разблокируем обработку прокрутки страницы для новых загрузок с сервера
        blockOfScrollHandler = false;

    }, 'json');

    return true;
}

$(window).scroll(function () {

    // Если мы дожидаемся окончания прошлого запроса к серверу, то новые не отправляются
    if (blockOfScrollHandler) return true;

    // Если ни один из списков объектов недвижимости не является видимым (например, при просмотре пользователем результатов поиска в режиме карты), то и обрабатывать прокрутку экрана не нужно
    if ($("#shortListOfRealtyObjects").is(":hidden") && $("#fullParametersListOfRealtyObjects").is(":hidden")) return true;

    var screenHeight = $(window).height(); // Высота экрана пользователя
    var currentTopScroll = $(this).scrollTop(); // Текущая промотка экрана (количество пикселей, скрытое вверху страницы)
    // Текущая координата низа экрана относительно всего документа
    var currentScreenBottom = screenHeight + currentTopScroll;

    // Вычисляем координату низа списка с объявлениями относительно всего документа. Либо подробного, либо с минимумом сведений - в зависимости от того, какой режим отображения результатов поиска выбран
    if ($("#shortListOfRealtyObjects").is(":visible")) {
        var listOfRealtyObjectsBottom = $("#shortListOfRealtyObjects").height() + $("#shortListOfRealtyObjects").offset().top;
        var lastRealtyObjectsId = $("#shortListOfRealtyObjects .realtyObject:last").attr('propertyId');
        var lastNumber = $("#shortListOfRealtyObjects .realtyObject:last .numberOfRealtyObject").html();
    }
    if ($("#fullParametersListOfRealtyObjects").is(":visible")) {
        listOfRealtyObjectsBottom = $("#fullParametersListOfRealtyObjects").height() + $("#fullParametersListOfRealtyObjects").offset().top;
        lastRealtyObjectsId = $("#fullParametersListOfRealtyObjects .realtyObject:last").attr('propertyId');
        lastNumber = $("#fullParametersListOfRealtyObjects .realtyObject:last .numberOfRealtyObject").html();
    }

    // Сколько пикселей осталось промотать пользователю, чтобы достигнуть низа списка объектов
    var leftHeight = listOfRealtyObjectsBottom - currentScreenBottom;

    if (leftHeight < 400) { // Если до низа осталось меньше 400 пикселей - пытаемся подгрузить продолжение списка с сервера

        // Дошли до конца списка?
        if (allProperties[allProperties.length - 1]['id'] == lastRealtyObjectsId) return true;

        getNextRealtyObjects(lastRealtyObjectsId, lastNumber);
    }

    return true;
});

/**********************************************************************************
 * Инициализация карты при загрузке страницы
 **********************************************************************************/

ymaps.ready(init);

function init() {
    // Создание экземпляра карты и его привязка к контейнеру с заданным id ("map")
    var map = new ymaps.Map('map', {
        center:[56.829748, 60.617435], // В центре карты - Екатеринбург
        zoom:11, // Коэффициент первоначального масштабирования
        behaviors:['default', 'scrollZoom', 'ruler'] // Включим поведения по умолчанию (default), а также масштабирование колесом мыши (scrollZoom) и измеритель расстояний по клику левой кнопки мыши (ruler)
    });

    /***** Добавляем элементы управления на карту *****/
    map.controls.add('typeSelector'); // Выбор типа карты
    map.controls.add('smallZoomControl', { // Кнопка изменения масштаба - компактный вариант. Расположим её ниже и левее левого верхнего угла
        left:5,
        top:55
    });
    map.controls.add('mapTools'); // Стандартный набор кнопок

    // Создаем макет для правой части баллуна кластера
    var MyMainContentLayout = ymaps.templateLayoutFactory.createClass('', {

        build:function () {
            // Сначала вызываем метод build родительского класса.
            MyMainContentLayout.superclass.build.call(this);
            // Нужно отслеживать, какой из пунктов левого меню выбран, чтобы обновлять содержимое правой части.
            this.stateListener = this.getData().state.events.group().add('change', this.onStateChange, this);
            this.activeObject = this.getData().state.get('activeObject');
            this.applyContent();
        },

        clear:function () {
            // Снимаем слушателей изменения полей.
            this.stateListener.removeAll();
            // А затем вызываем метод clear родительского класса.
            MyMainContentLayout.superclass.clear.call(this);
        },

        onStateChange:function () {
            // При изменении одного из полей состояния проверяем, не сменился ли активный объект.
            var newActiveObject = this.getData().state.get('activeObject');
            if (newActiveObject != this.activeObject) {
                // Если объект изменился, нужно обновить содержимое правой части.
                this.activeObject = newActiveObject;
                this.applyContent();
            }
        },

        applyContent:function () {
            balloonHTML = getContentForBalloonFromPage(this.activeObject.properties.get('propertyid')); //TODO: пока не получаем данные с сервера
            this.activeObject.properties.set('balloonContentBody', balloonHTML);
            // Для того, чтобы макет автоматически изменялся при обновлении данных
            // в геообъекте, создадим дочерний макет через фабрику
            var subLayout = new MyMainContentSubLayout({
                options:this.options,
                properties:this.activeObject.properties
            });

            // прицепим новый макет к родителю
            subLayout.setParentElement(this.getParentElement());
        }
    });

    // А вот и сам дочерний макет - он принимает на вход данные текущего выбранного геообъекта и показывает их.
    var MyMainContentSubLayout = ymaps.templateLayoutFactory.createClass(
        '<div width="100">' +
            '$[properties.balloonContentBody]' +
        '</div>'
    );

    // Рисуем на карте маркеры объектов недвижимости, соответствующих запросу
    placeMarkers();

    // Перестроение карты при различных событиях
    $('#expandMap').bind('click', reDrawMap);
    $('#listPlusMap').bind('click', reDrawMap);
    // Чтобы карта отображалась при открытии вкладки (Избранное в Личном кабинете), ее нужно перестраивать по событию - открытие вкладки
    if ($("#tabs #map").length) {
        $('#tabs').bind('tabsshow', function (event, ui) {
            map.setCenter([56.829748, 60.617435]);
            map.container.fitToViewport();
        });
    }

    /***** Функция перестроения карты - используется при изменении размеров блока *****/
    function reDrawMap() {
        //map.setCenter([56.829748, 60.617435]);
        map.container.fitToViewport();
    }

    function placeMarkers() {

        // Создаем кластеризатор. Который будет объединять в 1 метку близко расположенные метки и будет масштабироваться по клику
        cluster = new ymaps.Clusterer();
        // Задаем размер ячейки кластера в пикселях. Чем больше, тем при большем расстояние между друг другом метки будут объединяться в одну
        cluster.options.set({
            // TODO: test
            clusterBalloonMainContentLayout:MyMainContentLayout, // зададим макет для правой части балуна
            //clusterBalloonSidebarWidth: 75, // Настроим ширину левой части балуна кластера
            //clusterBalloonWidth: 450, // И ширину балуна неделимого кластера целиком
            gridSize:40
        });

        // Инициализируем массив меток, которые нужно добавить на карту
        placemarks = [];

        // Перебираем данные для всех баллунов и готовим соответствующие метки на карту
        for (var i = 0; i < allProperties.length; i++) {

            // Создаем метку на основе координат
            myPlacemark = new ymaps.Placemark([allProperties[i]['coordX'], allProperties[i]['coordY']], {
                propertyid:allProperties[i]['id'],
                clusterCaption:'Вариант', // Если координаты метки совпадут с координатами другой метки, то по клику на их кластер должен открываться баллун с описанием обоих объявлений
                balloonContentBody:'Загрузка данных...' // Текст для индикации процесса загрузки (будет заменен на контент когда данные загрузятся)
            });

            // Навешиваем обработчик клика по метке
            myPlacemark.events.add('click', onPlacemarkClick);

            // Добавляем полученную метку в коллекцию. Перед этим можно добавить проверку на удачность создания метки, чтобы всю страницу не запароть из-за одной косячной метки
            placemarks[i] = myPlacemark;
        }

        // Добавляем собранную коллекцию меток в кластер и на карту
        cluster.add(placemarks);
        map.geoObjects.add(cluster);
    }

    // Обработчик клика по метке на Яндекс карте
    function onPlacemarkClick(event) {

        // Получаем параметры метки, в том числе id объекта недвижимости.
        var placemark = event.get('target'),
            map = placemark.getMap(), // Ссылка на карту.
            //bounds = map.getBounds(), // Область показа карты.
            propertyid = placemark.properties.get('propertyid'); // Получаем данные для запроса из свойств метки.

        // Пытаемся найти контент для баллуна на нашей текущей странице в разделе AllBalloons
        balloonHTML = getContentForBalloonFromPage(propertyid);

        // Проверяем - есть ли на странице данные для формирования баллуна для этого объекта, если есть, формируем на основе их баллун
        if (balloonHTML) {

            // Обновляем поле "body" у properties метки
            placemark.properties.set('balloonContentBody', balloonHTML);
            setColorBoxForOpenBalloon();

        } else { // Если данные по этому объекту еще не были подгружены на страницу, то обращаемся к серверу

            // Обращаемся к серверу за HTML баллуна, передаем серверу propertyid - идентификатор объекта недвижимости
            jQuery.post("../AJAXGetSearchResult.php", {"propertyId":new Array(propertyid), "typeOperation":"FullBalloons"}, function (data) {

                //TODO: мы можем получить пустой массив data.arrayOfBalloonList, нужно проверять, что возможно обращение к данному элементу, иначе выдавать ошибку запроса в баллуне, чтобы пользователь понял, что ждать нечего
                balloonHTML = data.arrayOfBalloonList[propertyid];

                // Проверка на адекватность полученного HTML для баллуна
                if (balloonHTML != "") {

                    // Обновляем поле "body" у properties метки
                    placemark.properties.set('balloonContentBody', balloonHTML);
                    setColorBoxForOpenBalloon();

                    // Также в случае успеха, сохраняем данные по баллуну для данного объекта на странице с целью уменьшения количества запросов к серверу
                    $("#allBalloons").append(balloonHTML);

                } else {
                    // Сообщаем об ошибке получения данных объявления с сервера
                    placemark.properties.set('balloonContentBody', "Не удалось найти данные по этому объявлению");
                }

            }, 'json');

        }

    }

    // Пытается найти HTML содержимое для баллуна на странице
    function getContentForBalloonFromPage(propertyid) {
        balloonHTML = $("#allBalloons .balloonBlock[propertyId='" + propertyid + "']").html();
        return balloonHTML;
    }

    // Объединяет в галерею ColorBox фотографии открытого баллуна (нужно запускать каждый раз при открытии нового баллуна)
    function setColorBoxForOpenBalloon() {
        $("#map .fotosWrapper .gallery").removeClass('cboxElement').colorbox({ opacity:0.7, rel:currentFotoGalleryIndex, current:'№ {current} из {total}' });
        currentFotoGalleryIndex++;
    }

    // TODO: test
    /*map.geoObjects.events.add('click', onClickHandler);
     function onClickHandler(event) {
     alert("Получилось");
     /*currentGeoObject = event.get('target');
     if (currentGeoObject.options.getName() == "cluster") {
     // Проверить масштаб, если минимальный
     // Перебираем объявления в кластере и получаем их данные
     arr = currentGeoObject.properties.get('geoObjects');
     for (i = 0; i < arr.length; i++) {
     arr[i].properties.set('balloonContentBody', "Тестовое значение!");
     }
     }
     }*/

    /* Вешаем обработчик на клик по строчке краткого списка - чтобы отобразить инфу в виде баллуна на карте */
    $('#shortListOfRealtyObjects').on('click', ".realtyObject", function (event) {
        var target = event.target;

        var propertyId = $(this).attr('propertyId');
        var balloonContentBodyVar = getContentForBalloonFromPage(propertyId);
        var realtyObjCoordX = $("#allBalloons .balloonBlock[propertyId='" + propertyId + "']").attr('coordX');
        var realtyObjCoordY = $("#allBalloons .balloonBlock[propertyId='" + propertyId + "']").attr('coordY');

        map.balloon.open(
            [realtyObjCoordX, realtyObjCoordY], // Позиция балуна
            { contentBody:balloonContentBodyVar } // Свойства балуна
        );

        // Навешиваем на фотографии только что сформированного HTML баллуна галерею colorBox
        setColorBoxForOpenBalloon();

        return true; // чтобы дать возможность отработать и другим обработчикам клика (например, для добавления/удаления в избранное, просмотра объявления подробнее)
    });

}

/**********************************************************************************
 * Переход на страницу с подробным описанием недвижимости по клику в режиме "только список"
 **********************************************************************************/

$("#fullParametersListOfRealtyObjects").on('click', '.realtyObject', function (event) {

    // Если клик был по ссылке "адрес объекта", то описание объекта откроется в новой вкладке и без этого обработчика
    // Чтобы избежать открытия второй вкладки с описанием объекта, не будем выполнять аналогичное ссылке действие в обработчике
    if ($(event.target).hasClass("linkToDescription")) return true;

    // Открываем подробное описание объекта в новом окне
    var linkToDescription = "property.php?propertyId=" + $(this).attr('propertyId');
    window.open(linkToDescription);

    return true;
});

/**********************************************************************************
 * Устанавливаем режим просмотра результатов поиска по умолчанию, а также возможность его переключения
 **********************************************************************************/

$('#expandList a').removeClass('inUse');
$('#listPlusMap a').addClass('inUse');
$('#expandMap a').removeClass('inUse');

/* Событие клика по ссылке развернуть список */
$('#expandList').on('click', function () {
    $('#shortListOfRealtyObjects').css('display', 'none');
    $('#map').css('display', 'none');
    $('#fullParametersListOfRealtyObjects').css('display', '');
    $('#expandList a').addClass('inUse');
    $('#listPlusMap a').removeClass('inUse');
    $('#expandMap a').removeClass('inUse');
    return false;
});

/* Событие клика по ссылке список + карта*/
$('#listPlusMap').on('click', function () {
    $('#shortListOfRealtyObjects').css('display', '');
    $('#map').css('display', '');
    $('#map').css('width', '50%');
    $('#fullParametersListOfRealtyObjects').css('display', 'none');
    $('#expandList a').removeClass('inUse');
    $('#listPlusMap a').addClass('inUse');
    $('#expandMap a').removeClass('inUse');
    return false;
});

/* Событие клика по ссылке развернуть карту*/
$('#expandMap').on('click', function () {
    $('#shortListOfRealtyObjects').css('display', 'none');
    $('#map').css('display', '');
    $('#map').css('width', '100%');
    $('#fullParametersListOfRealtyObjects').css('display', 'none');
    $('#expandList a').removeClass('inUse');
    $('#listPlusMap a').removeClass('inUse');
    $('#expandMap a').addClass('inUse');
    return false;
});