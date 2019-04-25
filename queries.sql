/**
  INSERT
 */

 /* inset category_item in categories table */
INSERT INTO yeticave.categories (name, code)
    VALUE ('cat_name', 'cat_code');

 /* inset new user in users table */
INSERT INTO yeticave.users (email, name, password, contact, avatar)
    VALUE ('email', 'name', 'password', 'contact', 'avatar');

 /* inset new lot in lots table */
INSERT INTO yeticave.lots (user_id, category_id, name, price, image)
    VALUE ('user_id', 'category_id', 'name', 'price', 'image_url');

 /* inset new bet in bets table */
INSERT INTO yeticave.lots (price)
    VALUE ('price');


/**
  SELECT
 */

/* получить все категории */
SELECT * FROM yeticave.categories;

/* получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории */
SELECT name, price, image, step_rate
      FROM yeticave.lots
      WHERE dt_end IS NULL
      ORDER BY dt_add DESC;

/* показать лот по его id. Получите также название категории, к которой принадлежит лот; */
  SELECT * FROM  yeticave.lots l INNER JOIN categories c ON l.category_id = c.id WHERE l.id in (1,3,5);

/* обновить название лота по его идентификатору */
UPDATE yeticave.lots SET name = 'new-name' WHERE id = 2;

/* получить список самых свежих ставок для лота по его идентификатору. */
SELECT * FROM yeticave.bets b
    INNER JOIN yeticave.lots l ON b.lot_id = l.id
    WHERE b.lot_id = 2
    ORDER BY b.dt_add DESC;