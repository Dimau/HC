<div class="descriptionFieldsetsWrapper">
    <fieldset class="edited left private">
        <legend>
            ФИО
        </legend>
        <table>
            <tbody>
            <tr>
                <td class="itemLabel">
                    Фамилия
                </td>
                <td class="itemRequired">
                </td>
                <td class="itemBody">
                    <input name="surname" id="surname" type="text" autofocus
                           value='<?php echo $userCharacteristic['surname'];?>'>
                </td>
            </tr>
            <tr>
                <td class="itemLabel">
                    Имя
                </td>
                <td class="itemRequired">
                    *
                </td>
                <td class="itemBody">
                    <input name="name" id="name" type="text" value='<?php echo $userCharacteristic['name']?>'>
                </td>
            </tr>
            <tr>
                <td class="itemLabel">
                    Отчество
                </td>
                <td class="itemRequired">
                </td>
                <td class="itemBody">
                    <input name="secondName" id="secondName" type="text"
                           value='<?php echo $userCharacteristic['secondName'];?>'>
                </td>
            </tr>
            <tr <?php if ($userCharacteristic['typeTenant']) echo 'title="Мы используем указанный Вами пол при поиске недвижимости: некоторые объекты сдаются только девушкам, другие только молодым людям"'; ?>>
                <td class="itemLabel">
                    Пол
                </td>
                <td class="itemRequired">
                </td>
                <td class="itemBody">
                    <select name="sex" id="sex">
                        <option value="0" <?php if ($userCharacteristic['sex'] == "0") echo "selected";?>></option>
                        <option value="мужской" <?php if ($userCharacteristic['sex'] == "мужской") echo "selected";?>>
                            мужской
                        </option>
                        <option value="женский" <?php if ($userCharacteristic['sex'] == "женский") echo "selected";?>>
                            женский
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="itemLabel">
                    Внешность
                </td>
                <td class="itemRequired">
                </td>
                <td class="itemBody">
                    <select name="nationality" id="nationality">
                        <option value="0" <?php if ($userCharacteristic['nationality'] == "0") echo "selected";?>></option>
                        <option
                                value="славянская" <?php if ($userCharacteristic['nationality'] == "славянская") echo "selected";?>>
                            славянская
                        </option>
                        <option
                                value="европейская" <?php if ($userCharacteristic['nationality'] == "европейская") echo "selected";?>>
                            европейская
                        </option>
                        <option
                                value="азиатская" <?php if ($userCharacteristic['nationality'] == "азиатская") echo "selected";?>>
                            азиатская
                        </option>
                        <option
                                value="кавказская" <?php if ($userCharacteristic['nationality'] == "кавказская") echo "selected";?>>
                            кавказская
                        </option>
                        <option
                                value="африканская" <?php if ($userCharacteristic['nationality'] == "африканская") echo "selected";?>>
                            африканская
                        </option>
                    </select>
                </td>
            </tr>
            <tr <?php if ($userCharacteristic['typeTenant']) echo 'title="На основе даты рождения мы автоматически вычисляем Ваш возраст, а им, в свою очередь, интересуются собственники"'; ?>>
                <td class="itemLabel">
                    День рождения
                </td>
                <td class="itemRequired">
                </td>
                <td class="itemBody">
                    <input name="birthday" id="birthday" type="text"
                           placeholder="дд.мм.гггг" value='<?php echo $userCharacteristic['birthday'];?>'>
                </td>
            </tr>
            </tbody>
        </table>
    </fieldset>

    <fieldset class="edited right private" style="display: block;">
        <legend>
            Контакты
        </legend>
        <table>
            <tbody>
            <tr>
                <td class="itemLabel">
                    Телефон
                </td>
                <td class="itemRequired">
                    *
                </td>
                <td class="itemBody">
                    <?php echo $userCharacteristic['telephon'];?>
                </td>
            </tr>
            <tr>
                <td class="itemLabel">
                    E-mail
                </td>
                <td class="itemRequired">
                </td>
                <td class="itemBody">
                    <input type="text" name="email" id="email" value='<?php echo $userCharacteristic['email'];?>'>
                </td>
            </tr>
            <tr>
                <td class="itemLabel">
                    Пароль
                </td>
                <td class="itemRequired">
                    *
                </td>
                <td class="itemBody">
                    <input type="password" name="password" id="password"
                           maxlength="50" value='<?php echo $userCharacteristic['password'];?>'>
                </td>
            </tr>
            </tbody>
        </table>
    </fieldset>

    <fieldset id='fotoWrapperBlock' class="edited fullWidth" style="min-width: 300px;">
        <legend
            <?php if ($userCharacteristic['typeTenant']) echo 'title="Рекомендуем загрузить хотя бы 1 фотографию, которая в выгодном свете представит Вас перед собственником"'; ?>>
            Фотографии
        </legend>
        <input type='hidden' name='fileUploadId' id='fileUploadId'
               value='<?php echo $userFotoInformation['fileUploadId'];?>'>
        <input type='hidden' name='uploadedFoto' id='uploadedFoto' value=''>

        <div id="file-uploader">
            <noscript>
                <p>Пожалуйста, активируйте JavaScript для загрузки файлов</p>
                <!-- or put a simple form for upload here -->
            </noscript>
        </div>
    </fieldset>
    <div class="clearBoth"></div>
</div>