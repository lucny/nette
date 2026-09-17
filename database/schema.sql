CREATE DATABASE IF NOT EXISTS nette_products CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nette_products;

CREATE TABLE IF NOT EXISTS product (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    code VARCHAR(40) NOT NULL,
    name VARCHAR(160) NOT NULL,
    description TEXT NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    price DECIMAL(12, 2) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_product_code (code),
    KEY idx_product_active_created (active, created_at),
    KEY idx_product_name (name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS user (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_user_email (email)
) ENGINE=InnoDB;
