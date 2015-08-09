--
-- Создание таблицы `wishlist` в базе битрикса
--

CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned NOT NULL,
  `id_tovar` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`,`id_tovar`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=24 ;