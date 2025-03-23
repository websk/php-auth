CREATE DATABASE IF NOT EXISTS `db_auth` COLLATE 'utf8_general_ci' ;
GRANT ALL ON `db_auth`.* TO 'default'@'%' ;

FLUSH PRIVILEGES ;