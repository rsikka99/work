-- scripts/sql/data.mysql.sql
--
-- You can begin populating the database with the following SQL statements.
-- $6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0


INSERT INTO `users` (`id`, `username`, `password`, `firstname`, `lastname`, `email`) VALUES
(1, 'root', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Root', 'User', 'lrobert@tangentmtw.com'),
(2, 'lrobert', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Lee', 'Robert', 'lrobert@tangentmtw.com'),
(3, 'cgarrah', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Chris', 'Garrah', 'cgarrah@tangentmtw.com'),
(4, 'swilder', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Shawn', 'Wilder', 'swilder@tangentmtw.com'),
(5, 'jsadler', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'John', 'Sadler', 'jsadler@tangentmtw.com'),
(6, 'nmcconkey', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Norm', 'McConkey', 'nmcconkey@tangentmtw.com'),
(7, 'eoffshack', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Emily', 'Offshack', 'eoffshack@tangentmtw.com'),
(8, 'mrogall', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Marc', 'Rogall', 'mrogall@tangentmtw.com'),
(9, 'lkittner', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Laura', 'Kittner', 'lkittner@tangentmtw.com'),
(10, 'dnikolopoulos', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Demetra', 'Nikolopoulos', 'dnikolopoulos@tangentmtw.com'),
(11, 'companyadmin', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Company', 'Admin', 'development@tangentmtw.com'),
(12, 'standarduser', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Standard', 'User', 'development@tangentmtw.com');

INSERT INTO `roles` VALUES
    (1, 'Root'),
    (2, 'Anyone'),
    (3, 'System Administrator'),
    (4, 'Standard User');
    
/* Add roles to users */
INSERT INTO `user_roles` VALUES
    (1,2), /* root,Root */
    (2,4),
    (3,4),
    (4,4),
    (5,4),
    (6,4),
    (7,4),
    (8,4),
    (9,4),
    (10,4),
    (11,4),
    (12,3);
    
/* Add privileges to the roles */
INSERT INTO `privileges` (`roleId`, `module`, `controller`, `action`) VALUES
    (1, '%', '%', '%'),    
    (2, 'default', 'auth', 'eula'),
    (2, 'default', 'auth', 'login'),
    (2, 'default', 'auth', 'forgotpassword');
    
INSERT INTO `log_types` (`id`, `name`) VALUES
(1, 'Application Log'),
(2, 'Login Attempt'),
(3, 'Proposal'),
(4, 'Email'),
(5, 'Security');

INSERT INTO `clients` (`id`, `name`, `address`, `phoneNumber`) VALUES
(1, 'Test Company', "123 Fake Street\nSuite 456\nFake City\nA1A 2B2", '123-456-7890'),
(2, 'NotReal Solutions', "123 Fake Street\nSuite 9876\nFake City\nA1A 2B2", '1-123-456-7890'),
(3, 'Company ABC', "123 Fake Street\nSuite 132\nFake City\nA1A 2B2", '01-123-456-7890'),
(4, 'Company XYZ', "123 Fake Street\nSuite 004-84\nFake City\nA1A 2B2", '+1 123-456-7890');

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
