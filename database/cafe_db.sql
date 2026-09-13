
DROP DATABASE IF EXISTS cafe_db;
CREATE DATABASE cafe_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cafe_db;
CREATE TABLE address (
    aid      INT AUTO_INCREMENT PRIMARY KEY,
    city     VARCHAR(30) NOT NULL,
    country  VARCHAR(30) NOT NULL DEFAULT 'Bangladesh'
) ENGINE=InnoDB;
CREATE TABLE users (
    userId      VARCHAR(20)  PRIMARY KEY,
    name        VARCHAR(50)  NOT NULL,
    phone       VARCHAR(15)  NOT NULL,
    email       VARCHAR(50)  NOT NULL UNIQUE,
    pass        VARCHAR(255) NOT NULL,
    role        ENUM('manager','barista','waiter','customer') NOT NULL DEFAULT 'customer',
    aid         INT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT users_aid_fk FOREIGN KEY (aid)
        REFERENCES address (aid) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE INDEX users_email_idx ON users (email);
CREATE TABLE password_reset (
    prid        INT AUTO_INCREMENT PRIMARY KEY,
    userId      VARCHAR(20) NOT NULL,
    token_hash  CHAR(64)    NOT NULL,
    expires_at  DATETIME    NOT NULL,
    used        TINYINT(1)  NOT NULL DEFAULT 0,
    created_at  TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT reset_user_fk FOREIGN KEY (userId)
        REFERENCES users (userId) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE INDEX reset_token_idx ON password_reset (token_hash);
INSERT INTO address (city, country) VALUES
('Dhaka',    'Bangladesh'),
('Gazipur',  'Bangladesh'),
('Rangpur',  'Bangladesh');

CREATE TABLE menu_items (
    itemId       INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(80)   NOT NULL,
    description  VARCHAR(255)  NOT NULL DEFAULT '',
    price        DECIMAL(8,2)  NOT NULL,
    category     VARCHAR(40)   NOT NULL DEFAULT 'General',
    available    TINYINT(1)    NOT NULL DEFAULT 1,
    created_at   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE orders (
    orderId         INT AUTO_INCREMENT PRIMARY KEY,
    customerId      VARCHAR(20) NOT NULL,
    baristaId       VARCHAR(20) NULL,
    waiterId        VARCHAR(20) NULL,
    status          ENUM('received','preparing','prepared','served') NOT NULL DEFAULT 'received',
    payment_method  ENUM('cash','ewallet') NOT NULL DEFAULT 'cash',
    total_amount    DECIMAL(8,2) NOT NULL DEFAULT 0,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT orders_customer_fk FOREIGN KEY (customerId)
        REFERENCES users (userId) ON DELETE CASCADE,
    CONSTRAINT orders_barista_fk FOREIGN KEY (baristaId)
        REFERENCES users (userId) ON DELETE SET NULL,
    CONSTRAINT orders_waiter_fk FOREIGN KEY (waiterId)
        REFERENCES users (userId) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE INDEX orders_status_idx ON orders (status);
CREATE INDEX orders_customer_idx ON orders (customerId);
CREATE INDEX orders_barista_idx ON orders (baristaId);

CREATE TABLE order_items (
    orderItemId  INT AUTO_INCREMENT PRIMARY KEY,
    orderId      INT NOT NULL,
    itemId       INT NULL,
    name         VARCHAR(80) NOT NULL,
    unit_price   DECIMAL(8,2) NOT NULL,
    quantity     INT NOT NULL DEFAULT 1,

    CONSTRAINT order_items_order_fk FOREIGN KEY (orderId)
        REFERENCES orders (orderId) ON DELETE CASCADE,
    CONSTRAINT order_items_menu_fk FOREIGN KEY (itemId)
        REFERENCES menu_items (itemId) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE INDEX order_items_order_idx ON order_items (orderId);

INSERT INTO menu_items (name, description, price, category, available) VALUES
('Espresso',         'Double shot, no milk',                 150.00, 'Coffee',  1),
('Cappuccino',        'Espresso, steamed milk, foam',          220.00, 'Coffee',  1),
('Cafe Latte',        'Espresso with steamed milk',            240.00, 'Coffee',  1),
('Iced Americano',    'Espresso over ice and cold water',      200.00, 'Coffee',  1),
('Masala Chai',       'Spiced milk tea',                       120.00, 'Tea',     1),
('Green Tea',         'Plain, no sugar',                       110.00, 'Tea',     1),
('Grilled Sandwich',  'Chicken, cheese, mixed vegetables',     280.00, 'Food',    1),
('Beef Burger',       'Beef patty, cheese, house sauce',       350.00, 'Food',    1),
('Chocolate Brownie', 'Warm, served with a chocolate drizzle', 180.00, 'Dessert', 1),
('Blueberry Muffin',  'Baked fresh every morning',             150.00, 'Dessert', 1);
