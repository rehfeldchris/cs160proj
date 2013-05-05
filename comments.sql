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