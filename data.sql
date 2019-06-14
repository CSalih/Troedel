CREATE TABLE `posts` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `text` text NOT NULL,
 `rating` int(11) NOT NULL DEFAULT '0',
 `create_user` varchar(50) NOT NULL,
 `create_date` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4

CREATE TABLE `rating` (
 `liked` int(11) NOT NULL,
 `user` varchar(50) NOT NULL,
 `thumb` varchar(1) NOT NULL,
 PRIMARY KEY (`liked`,`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4

 	CREATE TABLE `users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `username` varchar(50) NOT NULL,
 `password` varchar(255) NOT NULL,
 `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
 `ip` varchar(255) DEFAULT NULL,
 `last_login` datetime DEFAULT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4