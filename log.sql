-- Дамп структуры для таблица kerio_control.log
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(10) NOT NULL,
  `action_type` ENUM('logged in','logged out') NOT NULL COMMENT 'Тип совершенного пользователем действия',
  `date_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;