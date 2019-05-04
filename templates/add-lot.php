<main>
    <nav class="nav">
        <ul class="nav__list container">
            <?php foreach ($categories as $category): ?>
                <li class="nav__item">
                    <a href="/category.php?id=<?= $category['id']; ?>"><?= $category['name']; ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <form class="form form--add-lot container <?= $form_error = isset($errors) ? 'form--invalid' : false; ?>" action="add-lot.php" method="post" enctype="multipart/form-data">
        <h2>Добавление лота</h2>
        <div class="form__container-two">
            <div class="form__item <?= $name_error = isset($errors['name']) ? 'form__item--invalid' : false; ?>">
                <label for="lot-name">Наименование <sup>*</sup></label>
                <input id="lot-name" type="text" name="lot[name]" placeholder="Введите наименование лота">
                <span class="form__error">Введите наименование лота</span>
            </div>
            <div class="form__item <?= $category_error = isset($errors['category']) ? 'form__item--invalid' : false; ?>">
                <label for="category">Категория <sup>*</sup></label>
                <select id="category" name="lot[category]">
                    <option>Выберите категорию</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id']; ?>"><?= $category['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="form__error">Выберите категорию</span>
            </div>
        </div>
        <div class="form__item form__item--wide <?= $content_error = isset($errors['content']) ? 'form__item--invalid' : false; ?>">
            <label for="message">Описание <sup>*</sup></label>
            <textarea id="message" name="lot[content]" placeholder="Напишите описание лота"></textarea>
            <span class="form__error">Напишите описание лота</span>
        </div>
        <div class="form__item form__item--file <?= $image_error = isset($errors['lot-image']) ? 'form__item--invalid' : false; ?>">
            <label>Изображение <sup>*</sup></label>
            <div class="form__input-file">
                <input class="visually-hidden" type="file" name="lot-image" id="lot-img" value="">
                <label for="lot-img">
                    Добавить
                </label>
            </div>
            <span class="form__error"><?= $errors['lot-image']; ?></span>
        </div>
        <div class="form__container-three">
            <div class="form__item form__item--small <?= $price_error = isset($errors['start-price']) ? 'form__item--invalid' : false; ?>">
                <label for="lot-rate">Начальная цена <sup>*</sup></label>
                <input id="lot-rate" type="text" name="lot[start-price]" placeholder="0">
                <span class="form__error">
                    Введите начальную цену <br>
                    <?= $rate_not_number = isset($errors['start-price-not-number']) ? $errors['start-price-not-number'] : false; ?>
                </span>
            </div>
            <div class="form__item form__item--small <?= $rate_error = isset($errors['step-rate']) ? 'form__item--invalid' : false; ?>">
                <label for="lot-step">Шаг ставки <sup>*</sup></label>
                <input id="lot-step" type="text" name="lot[step-rate]" placeholder="0">
                <span class="form__error">
                    Введите шаг ставки <br>
                    <?= $rate_not_number = isset($errors['step-rate-not-number']) ? $errors['step-rate-not-number'] : false; ?>
                </span>
            </div>
            <div class="form__item <?= $end_time_error = isset($errors['end-time']) ? 'form__item--invalid' : false; ?>">
                <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
                <input class="form__input-date" id="lot-date" type="text" name="lot[end-time]" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
                <span class="form__error">
                    Введите дату завершения торгов<br>
                    <?= $rate_time_not_future = isset($errors['end-time-no-future']) ? $errors['end-time-no-future'] : false; ?>
                </span>
            </div>
        </div>
        <span class="form__error form__error--bottom">Пожалуйста, исправьте  ошибки в форме.</span>
        <button type="submit" name="" class="button">Добавить лот</button>
    </form>
</main>
<?php


if(isset($errors)) {
    var_dump($errors);
}


?>