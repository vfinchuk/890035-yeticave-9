/**
  INSERT
 */

 /* inset category_item in categories table */
INSERT INTO yeticave.categories (name, code)
    VALUE ('cat_name', 'cat_code');

 /* inset new user in users table */
INSERT INTO yeticave.users (email, name, password, avatar, contact)
    VALUE ('email', 'name', 'password', 'avatar', 'contact');

 /* inset new lot in lots table */
INSERT INTO yeticave.lots (user_id, category_id, name, content, image, start_price)
    VALUE ('user_id', 'category_id', 'name', 'content', 'image_url', 'start_price');

 /* inset new bet in bets table */
INSERT INTO yeticave.bets (amount)
    VALUE ('amount');


/**
  SELECT
 */

/* получить все категории */
SELECT * FROM yeticave.categories;

/* получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории */
SELECT name, start_price, image, step_rate
      FROM yeticave.lots
      WHERE end_time IS NULL
      ORDER BY create_time DESC;

/* показать лот по его id. Получите также название категории, к которой принадлежит лот; */
  SELECT * FROM  yeticave.lots l INNER JOIN categories c ON l.category_id = c.id WHERE l.id in (1,3,5);

/* обновить название лота по его идентификатору */
UPDATE yeticave.lots SET name = 'new-name' WHERE id = 2;

/* получить список самых свежих ставок для лота по его идентификатору. */
SELECT * FROM yeticave.bets b
    INNER JOIN yeticave.lots l ON b.lot_id = l.id
    WHERE b.lot_id = 2
    ORDER BY b.create_time DESC;