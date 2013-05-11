CREATE TABLE IF NOT EXISTS `new_courses` (
  `course_data_id` int(10) NOT NULL,
  `date_added` date NOT NULL,
  constraint
   foreign key (course_data_id)
   references course_data (id)
   ON DELETE CASCADE
   ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
