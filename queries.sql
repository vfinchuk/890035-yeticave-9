USE yeticave;

/**
  INSERT
 */

 /* Добавление новых категорий */
INSERT INTO categories (name, code)
    VALUE ('Доски и лыжи', 'boards'),
          ('Крепления','attachment'),
          ('Ботинки', 'boots'),
          ('Одежда', 'clothing'),
          ('Инструменты', 'tools'),
          ('Разное', 'other');

 /* Добавление новых пользователей */
INSERT INTO users (email, name, password, contact)
    VALUE ('andrey123@gmail.com', 'Андрей Иванов', 'qwerty12345', 'Kiev, Ukraine'),
          ('dima777@mail.ru', 'Дмитрий Сидоров', '123456789', 'Москва, Центральная 47, кв7'),
          ('nataly@ukr.net', 'Наталья Смирнова', 'nata12345', 'Броварской проспект 34, кв123');

 /* Добавление новых лотов */
INSERT INTO lots (user_id, category_id, end_time, name, content, image, start_price, step_rate)
    VALUE (1, 1, NOW() + INTERVAL 1 DAY, '2014 Rossignol District Snowboard', 'Сноуборд находится в хорошем состоянии',  'img/lot-1.jpg', 10999, 200),
          (2, 1, NOW() + INTERVAL 1 DAY, 'DC Ply Mens 2016/2017 Snowboard', 'В идельном состоянии', 'img/lot-2.jpg', 159999, 300),
          (3, 2, NOW() + INTERVAL 1 DAY, 'Крепления Union Contact Pro 2015 года размер L/XL', 'Причина продажи - не подошли к моей доске', 'img/lot-3.jpg', 8000, 150),
          (1, 3, NOW() + INTERVAL 1 DAY, 'Ботинки для сноуборда DC Mutiny Charocal', 'Абсолютно новые ботинки, не были в использовании', 'img/lot-4.jpg', 10999, 100),
          (1, 4, NOW() + INTERVAL 1 DAY, 'Куртка для сноуборда DC Mutiny Charocal', 'Куртка, теплая, целая, в хорошем состоянии', 'img/lot-5.jpg', 7500, 185),
          (1, 6, NOW() + INTERVAL 1 DAY, 'Маска Oakley Canopy', 'В нормальном состоянии, есть пару царапин на стекле', 'img/lot-6.jpg', 5400, 60);


 /* Добавление новых ставок*/
INSERT INTO bets (user_id, lot_id, create_time, amount)
    VALUE (1, 1, NOW(), 12000),
          (1, 2, NOW(), 175000),
          (2, 3, NOW(), 8900),
          (3, 4, NOW(), 9500),
          (3, 5, NOW(), 7500);

/**
  SELECT
 */

/* получить все категории */
SELECT * FROM categories;

/* получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории */


SELECT l.name, start_price, image, MAX(amount), c.name AS category_name
    FROM lots l
    LEFT JOIN bets b ON b.lot_id = l.id
    JOIN categories c ON l.category_id = c.id
    GROUP BY l.id
    ORDER BY l.create_time DESC;


/* показать лот по его id. Получите также название категории, к которой принадлежит лот; */
SELECT l.name, start_price, image, MAX(amount), c.name AS category_name
    FROM lots l
    LEFT JOIN bets b ON b.lot_id = l.id
    JOIN categories c ON l.category_id = c.id
    WHERE l.id = 1;


/* обновить название лота по его идентификатору */
UPDATE lots SET name = 'new-name' WHERE id = 2;

/* получить список самых свежих ставок для лота по его идентификатору. */
SELECT * FROM bets b
    INNER JOIN lots l ON b.lot_id = l.id
    WHERE b.lot_id = 2
    ORDER BY b.create_time DESC;