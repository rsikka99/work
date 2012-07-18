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
(8, 'standarduser', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'Standard', 'User', 'development@tangentmtw.com');

INSERT INTO `roles` VALUES
    (1, 'Root Admin'),
    (2, 'Proposal Generator Admin'),
    (3, 'Quote Generator Admin'),
    (4, 'Proposal Generator'),
    (5, 'Quote Generator');
    
    
/* Add roles to users */
INSERT INTO `user_roles` (`userId`, `roleId`) VALUES
    (1,1), /* root,Root */
   
    -- lrobert
    (2,2),
    (2,3),
    (2,4),
    (2,5),
    
    -- cgarrah
    (3,2),
    (3,3),
    (3,4),
    (3,5),
    
    -- swilder
    (4,2),
    (4,3),
    (4,4),
    (4,5),
    
    -- jsadler
    (5,2),
    (5,3),
    (5,4),
    (5,5),
    
    -- nmcconkey
    (6,2),
    (6,3),
    (6,4),
    (6,5),
    
    -- eoffshack
    (7,2),
    (7,3),
    (7,4),
    (7,5),
    
    -- standarduser
    (8,4),
    (8,5);
    
/* Add privileges to the roles */
INSERT INTO `privileges` (`roleId`, `module`, `controller`, `action`) VALUES
-- ROOT    
    (1, '%', '%', '%'),    
-- Proposal Generator Admin
    (2, 'default', '%', '%'),
    (2, 'proposalgen', 'manufacturer', '%'),
    (2, 'proposalgen', 'masterdevice', '%'),
-- Quote Generator Admin
    (3, 'default', '%', '%'),
    (3, 'quotegen', '%', '%'),
-- Proposal Generator Standard
    (4, 'default', '%', '%'),
    (4, 'proposalgen', 'index', '%'),
    (4, 'proposalgen', 'survey', '%'),
    (4, 'proposalgen', 'fleet', '%'),
    (4, 'proposalgen', 'report', '%'),
-- Quote Generator Standard
    (5, 'default', '%', '%'),
    (5, 'quotegen', 'index', '%'),
    (5, 'quotegen', 'quote_devices', '%'),
    (5, 'quotegen', 'quote_rettings', '%'),
    (5, 'quotegen', 'quote_reports', '%'),
    (5, 'quotegen', 'quotesetting', 'edit'),
    (5, 'quotegen', 'quote', 'index'),
    (5, 'quotegen', 'quote', 'create'),
    (5, 'quotegen', 'quote', 'delete');
    
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
