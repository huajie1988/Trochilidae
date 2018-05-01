DROP TABLE IF EXISTS `admin`;CREATE TABLE `admin`(id int AUTO_INCREMENT,admin_name varchar(50)NOT NULL,password varchar(50)NOT NULL,avatar varchar(100),description text,email varchar(50),login_time datetime,status int,PRIMARY KEY (id));

DROP TABLE IF EXISTS `user`;CREATE TABLE `user`(id int AUTO_INCREMENT,user_name varchar(50)NOT NULL,password varchar(50)NOT NULL,avatar varchar(100),description text,email varchar(50),login_time datetime,status int,PRIMARY KEY (id));
INSERT INTO user (id,user_name,password,avatar,description,email,status) VALUES ('1','aaa','','aaas','sdsda','dert','1');
INSERT INTO user (id,user_name,password,avatar,description,login_time,status) VALUES ('2','huajie2','1111112','222','332','2018-04-29 00:00:00','2');
INSERT INTO user (id,user_name,password,avatar,description,email,login_time,status) VALUES ('3','aaa','sqw222','aaas','sdsda','dert','2018-04-29 09:26:57','1');
INSERT INTO user (id,user_name,password,avatar,description,email,login_time,status) VALUES ('4','aaa','sqw222','这是头像','sdsda','dert','2018-04-29 09:27:24','1');
