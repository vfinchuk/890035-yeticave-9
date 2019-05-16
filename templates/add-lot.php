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
    <form class="form form--add-lot container <?= isset($errors)
        ? 'form--invalid' : false; ?>" action="add-lot.php"
          method="post" enctype="multipart/form-data">
        <h2>Добавление лот</h2>

        <div class="form__container-two">
            <div class="form__item <?= isset($errors['name'])
                ? 'form__item--invalid' : false; ?>">
                <label for="lot-name">Наименование <sup>*</sup></label>
                <input id="lot-name" type="text" name="lot[name]"
                       placeholder="Введите наименование лота"
                       value="<?= $_POST['lot']['name'] ?? false; ?>">
                <span class="form__error"><?= $errors['name'] ?? null; ?></span>
            </div>

            <div class="form__item <?= isset($errors['category'])
                ? 'form__item--invalid' : false; ?>">
                <label for="category">Категория <sup>*</sup></label>
                <select id="category" name="lot[category]">
                    <option>Выберите категорию</option>
                    <?php foreach ($categories as $category):
                        $category_post = $_POST['lot']['category'] ?? null; ?>
                        <option value="<?= $category['id']; ?>"
                                <?php if ($category['id']
                                == $category_post): ?>selected="selected"<?php endif; ?>>
                            <?= $category['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="form__error"><?= $errors['category'] ??
                    null; ?></span>
            </div>
        </div>

        <div class="form__item form__item--wide <?= isset($errors['content'])
            ? 'form__item--invalid' : false; ?>">
            <label for="message">Описание <sup>*</sup></label>
            <textarea id="message" name="lot[content]" maxlength="10000"
                      placeholder="Напишите описание лота"><?= $_POST['lot']['content']
                ?? null; ?></textarea>
            <span class="form__error"><?= $errors['content'] ?? false; ?></span>
        </div>

        <div class="form__item form__item--file <?= isset($errors['lot-image'])
            ? 'form__item--invalid' : false; ?>">
            <label>Изображение <sup>*</sup></label>
            <div class="form__input-file">
                <input class="visually-hidden" type="file" name="lot-image"
                       id="lot-img" value="">
                <label for="lot-img">
                    Добавить
                </label>
            </div>
            <span class="form__error"><?= $errors['lot-image'] ??
                false; ?></span>
        </div>

        <div class="form__container-three">
            <div class="form__item form__item--small <?= isset($errors['start-price'])
                ? 'form__item--invalid' : false; ?>">
                <label for="lot-rate">Начальная цена <sup>*</sup></label>
                <input id="lot-rate" type="text" name="lot[start-price]"
                       placeholder="0"
                       value="<?= $_POST['lot']['start-price'] ?? false; ?>">
                <span class="form__error"><?= $errors['start-price'] ??
                    false; ?></span>
            </div>

            <div class="form__item form__item--small <?= isset($errors['step-rate'])
                ? 'form__item--invalid' : false; ?>">
                <label for="lot-step">Шаг ставки <sup>*</sup></label>
                <input id="lot-step" type="text" name="lot[step-rate]"
                       placeholder="0"
                       value="<?= $_POST['lot']['step-rate'] ?? false; ?>">
                <span class="form__error"><?= $errors['step-rate'] ??
                    false; ?></span>
            </div>

            <div class="form__item <?= isset($errors['end-time'])
                ? 'form__item--invalid' : false; ?>">
                <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
                <input class="form__input-date" id="lot-date" type="text"
                       name="lot[end-time]"
                       placeholder="Введите дату в формате ГГГГ-ММ-ДД"
                       value="<?= $_POST['lot']['end-time'] ?? false; ?>">
                <span class="form__error"><?= $errors['end-time'] ??
                    false; ?></span>
            </div>
        </div>
        <span class="form__error form__error--bottom">Пожалуйста, исправьте  ошибки в форме.</span>
        <button type="submit" name="" class="button">Добавить лот</button>
    </form>
</main>
