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
(11, 'systemadmin', '$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0', 'System', 'Admin', 'development@tangentmtw.com'),
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
    (2, 'default', 'index', '%'),
    (2, 'default', 'auth', 'login'),
    (2, 'default', 'auth', 'logout');