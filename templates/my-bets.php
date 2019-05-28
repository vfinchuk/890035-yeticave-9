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
    <section class="rates container">
        <h2>Мои ставки</h2>
        <table class="rates__list">
            <?php
            if ($my_bets):
                foreach ($my_bets as $my_bet):
                    if($user['id'] == $my_bet['winner_id']){
                        $class_lot_item = 'rates__item--win';
                    } else {
                        $class_lot_item = 'rates__item--end';
                    }
                    ?>
                    <tr class="rates__item <?= is_lot_end($my_bet['end_time'])
                        ? $class_lot_item : ''; ?>">
                        <td class="rates__info">
                            <div class="rates__img">
                                <img src="<?= $my_bet['image']; ?>" width="54" height="40" alt="<?= $my_bet['lot_name']; ?>">
                            </div>
                            <div>
                                <h3 class="rates__title"><a href="/lot.php?id=<?= $my_bet['lot_id']; ?>"><?= $my_bet['lot_name']; ?></a></h3>
                                <?php if($user['id'] == $my_bet['winner_id']): ?>
                                    <p><?= $my_bet['contact']; ?></p>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="rates__category"><?= $my_bet['category_name']; ?></td>
                        <td class="rates__timer">
                            <?php if (is_lot_end($my_bet['end_time'])): ?>
                                <?php if($user['id'] == $my_bet['winner_id']): ?>
                                    <div class="timer timer--win">Ставка выиграла</div>
                                   <?php else: ?>
                                    <div class="timer timer--end">Торги окончены</div>
                                    <?php endif; ?>
                            <?php else: ?>
                                <div class="timer timer<?= is_timer_finishing($my_bet['end_time'],
                                    1) ? '--finishing' : ''; ?>">
                                    <?= time_to_end($my_bet['end_time']); ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="rates__price"><?= price_format(intval($my_bet['bet_amount']),
                                true); ?></td>
                        <td class="rates__time"><?= get_bet_time($my_bet['create_time']); ?></td>
                    </tr>
                <?php endforeach; endif; ?>
        </table>
    </section>
</main>