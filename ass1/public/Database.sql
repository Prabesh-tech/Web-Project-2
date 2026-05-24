-- Adminer 5.4.2 MariaDB 12.2.2-MariaDB-ubu2404 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `auction`;
CREATE TABLE `auction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `description` text DEFAULT NULL,
  `categoryId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `mileage` int(11) DEFAULT NULL,
  `currentBid` decimal(10,2) DEFAULT NULL,
  `endDate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO `auction` (`id`, `title`, `description`, `categoryId`, `userId`, `image`, `year`, `mileage`, `currentBid`, `endDate`) VALUES
(1,	'BYD ATTO 3',	'The BYD ATTO 3 is a compact electric SUV that blends futuristic design with practicality. Built on BYD’s advanced e‑Platform 3.0, it offers smooth performance, a spacious cabin, and a driving range that makes it ideal for families and professionals who want a stylish yet efficient EV.',	26,	1,	'1779520063_BYD4.jpg',	2,	12,	500000.00,	'2026-05-23 12:48:00'),
(2,	'BMW 3 Series (G20 Facelift)',	'A luxury sedan with sporty handling, digital cockpit, and elegant LED headlights.',	45,	1,	'1779548784_BMW1.jpg',	2023,	18,	25000.00,	'2026-06-01 20:50:00'),
(3,	'BMW M4 Coupe (G82)',	'High-performance coupe with bold vertical grilles, twin-turbo engine, and track-ready design.',	45,	1,	'1779548967_BMW2.jpg',	2022,	11,	70000.00,	'2026-06-01 20:53:00'),
(4,	'BMW 3 Series (G20 Facelift)',	'A refined mid-size sedan with sporty handling, premium interiors, and advanced tech.',	46,	1,	'1779587511_BMW4.jpg',	2023,	18,	25000.00,	'2026-06-01 21:47:00'),
(5,	'BMW i8 Hybrid Sports Car',	'A plug‑in hybrid supercar combining electric efficiency with turbocharged power. Its butterfly doors and aerodynamic design make it a symbol of BMW’s innovation.',	46,	1,	'1779587471_BMW3.jpg',	2020,	47,	80000.00,	'2026-06-01 22:01:00'),
(6,	'BMW M8 Competition Coupe',	'A twin‑turbo V8 beast with 625 hp, aggressive styling, and track‑ready aerodynamics. Combines luxury with raw power.',	46,	1,	'1779587429_BMW2.jpg',	2023,	10,	95000.00,	'2026-06-03 22:10:00'),
(7,	'BMW 327 (Classic)',	'A timeless pre‑war BMW with elegant curves, chrome detailing, and handcrafted precision — a symbol of automotive artistry.',	46,	1,	'1779587365_BMW1.jpg',	1937,	10,	120000.00,	'2026-06-05 22:12:00'),
(9,	'Audi A5 Sportback',	'A stylish four‑door coupe combining sporty performance with refined comfort and advanced tech.',	47,	1,	'1779587606_Audi3.jpg',	2022,	17,	40000.00,	'2026-06-07 22:23:00'),
(12,	'Audi RS5 Coupe',	'A twin‑turbo V6 powerhouse delivering thrilling performance, Quattro all‑wheel drive, and bold RS styling.',	47,	1,	'1779587591_Audi2.jpg',	2023,	11,	84999.99,	'2026-06-08 22:55:00'),
(13,	'Audi A7 Sportback',	'A premium four‑door coupe combining sporty performance, advanced tech, and refined comfort — perfect for long drives and executive style.',	47,	1,	'1779587575_Audi1.jpg',	2023,	15,	80000.00,	'2026-06-10 22:58:00'),
(14,	'Audi RS5 Sportback',	'A powerful twin‑turbo V6 sedan with Quattro AWD, aggressive styling, and luxurious comfort — built for speed and sophistication.',	47,	1,	'1779587619_Audi4.jpg',	2023,	11,	95000.00,	'2026-05-11 23:03:00'),
(15,	'Porsche 911 Carrera 4S',	'A legendary all‑wheel‑drive sports car combining precision handling, timeless design, and thrilling performance.',	48,	4,	'1779594333_Porsche3.jpg',	2023,	11,	110000.00,	'2026-06-13 06:10:00'),
(16,	'Porsche 911 GT3 RS',	'A high‑performance icon built for precision and speed, featuring aerodynamic upgrades, lightweight design, and race‑ready engineering.',	48,	4,	'1779594304_Porsche2.jpg',	2023,	9,	180000.00,	'2026-06-14 06:14:00'),
(17,	'Porsche 911 GT3 RS Manthey Edition',	'A race‑tuned version of the GT3 RS with Manthey performance upgrades, delivering extreme aerodynamics, precision handling, and breathtaking speed.',	48,	4,	'1779587955_Porsche1.jpg',	2024,	9,	200000.00,	'2026-06-15 06:17:00'),
(18,	'Porsche 911 Turbo S',	'A top‑tier 911 variant with blistering acceleration, advanced aerodynamics, and refined luxury — the ultimate everyday supercar.',	48,	4,	'1779594162_Porsche4.jpg',	2023,	10,	160000.00,	'2026-07-16 06:18:00'),
(19,	'Lamborghini Huracan Evo',	'A mid‑engine masterpiece combining raw V10 power, advanced aerodynamics, and Italian craftsmanship — built for pure adrenaline.',	49,	4,	'1779594479_Lamborghini3.jpg',	2023,	8,	210000.00,	'2026-06-18 06:44:00'),
(21,	'Lamborghini Revuelto',	'A revolutionary V12 hybrid supercar combining electrified power with Lamborghini’s unmistakable design and performance heritage.',	49,	4,	'1779594501_Lamborghini4.jpg',	2024,	7,	600000.00,	'2026-05-19 06:48:00'),
(22,	'Porsche 911 Widebody LB Works Edition',	'A heavily modified 911 featuring Liberty Walk’s signature widebody kit, aggressive aerodynamics, and street‑ready performance.',	48,	4,	'1779594362_Porsche5.jpg',	2022,	10,	140000.00,	'2026-05-20 06:50:00'),
(23,	'Lamborghini Huracan Performante',	'A track‑focused evolution of the Huracán, featuring lightweight carbon fiber construction, active aerodynamics, and a roaring V10 engine.',	49,	4,	'1779594453_Lamborghini2.jpg',	2022,	8,	250000.00,	'2026-06-21 06:57:00'),
(24,	'Lamborghini Aventador SV Roadster',	'A limited‑edition V12 convertible delivering breathtaking performance, aggressive styling, and the thrill of open‑air driving.',	49,	4,	'1779594420_Lamborghini1.jpg',	2016,	7,	349999.99,	'2026-06-22 06:58:00');

DROP TABLE IF EXISTS `bid`;
CREATE TABLE `bid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auctionId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO `bid` (`id`, `auctionId`, `userId`, `amount`, `createdAt`) VALUES
(1,	13,	2,	80000.00,	'2026-05-23 17:21:16'),
(2,	14,	2,	95000.00,	'2026-05-23 17:23:42');

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO `categories` (`id`, `name`, `description`, `image`) VALUES
(46,	'BMW',	'BMW is a German luxury carmaker known for performance, innovation, and elegance. From modern sedans and sports coupes to timeless classics, every BMW delivers sheer driving pleasure with premium design and engineering.',	NULL),
(47,	'Audi',	'Audi is a German luxury carmaker known for its slogan “Vorsprung durch Technik” (Progress through Technology). It blends premium design, advanced engineering, and innovation, offering everything from sporty sedans to cutting‑edge electric vehicles.',	NULL),
(48,	'Porsche',	'Porsche is a German luxury sports car manufacturer known for precision engineering, timeless design, and thrilling performance. From the iconic 911 to modern electric models like the Taycan, every Porsche delivers pure driving excitement and unmatched craftsmanship.',	NULL),
(49,	'Lamborghini',	'Lamborghini is an Italian luxury sports car manufacturer renowned for its bold design, extreme performance, and unmistakable roar. Every model — from the Aventador to the Huracán — embodies speed, power, and pure automotive art.',	NULL),
(50,	'Rolls Royace',	'Rolls‑Royce is the ultimate symbol of luxury and prestige, blending handcrafted elegance with effortless performance. Each car is a masterpiece designed for comfort, exclusivity, and timeless sophistication.',	NULL);

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `reviewerId` int(11) NOT NULL,
  `reviewText` text NOT NULL,
  `rating` int(11) DEFAULT 0,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO `reviews` (`id`, `userId`, `reviewerId`, `reviewText`, `rating`, `createdAt`) VALUES
(1,	14,	2,	'Let\'s do it',	0,	'2026-05-23 17:23:31');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` text NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL,
  `isAdmin` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING HASH,
  UNIQUE KEY `email` (`email`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO `users` (`id`, `username`, `email`, `password`, `isAdmin`) VALUES
(1,	'Prabesh',	'prabesh@gmail.com',	'$2y$10$EoAW6pQPAL4QnDBPLgyBzeaMoma8d4QWV/tfX0og3hEVEaNOY/L7O',	2),
(2,	'user',	'user@example.com',	'$2y$10$cUY/e5Q/7rIwWfXjye9KcufmQpySVsAfF1jpQ8a0NpnYAbqOOi/ye',	0),
(4,	'Admin',	'admin@example.com',	'$2y$10$FXMJuo4iCzHkelpDLTOC8OCi3Pm/lg3hy7INnyeNeSXrYjSgBFUPC',	1);

DROP TABLE IF EXISTS `watches`;
CREATE TABLE `watches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auctionId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_watch` (`auctionId`,`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


-- 2026-05-24 04:03:40 UTC
