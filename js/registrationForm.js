// Вставляем календарь для выбора дня рождения
$(function() {
    $( "#datepicker" ).datepicker({
        changeMonth: true,
        changeYear: true,
        minDate: new Date(1900, 0, 1),
        maxDate: new Date(2004, 11, 31),
        defaultDate: new Date(1987, 0, 27),
        yearRange: "1900:2004",
    });
    $( "#datepicker" ).datepicker($.datepicker.regional["ru"]);

});

// Подготовим возможность загрузки фотографий
function createUploader(){
    var uploader = new qq.FileUploader({
        element: document.getElementById('file-uploader'),
        action: '../lib/uploader.php',
        allowedExtensions: ["jpeg", "jpg", "img", "bmp"], //Также расширения нужно менять в файле uploader.php
        sizeLimit: 10 * 1024 * 1024,
        debug: true,
        // О каждом загруженном файле информацию передаем на сервер через переменные - для сохранения в БД
        onSubmit: function(id, fileName){
            uploader.setParams({
                fileuploadid: $("#fileUploadId").val(),
                sourcefilename: fileName,
            });
        },
        //extraDropzones: [qq.getByClass(document, 'qq-upload-extra-drop-area')[0]]
    });

    // Сформируем зеленые блоки для уже загруженных фотографий руками, чтобы пользователя не путать
    var rezult = {success: true};
    var uploadedFoto = document.getElementsByClassName('uploadedFoto');
    for (var i = 0; i < uploadedFoto.length; i++) {
        var uploadedFotoName = $(uploadedFoto[i]).attr('filename');

        // Формируем зеленый блок в списке загруженных файлов в разделе Фотографии
        uploader._addToList(i + 100, uploadedFotoName);
        uploader._onComplete(i + 100, uploadedFotoName, rezult);
    }

   // Чтобы обмануть загрузчик файлов и он не выдавал при отправке страницы сообщение о том, что мол есть еще не загруженные фотографии, ориентируясь на сформированные вручную зеленые блоки
   uploader._filesInProgress = 0;
}
$(document).ready(createUploader);

/* Если в форме Работа указан чекбокс - не работаю, то блокировать заполнение остальных инпутов */
$("#notWorkCheckbox").on('change', notWorkCheckbox);
$(document).ready(notWorkCheckbox);
function notWorkCheckbox() {
    if ($("#notWorkCheckbox").is(':checked')) {
        $("input.ifWorked").attr('disabled', 'disabled').css('color', 'grey');
        $("div.searchItem.ifWorked div.required").text("");
    } else {
        $("input.ifWorked").removeAttr('disabled').css('color', '');
        $("div.searchItem.ifWorked div.required").text("*");
    }
}

/* Если в форме Образование указан чекбокс - не учился, то блокировать заполнение остальных инпутов */
$("#currentStatusEducation").change(currentStatusEducation);
$(document).ready(currentStatusEducation);
function currentStatusEducation() {
    var currentValue = $("#currentStatusEducation option:selected").attr('value');
    if (currentValue == 0) {
        $("input.ifLearned, select.ifLearned").removeAttr('disabled').css('color', '');
        $("div.searchItem.ifLearned div.required").text("*");
    }
    if (currentValue == 1) {
        $("input.ifLearned, select.ifLearned").attr('disabled', 'disabled').css('color', 'grey');
        $("div.searchItem.ifLearned div.required").text("");
    }
    if (currentValue == 2) {
        $("input.ifLearned, select.ifLearned").removeAttr('disabled').css('color', '');
        $("div.searchItem.ifLearned div.required").text("*");
        $('#kurs').css('display', '');
        $('#yearOfEnd').css('display', 'none');
    }
    if (currentValue == 3) {
        $("input.ifLearned, select.ifLearned").removeAttr('disabled').css('color', '');
        $("div.searchItem.ifLearned div.required").text("*");
        $('#kurs').css('display', 'none');
        $('#yearOfEnd').css('display', '');
    }
}



// Подгонка размера правого блока параметров (районы) расширенного поиска под размер левого блока параметров. 19 пикселей - на padding у fieldset
document.getElementById('rightBlockOfSearchParameters').style.height = document.getElementById('leftBlockOfSearchParameters').offsetHeight - 22 + 'px';

/* Сценарии для появления блока с подробным описанием сожителей */
$("#withWho").on('change', withWho);
$(document).ready(withWho);
function withWho() {
    if ($("#withWho").attr('value') != 0) {
        $("#withWhoDescription").css('display', '');
    } else {
        $("#withWhoDescription").css('display', 'none');
    }
}

/* Сценарии для появления блока с подробным описанием детей */
$("#children").on('change', children);
$(document).ready(children);
function children() {
    if ($("#children").attr('value') != 0) {
        $("#childrenDescription").css('display', '');
    } else {
        $("#childrenDescription").css('display', 'none');
    }
}

/* Сценарии для появления блока с подробным описанием животных */
$("#animals").on('change', animals);
$(document).ready(animals);
function animals() {
    if ($("#animals").attr('value') != 0) {
        $("#animalsDescription").css('display', '');
    } else {
        $("#animalsDescription").css('display', 'none');
    }
}