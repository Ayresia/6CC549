CREATE DATABASE IF NOT EXISTS bookandboard
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE bookandboard;

CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(190) NOT NULL,
  phone         VARCHAR(30)  NOT NULL DEFAULT '',
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('customer', 'staff') NOT NULL DEFAULT 'customer',
  created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_users_email (email)
);

CREATE TABLE IF NOT EXISTS offers (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  title       VARCHAR(150)  NOT NULL,
  destination VARCHAR(150)  NOT NULL,
  type        VARCHAR(40)   NOT NULL,
  description TEXT          NOT NULL,
  price       DECIMAL(10,2) NOT NULL,
  start_date  DATE          NOT NULL,
  end_date    DATE          NOT NULL,
  image       VARCHAR(255)  NOT NULL,
  alt         VARCHAR(255)  NOT NULL,
  active      TINYINT(1)    NOT NULL DEFAULT 1,
  bestseller  TINYINT(1)    NOT NULL DEFAULT 0,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_offers_active_created (active, created_at, id)
);

CREATE TABLE IF NOT EXISTS branches (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name           VARCHAR(100) NOT NULL,
  location       VARCHAR(150) NOT NULL DEFAULT '',
  street         VARCHAR(150) NOT NULL DEFAULT '',
  area           VARCHAR(150) NOT NULL DEFAULT '',
  phone          VARCHAR(30)  NOT NULL DEFAULT '',
  tel            VARCHAR(30)  NOT NULL DEFAULT '',
  email          VARCHAR(190) NOT NULL DEFAULT '',
  hours          VARCHAR(120) NOT NULL DEFAULT '',
  is_head_office TINYINT(1)   NOT NULL DEFAULT 0,
  created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_branches_office (is_head_office, id)
);

CREATE TABLE IF NOT EXISTS flights (
  id               INT UNSIGNED      NOT NULL AUTO_INCREMENT,
  airline          VARCHAR(80)       NOT NULL,
  origin           VARCHAR(100)      NOT NULL,
  destination      VARCHAR(100)      NOT NULL,
  departure_date   DATE              NOT NULL,
  departure_time   TIME              NOT NULL,
  arrival_time     TIME              NOT NULL,
  duration_minutes SMALLINT UNSIGNED NOT NULL,
  stops            TINYINT UNSIGNED  NOT NULL DEFAULT 0,
  price            DECIMAL(10,2)     NOT NULL,
  created_at       DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_flights_search (destination, departure_date)
);

CREATE TABLE IF NOT EXISTS hotels (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  name            VARCHAR(150)  NOT NULL,
  destination     VARCHAR(100)  NOT NULL,
  description     TEXT          NOT NULL,
  available_from  DATE          NOT NULL,
  available_to    DATE          NOT NULL,
  price_per_night DECIMAL(10,2) NOT NULL,
  rating          DECIMAL(2,1)  NOT NULL DEFAULT 0.0,
  image           VARCHAR(255)  NOT NULL DEFAULT '',
  alt             VARCHAR(255)  NOT NULL DEFAULT '',
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_hotels_search (destination, available_from, available_to)
);

CREATE TABLE IF NOT EXISTS previous_packages (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  customer_id  INT UNSIGNED NOT NULL,
  title        VARCHAR(150) NOT NULL,
  destination  VARCHAR(150) NOT NULL,
  start_date   DATE         NOT NULL,
  end_date     DATE         NOT NULL,
  package_type VARCHAR(40)  NOT NULL,
  status       VARCHAR(20)  NOT NULL DEFAULT 'Completed',
  created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_packages_customer (customer_id, start_date),
  CONSTRAINT fk_packages_customer FOREIGN KEY (customer_id)
    REFERENCES users (id) ON DELETE CASCADE
);
