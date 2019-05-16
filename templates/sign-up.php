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
    <form class="form container <?= isset($errors) ? 'form--invalid' : null; ?>"
          action="sign-up.php" method="post"
          autocomplete="off" enctype="multipart/form-data">
        <h2>Регистрация нового аккаунта</h2>

        <div class="form__item <?= isset($errors['email'])
            ? 'form__item--invalid' : null; ?>">
            <label for="email">E-mail <sup>*</sup></label>
            <input id="email" type="text" name="user[email]"
                   placeholder="Введите e-mail"
                   value="<?= $_POST['user']['email'] ?? null; ?>">
            <span class="form__error"><?= $errors['email'] ?? null; ?></span>
        </div>

        <div class="form__item <?= isset($errors['password'])
            ? 'form__item--invalid' : null; ?>">
            <label for="password">Пароль <sup>*</sup></label>
            <input id="password" type="password" name="user[password]"
                   placeholder="Введите пароль"
                   value="<?= $_POST['user']['password'] ?? null; ?>">
            <span class="form__error"><?= $errors['password'] ?? null; ?></span>
        </div>

        <div class="form__item <?= isset($errors['name'])
            ? 'form__item--invalid' : null; ?>">
            <label for="name">Имя <sup>*</sup></label>
            <input id="name" type="text" name="user[name]"
                   placeholder="Введите имя"
                   value="<?= $_POST['user']['name'] ?? null; ?>">
            <span class="form__error"><?= $errors['name'] ?? null; ?></span>
        </div>

        <div class="form__item <?= isset($errors['contact'])
            ? 'form__item--invalid' : null; ?>">
            <label for="message">Контактные данные <sup>*</sup></label>
            <textarea id="message" name="user[contact]"
                      placeholder="Напишите как с вами связаться"><?= $_POST['user']['contact']
                ?? null; ?></textarea>
            <span class="form__error"><?= $errors['contact'] ?? null; ?></span>
        </div>

        <div class="form__item form__item--file <?= isset($errors['avatar'])
            ? 'form__item--invalid' : false; ?>">
            <label>Аватар</label>
            <div class="form__input-file">
                <input class="visually-hidden" type="file" name="avatar"
                       id="lot-img" value="">
                <label for="lot-img">
                    Добавить
                </label>
            </div>
            <span class="form__error"><?= $errors['avatar'] ?? false; ?></span>
        </div>

        <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>

        <button type="submit" name="" class="button">Зарегистрироваться</button>

        <a class="text-link" href="/login.php">Уже есть аккаунт</a>
    </form>
</main>
