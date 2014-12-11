-- Дамп структуры для таблица kerio_control.log
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(10) NOT NULL,
  `action_type` ENUM('logged in','logged out') NOT NULL COMMENT 'Тип совершенного пользователем действия',
  `date_time` datetime NOT NULL,
--   `cur_okv_id` varchar(4) DEFAULT NULL,
--   `cur_country` varchar(255) DEFAULT NULL COMMENT 'ИД страны',
--   `cur_uses` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Обновлять курс',
--   `rate` decimal(20,6) NOT NULL DEFAULT '1.000000',
--   `created_at` datetime NOT NULL,
--   `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- CREATE TABLE `daily_currency` (
-- 	`currency_id` INT(11) NOT NULL,
-- 	`currency_from` INT(11) NOT NULL DEFAULT '1',
-- 	`currency_date` DATE NOT NULL,
-- 	`currency_sum` DECIMAL(20,4) UNSIGNED NOT NULL DEFAULT '0.0000' COMMENT 'Системный курс валют',
-- 	`direction` ENUM('+','-','0') NOT NULL COMMENT 'Направление роста валюты. + = растёт, - = падает, 0 = без изменений',
-- 	`currency_user_sum` DECIMAL(20,4) UNSIGNED NOT NULL DEFAULT '0.0000' COMMENT 'Пользовательский курс валют',
-- 	`user_id` BIGINT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Ид пользователя',
-- 	INDEX `new_index` (`currency_date`),
-- 	INDEX `user_id` (`user_id`)
-- )
-- COLLATE='utf8_general_ci'
-- ENGINE=InnoDB
-- ;
