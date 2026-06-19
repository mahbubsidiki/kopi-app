-- ============================================================
-- DATABASE: kopi_db
-- Cara pakai: Import file ini di phpMyAdmin → tab SQL → klik Go
-- ============================================================

-- CREATE DATABASE IF NOT EXISTS kopi_db
--   CHARACTER SET utf8mb4
--   COLLATE utf8mb4_general_ci;
-- 
-- USE kopi_db;

-- Tabel users
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nama       VARCHAR(100)  NOT NULL,
    email      VARCHAR(100)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,
    role       ENUM('user','admin') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabel kategori menu
CREATE TABLE IF NOT EXISTS categories (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE
);

-- Tabel menu items
CREATE TABLE IF NOT EXISTS menu_items (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    nama        VARCHAR(100) NOT NULL,
    deskripsi   TEXT,
    harga       DECIMAL(10,2) NOT NULL,
    gambar      VARCHAR(255) DEFAULT 'default.jpg',
    tersedia    TINYINT(1) DEFAULT 1,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Tabel orders
CREATE TABLE IF NOT EXISTS orders (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT NOT NULL,
    kode_pesanan VARCHAR(20) NOT NULL UNIQUE,
    total        DECIMAL(10,2) NOT NULL,
    status       ENUM('pending','proses','selesai','batal') DEFAULT 'pending',
    catatan      TEXT,
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel order items
CREATE TABLE IF NOT EXISTS order_items (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    order_id     INT NOT NULL,
    menu_item_id INT NOT NULL,
    jumlah       INT NOT NULL DEFAULT 1,
    harga_satuan DECIMAL(10,2) NOT NULL,
    subtotal     DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)     REFERENCES orders(id)     ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
);

-- Tabel payments
CREATE TABLE IF NOT EXISTS payments (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    order_id    INT NOT NULL UNIQUE,
    metode      ENUM('tunai','transfer','qris') DEFAULT 'tunai',
    jumlah_bayar DECIMAL(10,2) NOT NULL,
    status      ENUM('pending','lunas') DEFAULT 'pending',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- ============================================================
-- DATA AWAL
-- ============================================================

-- Admin (password akan diset via buat-admin.php)
INSERT IGNORE INTO users (nama, email, password, role) VALUES
('Admin Kopi', 'admin@kopi.com', 'PLACEHOLDER_RUN_BUAT_ADMIN', 'admin');

-- Kategori
INSERT IGNORE INTO categories (nama, slug) VALUES
('Espresso',  'espresso'),
('Cold Brew', 'cold-brew'),
('Non-Kopi',  'non-kopi'),
('Makanan',   'makanan');

-- Menu items
INSERT IGNORE INTO menu_items (category_id, nama, deskripsi, harga, gambar) VALUES
(1, 'Americano',        'Espresso dengan air panas, bold dan bersih',            25000, 'americano.jpg'),
(1, 'Cappuccino',       'Espresso dengan busa susu yang lembut dan creamy',      30000, 'cappuccino.jpg'),
(1, 'Caffe Latte',      'Espresso lembut dengan steamed milk dan latte art',     32000, 'latte.jpg'),
(1, 'Flat White',       'Double shot espresso dengan microfoam susu segar',      34000, 'flatwhite.jpg'),
(2, 'Cold Brew Original','Kopi diseduh dingin selama 12 jam, smooth & strong',   30000, 'coldbrew.jpg'),
(2, 'Cold Brew Vanilla', 'Cold brew dengan sirup vanilla premium',               33000, 'coldbrew-vanilla.jpg'),
(3, 'Matcha Latte',     'Matcha ceremonial grade dengan susu fresh',             28000, 'matcha.jpg'),
(3, 'Coklat Panas',     'Coklat belgia premium dengan susu hangat',              26000, 'coklat.jpg'),
(4, 'Croissant',        'Croissant mentega panggang renyah',                     18000, 'croissant.jpg'),
(4, 'Banana Cake',      'Cake pisang lembut dengan topping keju',                20000, 'bananacake.jpg');
