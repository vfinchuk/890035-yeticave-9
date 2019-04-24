USE yeticave;

/**
  Таблица категорий
 */
CREATE TABLE categories (
  id   INT AUTO_INCREMENT PRIMARY KEY,

  name VARCHAR(128),
  code VARCHAR(128)
);
/**
  Таблица зарегестрированых юзеров
 */
CREATE TABLE users (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  dt_add   TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,

  email    VARCHAR(128),
  name     VARCHAR(128),
  password VARCHAR(64),
  avatar   VARCHAR(128),
  contact  VARCHAR(255)
);
/**
  Таблица лотов
 */
CREATE TABLE lots (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT REFERENCES users(id),
  category_id INT REFERENCES categories(id),
  dt_add      TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  dt_end      DATETIME,

  name        VARCHAR(128),
  content     VARCHAR(128),
  image       VARCHAR(128),
  prise       INT                                 NOT NULL,
  step_rate   INT


);
/**
  Таблица ставок на лот
 */
CREATE TABLE bets (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT REFERENCES users(id),
  lot_id      INT REFERENCES lots(id),
  date_add    TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  price       INT                                 NOT NULL
);

/**
  Индексы:
    - c_name - названия категории
    - l_name - название лота
 */
CREATE INDEX c_name
  ON categories (name);


CREATE INDEX l_name
  ON lots (name);