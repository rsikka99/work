-- ---------------------------------------------------
-- Insert quote devices, options and option categories
-- ---------------------------------------------------
INSERT INTO `qgen_categories` (`id`, `name`, `description`) VALUES
(1,'Paper Tray','Increases the paper capacity of a printer'),
(2,'Finishers','These perform various tasks to finish off your prints'),
(3,'Networking','Allows a printer to gain network connectivity'),
(4,'Hard Drives','Hard Drives');

INSERT INTO `qgen_options` (`id`, `name`,`description`,`cost`,`sku`) VALUES
(1, '500 Sheet Paper tray', 'The standard 500 sheet paper tray', 3.00,'PPRTRY-500'),
(2, '40 GB Hard drive', '40gb Hard Drive', 44.00, 'HDD-WD40'),
(3, '60 GB Hard drive', '60 GB Hard Drive', 22.00, 'HDD-WD60'),
(4, 'Stapler/Collator', 'Staples and collates pages', 320.00, 'FNSHRSTPLCOL'),
(5, 'Stapler', 'Staples papers together to form booklets', 225.00, 'FNSHRSTPL'),
(6, 'WiFi Adapter', 'a/b/g/n WiFi adapter', 35.00, 'NTWKWIFI-ABGN-11');

INSERT INTO `qgen_option_categories` (`optionId`, `categoryId`) VALUES
(1,1),
(1,4),
(3,4),
(4,2),
(6,2),
(6,3);

-- Add Devices
INSERT INTO `qgen_devices` (`masterDeviceId`, `sku`, `description`) VALUES 
(1, 'FAKESKU3123', NULL),
(2, 'FAKESKU4113', NULL),
(3, 'FAKESKU7553', NULL);

-- Add Options To the Devices
INSERT INTO `qgen_device_options` (`masterDeviceId`, `optionId`) VALUES
(1, 1),
(1, 2),
(1, 4),

(2, 1),
(2, 3),
(2, 5),

(3, 1),
(3, 5);

-- ---------------------------------
-- Create a quote with some devices.
-- ---------------------------------
INSERT INTO `qgen_quotes` (`id`, `clientId`, `dateCreated`, `dateModified`, `quoteDate`, `userId`, `clientDisplayName`, `leaseRate`, `leaseTerm`, `monochromePageMargin`, `colorPageMargin`, `adminCostPerPage`,`serviceCostPerPage`, `pageCoverageMonochrome`, `monochromeOverageRatePerPage`, `colorOverageRatePerPage`, `pageCoverageColor`, `pricingConfigId`, `quoteType`) VALUES
(1, 2, NOW(), NOW(), NOW(), 1, NULL, 0.0343, 39, 20, 30, 0, 0, 6, 0.03, 0.05, 24, 2, 'leased');

INSERT INTO `qgen_quote_lease_terms` (`quoteId`, `leasingSchemaTermId`) VALUES
(1, 4);

INSERT INTO `qgen_quote_device_groups` (`id`, `quoteId`, `name`, `isDefault`) VALUES
(1, 1,'Default Group (Ungrouped)', 1),
(2, 1,'Kingston Office', 0),
(3, 1,'Head Office', 0);

INSERT INTO `qgen_quote_devices` (`id`,`quoteId`, `margin`, `name`, `sku`, `oemCostPerPageMonochrome`, `oemCostPerPageColor`, `compCostPerPageMonochrome`, `compCostPerPageColor`, `cost`, `residual`, `packageCost`, `packageMarkup`) VALUES
(1, 1, 20.00, 'HP Color Laserjet Cm3530 Mfp', 'FAKESKU3123',    0.01, 0.08, 0.01, 0.08, 2155.90, 0, 2522.90, 0),
(2, 1, 20.00, 'HP Color Laserjet Cm2320nf', 'FAKESKU4113',      0.01, 0.08, 0.01, 0.08,  699.99, 0,  949.99, 0),
(3, 1, 20.00, 'HP Laserjet 2200d', 'FAKESKU7553',               0.01, 0.08, 0.01, 0.08,   99.00, 0,  327.00, 0);

INSERT INTO `qgen_quote_device_group_devices` (`quoteDeviceId`,`quoteDeviceGroupId`,`quantity`,`monochromePagesQuantity`, `colorPagesQuantity`) VALUES
(1,1,1,0,0),
(2,1,1,0,0),
(3,1,1,0,0),
(2,2,1,0,0),
(3,2,1,0,0),
(3,3,3,0,0);

INSERT INTO `qgen_quote_device_configurations` (`masterDeviceId`, `quoteDeviceId`) VALUES
(1, 1),
(2, 2),
(3, 3);

INSERT INTO `qgen_quote_device_options` (`id`, `quoteDeviceId`, `sku`, `name`, `description`, `cost`, `quantity`, `includedQuantity`) VALUES
(1, 1, 'PPRTRY-500',    '500 Sheet Paper tray', 'The standard 500 sheet paper tray',          3.00, 1, 0),
(2, 1, 'HDD-WD40',      '40 GB Hard drive',     '40gb Hard Drive',                           44.00, 1, 0),
(3, 1, 'FNSHRSTPLCOL',  'Stapler/Collator',     'Staples and collates pages',               320.00, 1, 0),
(4, 2, 'PPRTRY-500',    '500 Sheet Paper tray', 'The standard 500 sheet paper tray',          3.00, 1, 0),
(5, 2, 'HDD-WD60',      '60 GB Hard drive',     '60 GB Hard Drive',                          22.00, 1, 0),
(6, 2, 'FNSHRSTPL',     'Stapler',              'Staples papers together to form booklets', 225.00, 1, 0),
(7, 3, 'PPRTRY-500',    '500 Sheet Paper tray', 'The standard 500 sheet paper tray',          3.00, 1, 0),
(8, 3, 'FNSHRSTPL',     'Stapler',              'Staples papers together to form booklets', 225.00, 1, 0);

INSERT INTO `qgen_quote_device_configuration_options` (`quoteDeviceOptionId`, `optionId`, `masterDeviceId`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 4, 1),
(4, 1, 2),
(5, 3, 2),
(6, 5, 2),
(7, 1, 3),
(8, 5, 3);

