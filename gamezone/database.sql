CREATE DATABASE IF NOT EXISTS gamezone CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gamezone;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NULL,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  image VARCHAR(500),
  stock INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  payment_method ENUM('PIX','Boleto','Cartão') NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'confirmed',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id),
  CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

INSERT INTO categories (name)
SELECT 'Teclados' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name='Teclados');
INSERT INTO categories (name)
SELECT 'Mouses' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name='Mouses');
INSERT INTO categories (name)
SELECT 'Headsets' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name='Headsets');
INSERT INTO categories (name)
SELECT 'Controles' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name='Controles');
INSERT INTO categories (name)
SELECT 'Consoles' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name='Consoles');

INSERT INTO products (category_id,name,description,price,image,stock)
SELECT c.id,'Teclado Mecânico RGB','Teclado mecânico com iluminação RGB e switches responsivos.',249.90,'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&w=900&q=85',15
FROM categories c WHERE c.name='Teclados' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Teclado Mecânico RGB');
INSERT INTO products (category_id,name,description,price,image,stock)
SELECT c.id,'Mouse Gamer Precision','Mouse gamer ergonômico com sensor de alta precisão.',139.90,'https://images.unsplash.com/photo-1527814050087-3793815479db?auto=format&fit=crop&w=900&q=85',22
FROM categories c WHERE c.name='Mouses' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Mouse Gamer Precision');
INSERT INTO products (category_id,name,description,price,image,stock)
SELECT c.id,'Headset Surround X','Headset confortável com áudio imersivo para suas partidas.',299.90,'https://images.unsplash.com/photo-1599669454699-248893623440?auto=format&fit=crop&w=900&q=85',10
FROM categories c WHERE c.name='Headsets' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Headset Surround X');
INSERT INTO products (category_id,name,description,price,image,stock)
SELECT c.id,'Controle Wireless Pro','Controle sem fio com design ergonômico.',219.90,'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?auto=format&fit=crop&w=900&q=85',8
FROM categories c WHERE c.name='Controles' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Controle Wireless Pro');
INSERT INTO products (category_id,name,description,price,image,stock)
SELECT c.id,'Console Game Station','Console de última geração para uma experiência de jogo completa.',3299.90,'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?auto=format&fit=crop&w=900&q=85',4
FROM categories c WHERE c.name='Consoles' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Console Game Station');
