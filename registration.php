<?php
    // Стартуем сессию с пользователем - сделать доступными переменные сессии
    session_start();

    // Подключаем нужные модели и представления
    include 'models/GlobFunc.php';
    include 'models/Logger.php';
    include 'models/IncomingUser.php';
    include 'views/View.php';
    include 'models/User.php';

    // Создаем объект-хранилище глобальных функций
    $globFunc = new GlobFunc();

    // Подключаемся к БД
    $DBlink = $globFunc->connectToDB();
    // Удалось ли подключиться к БД?
    if ($DBlink == FALSE) die('Ошибка подключения к базе данных (. Попробуйте зайти к нам немного позже.');

    // Инициализируем модель для запросившего страницу пользователя
    $incomingUser = new IncomingUser($globFunc, $DBlink);

    // Проверим, быть может пользователь уже авторизирован. Если это так, перенаправим его на главную страницу сайта
    if ($incomingUser->login()) {
        header('Location: personal.php');
    }

    // Инициализируем полную модель неавторизованного пользователя
    $user = new User($globFunc, $DBlink, $incomingUser);

    // Инициализируем переменную для сохранения ошибок, связанных с регистрационными данными пользователя, и других ошибок, которые не позволили успешно закончить его регистрацию
    $errors = array();

    // Готовим массив со списком районов в городе пользователя
    $allDistrictsInCity = $globFunc->getAllDistrictsInCity("Екатеринбург");

    /********************************************************************************
     * ОТПРАВЛЕНА ФОРМА РЕГИСТРАЦИИ
     *******************************************************************************/

    if (isset($_POST['submitButton'])) {

        // Записываем POST параметры в параметры объекта пользователя
        $user->writeCharacteristicFromPOST();
        $user->writeFotoInformationFromPOST();
        $user->writeSearchRequestFromPOST();

        // Проверяем корректность данных пользователя. Функции userDataCorrect() возвращает пустой array, если введённые данные верны и array с описанием ошибок в противном случае
        $errors = $user->userDataCorrect("registration");

        // Если данные, указанные пользователем, корректны, запишем их в базу данных
        if (is_array($errors) && count($errors) == 0) {

            $correctSaveCharacteristicToDB = $user->saveCharacteristicToDB("new");

            // Если сохранение Личных данных пользователя прошло успешно, то считаем, что пользователь уже зарегистрирован, выполняем сохранение в БД остальных данных (фотографии и поисковый запрос)
            if ($correctSaveCharacteristicToDB) {

                // Узнаем id пользователя - необходимо при сохранении информации о фотке в постоянную базу
                $user->getIdUseLogin();

                // Сохраним информацию о фотографиях пользователя
                // Функция вызывать необходимо независимо от того, есть ли в uploadedFoto информация о фотографиях или нет, так как при регистрации пользователь мог сначала выбрать фотографии, а затем их удалить. В этом случае $this->saveFotoInformationToDB почистит БД и серве от удаленных пользователем файлов
                $user->saveFotoInformationToDB();

                // Сохраняем поисковый запрос, если пользователь регистрируется в качестве арендатора
                if ($incomingUser->isTenant()) {
                    $user->saveSearchRequestToDB("new");
                }

                /******* Авторизовываем пользователя *******/
                $correctEnter = $incomingUser->enter();
                if (count($correctEnter) == 0) //если нет ошибок, отправляем уже авторизованного пользователя на страницу успешной регистрации
                {
                    header('Location: successfullRegistration.php');
                } else {
                    // TODO:что-то нужно делать в случае, если возникли ошибки при авторизации во время регистрации - как минимум вывести их текст во всплывающем окошке
                }

            } else { // Если сохранить личные данные пользователя в БД не удалось

                $errors[] = 'К сожалению, при сохранении данных произошла ошибка: проверьте, пожалуйста, еще раз корректность Вашей информации и повторите попытку регистрации';
                // Сохранении данных в БД не прошло - пользователь не зарегистрирован
            }

        }
    }

    /********************************************************************************
     * ФОРМИРОВАНИЕ ПРЕДСТАВЛЕНИЯ (View)
     *******************************************************************************/

    $view = new View($globFunc, $DBlink);
    $view->generate("templ_registration.php", array('userCharacteristic' => $user->getCharacteristicData(),
                                                    'userFotoInformation' => $user->getFotoInformationData(),
                                                    'userSearchRequest' => $user->getSearchRequestData(),
                                                    'errors' => $errors,
                                                    'allDistrictsInCity' => $allDistrictsInCity,
                                                    'isLoggedIn' => $incomingUser->login(),
                                                    'whatPage' => "forPersonalPage"));

    /********************************************************************************
     * Закрываем соединение с БД
     *******************************************************************************/

    $globFunc->closeConnectToDB($DBlink);