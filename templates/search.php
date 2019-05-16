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
    <div class="container">
        <section class="lots">
            <h2>Результаты поиска по запросу «<span><?= $_GET['search'] ??
                    ''; ?></span>»</h2>
            <?php if (isset($lots)): ?>
                <ul class="lots__list">
                    <?php foreach ($lots as $lot): ?>
                        <li class="lots__item lot">
                            <div class="lot__image">
                                <img src="<?= $lot['image'] ?? null; ?>"
                                     width="350" height="260"
                                     alt="<?= $lot['name'] ?? null; ?>">
                            </div>
                            <div class="lot__info">
                                <span class="lot__category"><?= $lot['category']
                                    ?? null; ?></span>
                                <h3 class="lot__title"><a class="text-link"
                                                          href="lot.php?id=<?= $lot['id'] ?>"><?= $lot['name']
                                        ?? null; ?></a>
                                </h3>
                                <div class="lot__state">
                                    <div class="lot__rate">
                                        <span class="lot__amount">Стартовая цена</span>
                                        <span class="lot__cost"><?= price_format(intval($lot['start_price'])); ?></span>
                                    </div>
                                    <div class="lot__timer timer <?= is_timer_finishing($lot['end_time'],
                                        1) ? 'timer--finishing' : ''; ?>">
                                        <?= time_to_end($lot['end_time']); ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Ничего не найдено по вашему запросу</p>
            <?php endif; ?>
        </section>
        <?php if ($pagination && ($pagination['pages_count'] > 1)): ?>
            <ul class="pagination-list">
                <li class="pagination-item pagination-item-prev">
                    <a href="search.php?search=<?= $pagination['search'] ?>&page=<?= $pagination['prev_page']; ?>">Назад</a>
                </li>
                <?php foreach ($pagination['pages'] as $page): ?>
                    <li class="pagination-item <?php if ($page
                        == $pagination['current_page']
                    ) {
                        echo 'pagination-item-active';
                    } ?>">
                        <a href="search.php?search=<?= $pagination['search'] ?>&page=<?= $page; ?>"><?= $page; ?></a>
                    </li>
                <?php endforeach; ?>
                <li class="pagination-item pagination-item-next">
                    <a href="search.php?search=<?= $pagination['search'] ?>&page=<?= $pagination['next_page']; ?>">Вперед</a>
                </li>
            </ul>
        <?php endif; ?>
    </div>
</main>