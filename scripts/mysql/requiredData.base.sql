-- scripts/sql/data.mysql.sql
--
-- You can begin populating the database with the following SQL statements.
-- $6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0


INSERT INTO `users` (`id`, `username`, `password`, `firstname`, `lastname`, `email`) VALUES
	(1, 'root', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Root', 'User', 'lrobert@tangentmtw.com'),
	(2, 'lrobert', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Lee', 'Robert', 'lrobert@tangentmtw.com'),
	(3, 'cgarrah', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Chris', 'Garrah', 'cgarrah@tangentmtw.com'),
	(4, 'swilder', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Shawn', 'Wilder', 'swilder@tangentmtw.com'),
	(5, 'jlarochelle', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Jay', 'Larochelle', 'jlarochelle@tangentmtw.com'),
	(6, 'nmcconkey', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Norm', 'McConkey', 'nmcconkey@tangentmtw.com'),
	(7, 'eoffshack', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Emily', 'Offshack', 'eoffshack@tangentmtw.com'),
	(8, 'standarduser', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Standard', 'User', 'development@tangentmtw.com');
	
INSERT INTO `roles` VALUES
    (1, 'Standard User');
    
    
/* Add roles to users */
INSERT INTO `user_roles` (`userId`, `roleId`) VALUES
    (1,1), /* root,Root */
   
    -- lrobert
    (2,1),
    
    -- cgarrah
    (3,1),
    
    -- swilder
    (4,1),
    
    -- jlarochelle
    (5,1),
    
    -- nmcconkey
    (6,1),
    
    -- eoffshack
    (7,1),
    
    -- standarduser
    (8,1);
    
/* Add privileges to the roles */
INSERT INTO `privileges` (`roleId`, `module`, `controller`, `action`) VALUES
-- ROOT    
    (1, 'admin', 'index', '%'),    
    (1, 'admin', 'toner', '%'),
    (1, 'admin', 'user', '%'),
    (1, 'default', '%', '%'),
    (1, 'quotegen', '%', '%');
    
INSERT INTO `log_types` (`id`, `name`) VALUES
(1, 'Application Log'),
(2, 'Login Attempt'),
(3, 'Proposal'),
(4, 'Email'),
(5, 'Security');

INSERT INTO `manufacturers` (`id`, `fullname`, `displayname`) VALUES
(1, 'Brother', 'Brother'),
(2, 'Canon', 'Canon'),
(3, 'Clover Technologies Group', 'Clover Technologies Group'),
(4, 'Copystar', 'Copystar'),
(5, 'Dell', 'Dell'),
(6, 'Fuji Xerox', 'Fuji Xerox'),
(7, 'Hewlett-packard', 'HP'),
(8, 'Horizon Usa', 'Horizon Usa'),
(9, 'Ibm', 'Ibm'),
(10, 'Image Projections West Inc', 'Image Projections West Inc'),
(11, 'Imagistics', 'Imagistics'),
(12, 'Konica Minolta', 'Konica Minolta'),
(13, 'Lanier', 'Lanier'),
(14, 'Lexmark', 'Lexmark'),
(15, 'Minolta', 'Minolta'),
(16, 'Oce', 'Oce'),
(17, 'Oce Imagistics', 'Oce Imagistics'),
(18, 'Oki', 'Oki'),
(19, 'Panasonic', 'Panasonic'),
(20, 'Ricoh', 'Ricoh'),
(21, 'Samsung', 'Samsung'),
(22, 'Savin', 'Savin'),
(23, 'Sharp', 'Sharp'),
(24, 'Tech Optics Inc', 'Tech Optics Inc'),
(25, 'Toshiba', 'Toshiba'),
(26, 'Xerox', 'Xerox');

INSERT INTO `countries` (`id`, `name`) VALUES
(1, 'Canada'),
(2, 'United States');
