-- Run once on a backup copy of the database matching dipak_sample_pos_starting.sql.
-- Keep original column types and add defaults required by the application.

ALTER TABLE `customer_details`
  MODIFY COLUMN `customer_name` varchar(200) NOT NULL DEFAULT '',
  MODIFY COLUMN `customer_address` varchar(500) NOT NULL DEFAULT '',
  MODIFY COLUMN `customer_contact1` varchar(100) NOT NULL DEFAULT '',
  MODIFY COLUMN `customer_contact2` varchar(100) NOT NULL DEFAULT '',
  MODIFY COLUMN `balance` int(11) NOT NULL DEFAULT 0;

ALTER TABLE `stock_avail`
  MODIFY COLUMN `name` varchar(120) NOT NULL DEFAULT '',
  MODIFY COLUMN `quantity` int(10) UNSIGNED NOT NULL DEFAULT 0,
  MODIFY COLUMN `category` varchar(20) NOT NULL DEFAULT '';

ALTER TABLE `stock_details`
  MODIFY COLUMN `stock_id` varchar(120) NOT NULL DEFAULT '',
  MODIFY COLUMN `barcode` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  MODIFY COLUMN `stock_name` varchar(120) NOT NULL DEFAULT '',
  MODIFY COLUMN `stock_brand` varchar(20) NOT NULL DEFAULT '',
  MODIFY COLUMN `stock_quatity` int(11) NOT NULL DEFAULT 0,
  MODIFY COLUMN `supplier_id` varchar(250) NOT NULL DEFAULT '',
  MODIFY COLUMN `supplier` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  MODIFY COLUMN `company_price` int(10) NOT NULL DEFAULT 0,
  MODIFY COLUMN `selling_price` decimal(10,2) NOT NULL DEFAULT 0,
  MODIFY COLUMN `category` varchar(120) NOT NULL DEFAULT '',
  MODIFY COLUMN `expire_date` datetime DEFAULT NULL,
  MODIFY COLUMN `uom` varchar(120) NOT NULL DEFAULT '';

ALTER TABLE `stock_entries`
  MODIFY COLUMN `stock_id` varchar(120) NOT NULL DEFAULT '',
  MODIFY COLUMN `stock_name` varchar(260) NOT NULL DEFAULT '',
  MODIFY COLUMN `barcode` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  MODIFY COLUMN `stock_brand` varchar(20) NOT NULL DEFAULT '',
  MODIFY COLUMN `stock_supplier_name` varchar(200) NOT NULL DEFAULT '',
  MODIFY COLUMN `category` varchar(120) NOT NULL DEFAULT '',
  MODIFY COLUMN `quantity` int(11) NOT NULL DEFAULT 0,
  MODIFY COLUMN `company_price` decimal(10,2) NOT NULL DEFAULT 0,
  MODIFY COLUMN `selling_price` decimal(10,2) NOT NULL DEFAULT 0,
  MODIFY COLUMN `opening_stock` int(11) NOT NULL DEFAULT 0,
  MODIFY COLUMN `closing_stock` int(11) NOT NULL DEFAULT 0,
  MODIFY COLUMN `username` varchar(120) NOT NULL DEFAULT '',
  MODIFY COLUMN `type` varchar(50) NOT NULL DEFAULT '',
  MODIFY COLUMN `salesid` varchar(120) NOT NULL DEFAULT '',
  MODIFY COLUMN `total` decimal(10,2) NOT NULL DEFAULT 0,
  MODIFY COLUMN `payment` decimal(10,2) NOT NULL DEFAULT 0,
  MODIFY COLUMN `balance` decimal(10,2) NOT NULL DEFAULT 0,
  MODIFY COLUMN `mode` varchar(150) NOT NULL DEFAULT '',
  MODIFY COLUMN `description` varchar(500) NOT NULL DEFAULT '',
  MODIFY COLUMN `due` datetime DEFAULT NULL,
  MODIFY COLUMN `subtotal` int(11) NOT NULL DEFAULT 0,
  MODIFY COLUMN `count1` int(11) NOT NULL DEFAULT 0,
  MODIFY COLUMN `billnumber` varchar(120) NOT NULL DEFAULT '';

ALTER TABLE `stock_sales`
  MODIFY COLUMN `transactionid` varchar(250) NOT NULL DEFAULT '',
  MODIFY COLUMN `billnumber` varchar(120) NOT NULL DEFAULT '',
  MODIFY COLUMN `stock_name` varchar(200) NOT NULL DEFAULT '',
  ADD COLUMN `barcode` varchar(50) NOT NULL DEFAULT '',
  MODIFY COLUMN `stock_brand` varchar(20) NOT NULL DEFAULT '',
  MODIFY COLUMN `category` varchar(120) NOT NULL DEFAULT '',
  MODIFY COLUMN `supplier_name` varchar(200) NOT NULL DEFAULT '',
  MODIFY COLUMN `selling_price` decimal(10,2) NOT NULL DEFAULT 0,
  MODIFY COLUMN `quantity` decimal(10,2) NOT NULL DEFAULT 0,
  MODIFY COLUMN `amount` decimal(10,2) NOT NULL DEFAULT 0,
  MODIFY COLUMN `username` varchar(120) NOT NULL DEFAULT '',
  MODIFY COLUMN `customer` varchar(100) NOT NULL DEFAULT '',
  MODIFY COLUMN `customer_id` varchar(120) NOT NULL DEFAULT '',
  MODIFY COLUMN `subtotal` decimal(10,2) NOT NULL DEFAULT 0,
  MODIFY COLUMN `payment` decimal(10,2) NOT NULL DEFAULT 0,
  MODIFY COLUMN `balance` decimal(10,2) NOT NULL DEFAULT 0,
  MODIFY COLUMN `discount` decimal(10,0) NOT NULL DEFAULT 0,
  MODIFY COLUMN `tax` decimal(10,0) NOT NULL DEFAULT 0,
  MODIFY COLUMN `tax_dis` varchar(100) NOT NULL DEFAULT '',
  MODIFY COLUMN `dis_amount` decimal(10,0) NOT NULL DEFAULT 0,
  MODIFY COLUMN `grand_total` decimal(10,0) NOT NULL DEFAULT 0,
  MODIFY COLUMN `due` date DEFAULT NULL,
  MODIFY COLUMN `mode` varchar(250) NOT NULL DEFAULT '',
  MODIFY COLUMN `description` varchar(500) NOT NULL DEFAULT '',
  MODIFY COLUMN `count1` int(11) NOT NULL DEFAULT 0,
  MODIFY COLUMN `profit` decimal(7,2) NOT NULL DEFAULT 0;

ALTER TABLE `supplier_details`
  MODIFY COLUMN `supplier_name` varchar(200) NOT NULL DEFAULT '',
  MODIFY COLUMN `supplier_address` varchar(500) NOT NULL DEFAULT '',
  MODIFY COLUMN `supplier_contact1` varchar(100) NOT NULL DEFAULT '',
  MODIFY COLUMN `supplier_contact2` varchar(100) NOT NULL DEFAULT '',
  MODIFY COLUMN `balance` int(11) NOT NULL DEFAULT 0;
