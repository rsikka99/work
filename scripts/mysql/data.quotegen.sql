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

INSERT INTO `qgen_options` (`id`, `name`, `description`, `cost`, `oemSku`) VALUES
(7, 'HP 3-bin Stapler/Stacker with Output', 'Staples up to 50 pages and stacks up to 1600 sheets', 1765.15, 'CC517A'),
(8, 'HP LaserJet MFP 3000-sheet Stapler/Stacker', 'Stacks up to 3000 sheets', 1686.4, 'C8085A'),
(9, 'Cart, W/Storage Capacity', 'Printer Cart with Storage Capacity', 245.17, '097S03636'),
(10, 'HP Booklet Maker/Finisher with Output', 'Capacity of 2000 sheets and 25 saddle-stitched booklets', 2711.76, 'CC516A'),
(11, 'HP LaserJet MFP Multifunction Finisher', 'Supports up to 50 pages per minute, can staple up to 50 sheets', 2062, 'C8088B'),
(12, 'Productivity Kit (Includes Hard Drive)', 'Expands workflow options for Xerox equipment', 420.17, '097S04141'),
(13, 'HP LaserJet MFP Analog Fax Accessory 300', 'Support efficient information sharing and improve work team productivity.', 202.34, 'Q3701A'),
(14, '525 Sheet Feeder, Adjustable Up To Legal', 'Capacity of 525 sheets, can support legal paper', 257.14, '097S04142'),
(15, '8-Bin Mailbox', 'Stacks up to 250 pages per bin', 1283, 'Q5693A'),
(16, 'Tray, Main, 525 Sheets', 'Capacity of 525 sheets', 172.28, '097S04143'),
(17, '3,000-sheet Stacker', 'Capacity of 3000 sheets', 1499.64, 'C8084A');


INSERT INTO `qgen_devices` (`masterDeviceId`, `oemSku`, `description`) VALUES
(4, 'Q3939A', '- Fax accessory'),
(5, 'CC395A', ''),
(6, 'CF116A', ''),
(7, 'CE992A', ''),
(8, '8870/DN', ''),
(9, 'CE504A', '');

INSERT INTO `qgen_device_options` (`masterDeviceId`, `optionId`, `includedQuantity`) VALUES
(4, 7, 0),
(4, 10, 0),
(5, 8, 0),
(5, 11, 0),
(5, 13, 0),
(5, 15, 0),
(5, 17, 0),
(8, 9, 0),
(8, 12, 0),
(8, 14, 0),
(8, 16, 0);

INSERT INTO `qgen_device_configurations` (`id`, `masterDeviceId`, `name`, `description`) VALUES
(1, 4, 'Jays build', '6040 w booklet maker'),
(2, 4, 'bobs fav', 'yaya');


INSERT INTO `qgen_device_configuration_options` (`deviceConfigurationId`, `optionId`, `quantity`) VALUES
(1, 10, 1),
(2, 7, 2),
(2, 10, 1);