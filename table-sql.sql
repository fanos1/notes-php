
CREATE TABLE `notes` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` CHAR(100) NOT NULL,
  `description` TINYTEXT NOT NULL,
  `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;