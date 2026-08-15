CREATE DATABASE IF NOT EXISTS bookandboard_basic
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE bookandboard_basic;

CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(190) NOT NULL,
  phone         VARCHAR(30)  NOT NULL DEFAULT '',
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('staff') NOT NULL DEFAULT 'staff',
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
