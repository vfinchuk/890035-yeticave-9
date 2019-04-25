USE yeticave;

/**
  Таблица категорий
 */
CREATE TABLE categories (
  id    INT AUTO_INCREMENT PRIMARY KEY,
  name  VARCHAR(128),
  code  VARCHAR(128)
);
/**
  Таблица зарегестрированых юзеров
 */
CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  create_time   DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
  email         VARCHAR(128),
  name          VARCHAR(128),
  password      VARCHAR(64),
  avatar        VARCHAR(128),
  contact       VARCHAR(255)
);
/**
  Таблица лотов
 */
CREATE TABLE lots (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  user_id       INT REFERENCES users(id),
  category_id   INT REFERENCES categories(id),
  create_time   DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
  end_time      DATETIME,
  name          VARCHAR(128),
  content       VARCHAR(128),
  image         VARCHAR(128),
  start_price   INT NOT NULL,
  step_rate     INT


);
/**
  Таблица ставок на лот
 */
CREATE TABLE bets (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT REFERENCES users(id),
  lot_id      INT REFERENCES lots(id),
  create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
  amount      INT NOT NULL
);

/**
  Индексы:
 */
 CREATE UNIQUE INDEX users_email_udx
  ON users (email);

CREATE INDEX categories_name_idx
  ON categories (name);


CREATE INDEX lots_name_idx
  ON lots (name);