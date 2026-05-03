SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS messenger_messages;
DROP TABLE IF EXISTS order_line;
DROP TABLE IF EXISTS invoice;
DROP TABLE IF EXISTS `order`;
DROP TABLE IF EXISTS product;
DROP TABLE IF EXISTS user;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. Create Tables

CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(180) NOT NULL UNIQUE,
    roles JSON NOT NULL,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description LONGTEXT DEFAULT NULL,
    price DOUBLE PRECISION NOT NULL,
    is_avalible TINYINT(1) NOT NULL,
    deleted_at DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_ref_id INT DEFAULT NULL,
    status VARCHAR(255) NOT NULL,
    total DOUBLE PRECISION NOT NULL,
    type VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    deleted_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_order_user FOREIGN KEY (user_id) REFERENCES user (id),
    CONSTRAINT fk_order_user_ref FOREIGN KEY (user_ref_id) REFERENCES user (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE invoice (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_relation_id INT NOT NULL UNIQUE,
    user_id INT DEFAULT NULL,
    total DOUBLE PRECISION NOT NULL,
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_invoice_order FOREIGN KEY (order_relation_id) REFERENCES `order` (id),
    CONSTRAINT fk_invoice_user FOREIGN KEY (user_id) REFERENCES user (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_line (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_relation_id INT DEFAULT NULL,
    order_ref_id INT DEFAULT NULL,
    product_id INT NOT NULL,
    product_ref_id INT DEFAULT NULL,
    quantity INT NOT NULL,
    price DOUBLE PRECISION NOT NULL,
    CONSTRAINT fk_ol_order_rel FOREIGN KEY (order_relation_id) REFERENCES `order` (id),
    CONSTRAINT fk_ol_order_ref FOREIGN KEY (order_ref_id) REFERENCES `order` (id),
    CONSTRAINT fk_ol_product FOREIGN KEY (product_id) REFERENCES product (id),
    CONSTRAINT fk_ol_product_ref FOREIGN KEY (product_ref_id) REFERENCES product (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE messenger_messages (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    body LONGTEXT NOT NULL,
    headers LONGTEXT NOT NULL,
    queue_name VARCHAR(190) NOT NULL,
    created_at DATETIME NOT NULL,
    available_at DATETIME NOT NULL,
    delivered_at DATETIME DEFAULT NULL,
    INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Demo Data

INSERT INTO user (email, roles, password) VALUES 
('admin@example.com', '["ROLE_ADMIN"]', '$2y$13$dummyhash1'),
('customer@example.com', '["ROLE_USER"]', '$2y$13$dummyhash2');

INSERT INTO product (name, description, price, is_avalible) VALUES 
('Smartphone X', 'Latest model with 128GB storage', 799.99, 1),
('Wireless Headphones', 'Noise cancelling over-ear headphones', 199.50, 1),
('Coffee Maker', 'Automatic espresso machine', 450.00, 1),
('Old Tablet', 'Discontinued model', 150.00, 0);

INSERT INTO `order` (user_id, status, total, type, created_at) VALUES 
(2, 'completed', 999.49, 'online', '2026-05-01 10:00:00'),
(2, 'pending', 450.00, 'online', '2026-05-02 14:30:00');

INSERT INTO order_line (order_relation_id, product_id, quantity, price) VALUES 
(1, 1, 1, 799.99),
(1, 2, 1, 199.50),
(2, 3, 1, 450.00);

INSERT INTO invoice (order_relation_id, user_id, total, created_at) VALUES 
(1, 2, 999.49, '2026-05-01 10:05:00');