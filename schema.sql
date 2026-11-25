-- Fresh installation schema. Use an empty database.

CREATE TABLE `category_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_name` varchar(120) NOT NULL,
  `category_description` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `costing` (
  `id` int(12) NOT NULL,
  `cost_type` varchar(50) CHARACTER SET utf8 NOT NULL,
  `description` varchar(100) CHARACTER SET utf8 NOT NULL,
  `amount` int(10) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `customer_details` (
  `id` int(15) UNSIGNED NOT NULL,
  `customer_name` varchar(200) NOT NULL DEFAULT '',
  `customer_address` varchar(500) NOT NULL DEFAULT '',
  `customer_contact1` varchar(100) NOT NULL DEFAULT '',
  `customer_contact2` varchar(100) NOT NULL DEFAULT '',
  `balance` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `dip_stock_product` (
  `sl` int(20) NOT NULL,
  `stock_code` varchar(20) NOT NULL,
  `barcode` varchar(20) NOT NULL,
  `particular` varchar(30) NOT NULL,
  `company` varchar(30) NOT NULL,
  `model` varchar(20) NOT NULL,
  `company_prize` int(10) NOT NULL,
  `sell_prize` int(10) NOT NULL,
  `stock_date` datetime NOT NULL,
  `supplier` varchar(50) NOT NULL,
  `stock_employer` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `stock_avail` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL DEFAULT '',
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `category` varchar(20) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `stock_details` (
  `id` int(15) UNSIGNED NOT NULL,
  `stock_id` varchar(120) NOT NULL DEFAULT '',
  `barcode` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `stock_name` varchar(120) NOT NULL DEFAULT '',
  `stock_brand` varchar(20) NOT NULL DEFAULT '',
  `stock_quatity` int(11) NOT NULL DEFAULT 0,
  `supplier_id` varchar(250) NOT NULL DEFAULT '',
  `supplier` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `company_price` int(10) NOT NULL DEFAULT 0,
  `selling_price` decimal(10,2) NOT NULL DEFAULT 0,
  `category` varchar(120) NOT NULL DEFAULT '',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expire_date` datetime DEFAULT NULL,
  `uom` varchar(120) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `stock_entries` (
  `id` int(15) UNSIGNED NOT NULL,
  `stock_id` varchar(120) NOT NULL DEFAULT '',
  `stock_name` varchar(260) NOT NULL DEFAULT '',
  `barcode` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `stock_brand` varchar(20) NOT NULL DEFAULT '',
  `stock_supplier_name` varchar(200) NOT NULL DEFAULT '',
  `category` varchar(120) NOT NULL DEFAULT '',
  `quantity` int(11) NOT NULL DEFAULT 0,
  `company_price` decimal(10,2) NOT NULL DEFAULT 0,
  `selling_price` decimal(10,2) NOT NULL DEFAULT 0,
  `opening_stock` int(11) NOT NULL DEFAULT 0,
  `closing_stock` int(11) NOT NULL DEFAULT 0,
  `date` datetime NOT NULL,
  `username` varchar(120) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT '',
  `salesid` varchar(120) NOT NULL DEFAULT '',
  `total` decimal(10,2) NOT NULL DEFAULT 0,
  `payment` decimal(10,2) NOT NULL DEFAULT 0,
  `balance` decimal(10,2) NOT NULL DEFAULT 0,
  `mode` varchar(150) NOT NULL DEFAULT '',
  `description` varchar(500) NOT NULL DEFAULT '',
  `due` datetime DEFAULT NULL,
  `subtotal` int(11) NOT NULL DEFAULT 0,
  `count1` int(11) NOT NULL DEFAULT 0,
  `billnumber` varchar(120) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `stock_sales` (
  `id` int(15) UNSIGNED NOT NULL,
  `transactionid` varchar(250) NOT NULL DEFAULT '',
  `billnumber` varchar(120) NOT NULL DEFAULT '',
  `stock_name` varchar(200) NOT NULL DEFAULT '',
  `barcode` varchar(50) NOT NULL DEFAULT '',
  `stock_brand` varchar(20) NOT NULL DEFAULT '',
  `category` varchar(120) NOT NULL DEFAULT '',
  `supplier_name` varchar(200) NOT NULL DEFAULT '',
  `selling_price` decimal(10,2) NOT NULL DEFAULT 0,
  `quantity` decimal(10,2) NOT NULL DEFAULT 0,
  `amount` decimal(10,2) NOT NULL DEFAULT 0,
  `date` date NOT NULL,
  `username` varchar(120) NOT NULL DEFAULT '',
  `customer` varchar(100) NOT NULL DEFAULT '',
  `customer_id` varchar(120) NOT NULL DEFAULT '',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0,
  `payment` decimal(10,2) NOT NULL DEFAULT 0,
  `balance` decimal(10,2) NOT NULL DEFAULT 0,
  `discount` decimal(10,0) NOT NULL DEFAULT 0,
  `tax` decimal(10,0) NOT NULL DEFAULT 0,
  `tax_dis` varchar(100) NOT NULL DEFAULT '',
  `dis_amount` decimal(10,0) NOT NULL DEFAULT 0,
  `grand_total` decimal(10,0) NOT NULL DEFAULT 0,
  `due` date DEFAULT NULL,
  `mode` varchar(250) NOT NULL DEFAULT '',
  `description` varchar(500) NOT NULL DEFAULT '',
  `count1` int(11) NOT NULL DEFAULT 0,
  `profit` decimal(7,2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `stock_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(120) NOT NULL,
  `email` varchar(254) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `user_type` varchar(20) NOT NULL,
  `reset_token_hash` char(64) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  UNIQUE KEY `stock_user_email` (`email`),
  UNIQUE KEY `stock_user_reset_token` (`reset_token_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `store_details` (
  `name` varchar(100) NOT NULL,
  `log` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `place` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `web` varchar(100) NOT NULL,
  `pin` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `supplier_details` (
  `id` int(15) UNSIGNED NOT NULL,
  `supplier_name` varchar(200) NOT NULL DEFAULT '',
  `supplier_address` varchar(500) NOT NULL DEFAULT '',
  `supplier_contact1` varchar(100) NOT NULL DEFAULT '',
  `supplier_contact2` varchar(100) NOT NULL DEFAULT '',
  `balance` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `test_table` (
  `id` int(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `customer` varchar(250) NOT NULL,
  `supplier` varchar(250) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `payment` decimal(10,2) NOT NULL,
  `balance` decimal(10,2) NOT NULL,
  `due` datetime NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rid` varchar(120) NOT NULL,
  `receiptid` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `uom_details` (
  `id` int(10) UNSIGNED ZEROFILL NOT NULL,
  `name` varchar(120) NOT NULL,
  `spec` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `category_details`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `costing`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `customer_details`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `dip_stock_product`
  ADD PRIMARY KEY (`sl`);

ALTER TABLE `stock_avail`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stock_details`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stock_entries`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stock_sales`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stock_user`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `supplier_details`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `uom_details`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `category_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `costing`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT;

ALTER TABLE `customer_details`
  MODIFY `id` int(15) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `dip_stock_product`
  MODIFY `sl` int(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `stock_avail`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `stock_details`
  MODIFY `id` int(15) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `stock_entries`
  MODIFY `id` int(15) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `stock_sales`
  MODIFY `id` int(15) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `stock_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `supplier_details`
  MODIFY `id` int(15) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

ALTER TABLE `uom_details`
  MODIFY `id` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

INSERT INTO store_details (name, log, type, address, place, city, phone, email, web, pin)
VALUES ('Store Management', '', '', '', '', '', '', '', '', '');
