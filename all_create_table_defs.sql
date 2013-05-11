CREATE TABLE IF NOT EXISTS `coursedetails` (
  `id` int(4) NOT NULL,
  `profname` varchar(30) NOT NULL,
  `profimage` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `course_data` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `short_desc` text NOT NULL,
  `long_desc` text NOT NULL,
  `course_link` text NOT NULL,
  `video_link` text NOT NULL,
  `start_date` date NOT NULL,
  `course_length` int(11) NOT NULL,
  `course_image` text NOT NULL,
  `category` varchar(100) NOT NULL,
  `site` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `word_hits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hits` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `trendingcourses` (
  `id` int(11) NOT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE utf8_general_ci;

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

CREATE TABLE IF NOT EXISTS `new_courses` (
  `course_data_id` int(10) NOT NULL,
  `date_added` date NOT NULL,
  constraint
   foreign key (course_data_id)
   references course_data (id)
   ON DELETE CASCADE
   ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS comments (
  course_data_id int not null,
  comment text not null,
  when_posted datetime not null,
  constraint 
   foreign key (course_data_id) 
   references course_data (id) 
   ON DELETE CASCADE 
   ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(100) NOT NULL,
  `hits` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `searched_words` (
	`word` VARCHAR(255) NOT NULL,
	`when_searched` DATETIME NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;