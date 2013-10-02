<?php

use Phinx\Migration\AbstractMigration;

class InsertData extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('INSERT INTO `dealers` (`id`, `dealerName`, `userLicenses`, `dateCreated`) VALUES
(1, \'Root Company\', 25, \'2013-03-12\'),
(2, \'Tangent MTW\', 25, \'2013-03-12\'),
(3, \'Office Depot\', 25, \'2013-04-05\'),
(4, \'Canon USA\', 5, \'2013-04-05\');');

        $this->execute('INSERT INTO `users` (`id`, `dealerId`, `password`, `firstname`, `lastname`, `email`) VALUES
(1, 1, \'$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0\', \'Root\', \'User\', \'root@tangentmtw.com\'),
(2, 2, \'$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0\', \'Lee\', \'Robert\', \'lrobert@tangentmtw.com\'),
(4, 2, \'$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0\', \'Shawn\', \'Wilder\', \'swilder@tangentmtw.com\'),
(5, 2, \'$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0\', \'Jay\', \'Larochelle\', \'jlarochelle@tangentmtw.com\'),
(6, 2, \'$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0\', \'Norm\', \'McConkey\', \'nmcconkey@tangentmtw.com\'),
(8, 2, \'$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0\', \'Standard\', \'User\', \'standarduser@tangentmtw.com\'),
(9, 2, \'$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0\', \'Tyson\', \'Riehl\', \'triehl@tangentmtw.com\'),
(10, 3, \'$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0\', \'OD\', \'Admin\', \'odadmin@tangentmtw.com\'),
(11, 4, \'$6$rounds=5000$lunchisdabest$T0ehTHDo0LbN/rxeeo/7JlKK0LhRZa5DdSMhXg2Si/24RWYC8gVZtsPUiW2fzMx.5zF6WjQdOngF2tcYul2Vi0\', \'Canon\', \'Admin\', \'canon@tangentmtw.com\');');

        $this->execute('INSERT INTO `roles` VALUES
(1, \'System Administrator\', 1),
(2, \'Company Administrator\', 0),
(3, \'Hardware & Pricing Administrator\', 0);');

        $this->execute("INSERT INTO `user_roles` (`userId`, `roleId`) VALUES
-- Root User
(1, 1),
-- lrobert
(2, 2),
(2, 3),
-- swilder
(4, 2),
(4, 3),
-- jlarochelle
(5, 2),
(5, 3),
-- nmcconkey
(6, 2),
(6, 3),
-- standarduser doesn't get any extra permissions
-- triehl
(9, 2),
(9, 3),

-- odadmin
(10, 2),
(10, 3),

-- canon
(11, 2),
(11, 3);");

        $this->execute('INSERT INTO `log_types` (`id`, `name`) VALUES
(1, \'Application Log\'),
(2, \'Login Attempt\'),
(3, \'Proposal\'),
(4, \'Email\'),
(5, \'Security\');');

        $this->execute('INSERT INTO `manufacturers` (`id`, `fullname`, `displayname`, `isDeleted`) VALUES
(1, \'Brother\', \'Brother\', 0),
-- Clover renamed to compatible vendor
(3, \'Compatible Vendor 1\', \'Comp. Vendor 1\', 0),
(4, \'Dell\', \'Dell\', 0),
(5, \'Hewlett-Packard\', \'HP\', 0),
(6, \'Image Projections West Inc\', \'Image Projections West Inc\', 0),
(7, \'Lexmark\', \'Lexmark\', 0),
(8, \'Horizon USA\', \'Horizon USA\', 0),
(9, \'Tech Optics Inc\', \'Tech Optics Inc\', 0),
(10, \'Xerox\', \'Xerox\', 0),
(11, \'Canon\', \'Canon\', 0),
(12, \'Imagistics\', \'Imagistics\', 0),
(13, \'Oce\', \'Oce\', 0),
(14, \'Oce Imagistics\', \'Oce Imagistics\', 0),
(15, \'Ricoh\', \'Ricoh\', 0),
(16, \'Savin\', \'Savin\', 0),
(17, \'Konica Minolta\', \'Konica\', 0),
(18, \'Kyocera Mita\', \'Kyocera\', 0),
(19, \'Minolta\', \'Minolta\', 0),
(20, \'Fuji Xerox\', \'Fuji Xerox\', 0),
(21, \'Oki\', \'Oki\', 0),
(22, \'Samsung\', \'Samsung\', 0),
(23, \'Toshiba\', \'Toshiba\', 0),
(24, \'Sharp\', \'Sharp\', 0),
(25, \'IBM\', \'IBM\', 0),
(26, \'Panasonic\', \'Panasonic\', 0),
(27, \'Copystar\', \'Copystar\', 0),
(28, \'Lanier\', \'Lanier\', 0),
(29, \'Media Science\', \'Media Science\', 0),
(30, \'Print Rite\', \'Print Rite\', 0),
(31, \'Memjet\', \'Memjet\', 0);');

        $this->execute('INSERT INTO `countries` (`id`, `name`, `locale`) VALUES
(1, \'Canada\', \'en_CA\'),
(2, \'United States\', \'en_US\');');

        $this->execute('INSERT INTO `regions` (`countryId`, `region`) VALUES
(1, \'AB\'),
(1, \'BC\'),
(1, \'MB\'),
(1, \'NB\'),
(1, \'NL\'),
(1, \'NT\'),
(1, \'NS\'),
(1, \'NU\'),
(1, \'ON\'),
(1, \'PE\'),
(1, \'QC\'),
(1, \'SK\'),
(1, \'YT\'),
(2, \'AK\'),
(2, \'AL\'),
(2, \'AR\'),
(2, \'AS\'),
(2, \'AZ\'),
(2, \'CA\'),
(2, \'CO\'),
(2, \'CT\'),
(2, \'DC\'),
(2, \'DE\'),
(2, \'FL\'),
(2, \'GA\'),
(2, \'GU\'),
(2, \'HI\'),
(2, \'IA\'),
(2, \'ID\'),
(2, \'IL\'),
(2, \'IN\'),
(2, \'KS\'),
(2, \'KY\'),
(2, \'LA\'),
(2, \'MA\'),
(2, \'MD\'),
(2, \'ME\'),
(2, \'MH\'),
(2, \'MI\'),
(2, \'MN\'),
(2, \'MO\'),
(2, \'MS\'),
(2, \'MT\'),
(2, \'NC\'),
(2, \'ND\'),
(2, \'NE\'),
(2, \'NH\'),
(2, \'NJ\'),
(2, \'NM\'),
(2, \'NV\'),
(2, \'NY\'),
(2, \'OH\'),
(2, \'OK\'),
(2, \'OR\'),
(2, \'PA\'),
(2, \'PR\'),
(2, \'PW\'),
(2, \'RI\'),
(2, \'SC\'),
(2, \'SD\'),
(2, \'TN\'),
(2, \'TX\'),
(2, \'UT\'),
(2, \'VA\'),
(2, \'VI\'),
(2, \'VT\'),
(2, \'WA\'),
(2, \'WI\'),
(2, \'WV\'),
(2, \'WY\');');

        $this->execute('INSERT INTO `device_swap_reason_categories` (`id`, `name`) VALUES
(1, \'Flagged Devices\'),
(2, \'Device Has Replacement Device\');');

        $this->execute('INSERT INTO `device_swap_reasons` (`id`, `dealerId`, `deviceSwapReasonCategoryId`, `reason`) VALUES
(1, 1, 1, \'Device not consistent with MPS program.  AMPV is significant.\'),
(2, 1, 2, \'Device has a high cost per page.\'),
(3, 2, 1, \'Device not consistent with MPS program.  AMPV is significant.\'),
(4, 2, 2, \'Device has a high cost per page.\');');

        $this->execute('INSERT INTO device_swap_reason_defaults (`deviceSwapReasonCategoryId`, `dealerId`, `deviceSwapReasonId`) VALUES
(1, 1, 1),
(2, 1, 2),
(1, 2, 3),
(2, 2, 4);');

    }

    /**
     * Migrate Down.
     */
    public function down ()
    {

    }
}