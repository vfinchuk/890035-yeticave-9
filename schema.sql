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
  email         VARCHAR(128) NOT NULL,
  name          VARCHAR(128) NOT NULL,
  password      VARCHAR(64) NOT NULL,
  avatar        VARCHAR(128),
  contact       VARCHAR(255) NOT NULL
);
/**
  Таблица лотов
 */
CREATE TABLE lots (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  user_id       INT NOT NULL,
  category_id   INT NOT NULL,
  create_time   DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
  end_time      DATETIME NOT NULL,
  name          VARCHAR(128) NOT NULL,
  content       VARCHAR(128) NOT NULL,
  image         VARCHAR(128) NOT NULL,
  start_price   INT NOT NULL,
  step_rate     INT NOT NULL
);

ALTER TABLE lots
  ADD FOREIGN KEY (user_id)
    REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE lots
  ADD FOREIGN KEY (category_id)
    REFERENCES categories(id) ON DELETE CASCADE ON UPDATE CASCADE;

/**
  Таблица ставок на лот
 */
CREATE TABLE bets (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  lot_id      INT NOT NULL,
  create_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
  amount      INT NOT NULL
);

ALTER TABLE bets
  ADD FOREIGN KEY (user_id)
    REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE bets
  ADD FOREIGN KEY (lot_id)
    REFERENCES lots(id) ON DELETE CASCADE ON UPDATE CASCADE;

/**
  Индексы:
 */
 CREATE UNIQUE INDEX users_email_udx
  ON users (email);

CREATE INDEX categories_name_idx
  ON categories (name);


CREATE INDEX lots_name_idx
  ON lots (name);