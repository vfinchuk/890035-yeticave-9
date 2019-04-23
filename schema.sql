USE yeticave;

CREATE TABLE categories (
  id   INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128),
  code CHAR(128)
);

CREATE TABLE users (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  name     VARCHAR(128),
  email    CHAR(128),
  password CHAR(64),
  avatar   CHAR(128),
  contact  CHAR(128),
  dt_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE lots (
  id          INT       AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(128),
  discription CHAR(128),
  avatar      CHAR(128),
  prise       INT,
  rate        INT,
  dt_add      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  dt_stop     INT
);

CREATE TABLE bets (
  id   INT       AUTO_INCREMENT KEY,
  date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  summ INT
);