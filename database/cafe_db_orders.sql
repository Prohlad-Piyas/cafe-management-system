
USE cafe_db;
CREATE TABLE IF NOT EXISTS menu_items (
    itemId       INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(80)   NOT NULL,
    description  VARCHAR(255)  NOT NULL DEFAULT '',
    price        DECIMAL(8,2)  NOT NULL,
    category     VARCHAR(40)   NOT NULL DEFAULT 'General',
    available    TINYINT(1)    NOT NULL DEFAULT 1,
    created_at   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS orders (
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
CREATE TABLE IF NOT EXISTS order_items (
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
INSERT INTO menu_items (name, description, price, category, available)
SELECT * FROM (SELECT
    'Espresso'          AS name, 'Double shot, no milk'              AS description, 150.00 AS price, 'Coffee'   AS category, 1 AS available UNION ALL SELECT
    'Cappuccino',          'Espresso, steamed milk, foam',              220.00,        'Coffee',      1 UNION ALL SELECT
    'Cafe Latte',          'Espresso with steamed milk',                240.00,        'Coffee',      1 UNION ALL SELECT
    'Iced Americano',      'Espresso over ice and cold water',          200.00,        'Coffee',      1 UNION ALL SELECT
    'Masala Chai',         'Spiced milk tea',                           120.00,        'Tea',         1 UNION ALL SELECT
    'Green Tea',           'Plain, no sugar',                           110.00,        'Tea',         1 UNION ALL SELECT
    'Grilled Sandwich',    'Chicken, cheese, mixed vegetables',         280.00,        'Food',        1 UNION ALL SELECT
    'Beef Burger',         'Beef patty, cheese, house sauce',           350.00,        'Food',        1 UNION ALL SELECT
    'Chocolate Brownie',   'Warm, served with a chocolate drizzle',     180.00,        'Dessert',     1 UNION ALL SELECT
    'Blueberry Muffin',    'Baked fresh every morning',                 150.00,        'Dessert',     1
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM menu_items);
