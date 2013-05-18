CREATE TABLE IF NOT EXISTS `subscription_emails` (
  `email_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `rand_key` char(50) NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `frequency` char(1) NOT NULL,
  `date_added` date NOT NULL,
  `sites` text NOT NULL,
  `max` tinyint(4) NOT NULL,
  PRIMARY KEY (`email_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;