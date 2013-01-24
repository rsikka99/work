INSERT INTO `clients` (`id`, `accountNumber`, `companyName`,`legalName`) VALUES
(1, '0000001', 'Tangent MTW', 'Tangent MTW Incorporated'),
(2, '0000002', 'Starbucks', 'Starbucks'),
(3, '0000003', 'Novellis', 'Novellis co-operatives'),
(4, '0000004', 'Samsung', 'Samsung Incorporated');

INSERT INTO `contacts` (`id`,`clientId`, `firstName`, `lastName`,`countryCode`,`areaCode`,`exchangeCode`,`number`,`extension`) VALUES
(1,1, 'Norm', 'McConkey', 1,613,507,5151,null),
(2,2, 'Tyson', 'Riehl', 1,613,333,4444,null),
(3,3, 'Shawn', 'Wilder', 1,613,666,6666,null),
(4,4, 'Lee', 'Robert', 1,613,123,1234,null);

INSERT INTO `addresses` (`id`, `clientId`, `addressLine1`, `addressline2`,`city`,`region`,`postCode`,`countryId`) VALUES
(1,1, '945 Princess Street','Suite 234','Kingston','9','K7L3N6',1),
(2,2, 'Tyson Avenue','','Kingston','9','k7n2s1',1),
(3,3, 'Shawn Lane','','Kingston','9','k2h7s6',1),
(4,4, 'Lee Street','','Kingston','9','k1b2s7',1);