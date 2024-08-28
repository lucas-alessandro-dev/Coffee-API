CREATE DATABASE `mosyle`;

DROP TABLE IF EXISTS `users` CASCADE;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(500) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  UNIQUE KEY `email_UNIQUE` (`email`)
);

DROP TABLE IF EXISTS `tokens` CASCADE;
CREATE TABLE `tokens` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_token_user` int unsigned NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiration_time` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_id_user_token_idx` (`id_token_user`),
  CONSTRAINT `FK_id_user_token` FOREIGN KEY (`id_token_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TABLE IF EXISTS `user_drink_coffee` CASCADE;
CREATE TABLE `user_drink_coffee` (
  `id_drink_coffee` int unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_drink_coffee`),
  KEY `FK_id_user_idx` (`id_user`),
  CONSTRAINT `FK_id_user_coffe` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
