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
    <form class="form container <?= $form_valid = isset($errors) ? 'form--invalid' : null; ?>" action="sign-up.php"  method="post" autocomplete="off" enctype="multipart/form-data">
        <h2>Регистрация нового аккаунта</h2>

        <div class="form__item <?= $email_valid = isset($errors['email']) ? 'form__item--invalid' : null; ?>">
            <label for="email">E-mail <sup>*</sup></label>
            <input id="email" type="text" name="user[email]" placeholder="Введите e-mail" value="<?= $email = $_POST['user']['email'] ?? null; ?>">
            <span class="form__error"><?= $email_error = $errors['email'] ?? null; ?></span>
        </div>

        <div class="form__item <?= $password_valid = isset($errors['password']) ? 'form__item--invalid' : null; ?>">
            <label for="password">Пароль <sup>*</sup></label>
            <input id="password" type="password" name="user[password]" placeholder="Введите пароль" value="<?= $password = $_POST['user']['password'] ?? null; ?>">
            <span class="form__error"><?= $password_error = $errors['password'] ?? null; ?></span>
        </div>

        <div class="form__item <?= $name_valid = isset($errors['name']) ? 'form__item--invalid' : null; ?>">
            <label for="name">Имя <sup>*</sup></label>
            <input id="name" type="text" name="user[name]" placeholder="Введите имя" value="<?= $password = $_POST['user']['name'] ?? null; ?>">
            <span class="form__error"><?= $name_error = $errors['name'] ?? null; ?></span>
        </div>

        <div class="form__item <?= $contact_valid = isset($errors['contact']) ? 'form__item--invalid' : null; ?>">
            <label for="message">Контактные данные <sup>*</sup></label>
            <textarea id="message" name="user[contact]" placeholder="Напишите как с вами связаться"><?= $contact = $_POST['user']['contact'] ?? null; ?></textarea>
            <span class="form__error"><?= $name_error = $errors['contact'] ?? null; ?></span>
        </div>

        <div class="form__item form__item--file <?= $avatar_valid = isset($errors['avatar']) ? 'form__item--invalid' : false; ?>">
            <label>Аватар</label>
            <div class="form__input-file">
                <input class="visually-hidden" type="file" name="avatar" id="lot-img" value="">
                <label for="lot-img">
                    Добавить
                </label>
            </div>
            <span class="form__error"><?= $avatar_error = $errors['avatar'] ?? false; ?></span>
        </div>

        <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>

        <button type="submit" name="" class="button">Зарегистрироваться</button>

        <a class="text-link" href="#">Уже есть аккаунт</a>
    </form>
</main>
