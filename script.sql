CREATE TABLE `admin` (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `password` varchar(60) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`admin_id`)
);

CREATE TABLE `category` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `image` varchar(512) DEFAULT NULL,
  `active` varchar(3) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`)
);

CREATE TABLE `food` (
  `food_id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` varchar(225) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(512) DEFAULT NULL,
  `active` varchar(3) NOT NULL,
  `category_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`food_id`,`category_id`),
  KEY `fk_food_category_idx` (`category_id`),
  CONSTRAINT `fk_food_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`)
);

CREATE TABLE `order` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `food` varchar(200) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` varchar(25) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_address` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`)
);