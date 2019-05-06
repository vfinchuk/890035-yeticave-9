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
    <form class="form container <?= $form_valid = isset($errors) ? 'form--invalid' : null; ?>" action="login.php" method="post">
        <h2>Вход</h2>

        <div class="form__item <?= $email_valid = isset($errors['email']) ? 'form__item--invalid' : null; ?>">
            <label for="email">E-mail <sup>*</sup></label>
            <input id="email" type="text" name="auth[email]" placeholder="Введите e-mail" value="<?= $email = $_POST['auth']['email'] ?? null; ?>">
            <span class="form__error"><?= $email_error = $errors['email'] ?? null; ?></span>
        </div>

        <div class="form__item form__item--last <?= $password_valid = isset($errors['password']) ? 'form__item--invalid' : null; ?>">
            <label for="password">Пароль <sup>*</sup></label>
            <input id="password" type="password" name="auth[password]" placeholder="Введите пароль" value="<?= $password = $_POST['auth']['password'] ?? null; ?>">
            <span class="form__error"><?= $password_error = $errors['password'] ?? null; ?></span>
        </div>

        <button type="submit" class="button">Войти</button>
    </form>
</main>