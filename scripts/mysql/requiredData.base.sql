-- scripts/sql/data.mysql.sql
--
-- You can begin populating the database with the following SQL statements.
-- $6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0


INSERT INTO `users` (`id`, `username`, `password`, `firstname`, `lastname`, `email`) VALUES
	(1, 'root', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Root', 'User', 'lrobert@tangentmtw.com'),
	(2, 'lrobert', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Lee', 'Robert', 'lrobert@tangentmtw.com'),
	(4, 'swilder', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Shawn', 'Wilder', 'swilder@tangentmtw.com'),
	(5, 'jlarochelle', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Jay', 'Larochelle', 'jlarochelle@tangentmtw.com'),
	(6, 'nmcconkey', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Norm', 'McConkey', 'nmcconkey@tangentmtw.com'),
	(8, 'standarduser', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Standard', 'User', 'development@tangentmtw.com');
	
INSERT INTO `roles` VALUES
    (1, 'Standard User');
    
    
/* Add roles to users */
INSERT INTO `user_roles` (`userId`, `roleId`) VALUES
    (1,1), /* root,Root */
   
    -- lrobert
    (2,1),
    
    -- swilder
    (4,1),
    
    -- jlarochelle
    (5,1),
    
    -- nmcconkey
    (6,1),
    
    -- standarduser
    (8,1);
    
/* Add privileges to the roles */
INSERT INTO `privileges` (`roleId`, `module`, `controller`, `action`) VALUES
-- ROOT
    (1, 'admin', 'index', '%'),    
    (1, 'admin', 'toner', '%'),
    (1, 'admin', 'user', '%'),
    (1, 'default', '%', '%'),
    (1, 'quotegen', '%', '%'),
    (1, 'proposalgen', 'manufacturer', '%');        
    
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
(26, 'Xerox', 'Xerox'),
(27, 'Kyocera', 'Kyocera');

INSERT INTO `countries` (`id`, `name`,`locale`) VALUES
(1, 'Canada','en_CA'),
(2, 'United States','en_US');

INSERT INTO `regions` (`countryId`, `region`) VALUES
(1, 'AB'),
(1, 'BC'),
(1, 'MB'),
(1, 'NB'),
(1, 'NL'),
(1, 'NT'),
(1, 'NS'),
(1, 'NU'),
(1, 'ON'),
(1, 'PE'),
(1, 'QC'),
(1, 'SK'),
(1, 'YT'),
(2, 'AK'),
(2, 'AL'),
(2, 'AR'),
(2, 'AS'),
(2, 'AZ'),
(2, 'CA'),
(2, 'CO'),
(2, 'CT'),
(2, 'DC'),
(2, 'DE'),
(2, 'FL'),
(2, 'GA'),
(2, 'GU'),
(2, 'HI'),
(2, 'IA'),
(2, 'ID'),
(2, 'IL'),
(2, 'IN'),
(2, 'KS'),
(2, 'KY'),
(2, 'LA'),
(2, 'MA'),
(2, 'MD'),
(2, 'ME'),
(2, 'MH'),
(2, 'MI'),
(2, 'MN'),
(2, 'MO'),
(2, 'MS'),
(2, 'MT'),
(2, 'NC'),
(2, 'ND'),
(2, 'NE'),
(2, 'NH'),
(2, 'NJ'),
(2, 'NM'),
(2, 'NV'),
(2, 'NY'),
(2, 'OH'),
(2, 'OK'),
(2, 'OR'),
(2, 'PA'),
(2, 'PR'),
(2, 'PW'),
(2, 'RI'),
(2, 'SC'),
(2, 'SD'),
(2, 'TN'),
(2, 'TX'),
(2, 'UT'),
(2, 'VA'),
(2, 'VI'),
(2, 'VT'),
(2, 'WA'),
(2, 'WI'),
(2, 'WV'),
(2, 'WY');
