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
(1, 1,'Deafult Group (Ungrouped)', 1),
(2, 1,'Kingston Office', 0),
(3, 1,'Head Office', 0);

INSERT INTO `qgen_quote_devices` (`id`,`quoteId`, `margin`, `name`, `sku`, `oemCostPerPageMonochrome`, `oemCostPerPageColor`, `compCostPerPageMonochrome`, `compCostPerPageColor`, `cost`, `residual`, `packageCost`, `packageMarkup`) VALUES
(1, 1, 20.00, 'Unsynced Device Name 1', 'Unsynced SKU 1', 999, 999, 999, 999, 999.99, 0, 999.99, 999.99),
(2, 1, 20.00, 'Unsynced Device Name 2', 'Unsynced SKU 2', 999, 999, 999, 999, 999.99, 0, 999.99, 999.99),
(3, 1, 20.00, 'Unsynced Device Name 3', 'Unsynced SKU 3', 999, 999, 999, 999, 999.99, 0, 999.99, 999.99);

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
(1, 1, 'Unsynced SKU 1', 'Unsynced Option Name 1', 'Add Description!', 999.99, 1, 0),
(2, 1, 'Unsynced SKU 2', 'Unsynced Option Name 2', 'Add Description!', 999.99, 1, 0),
(3, 1, 'Unsynced SKU 3', 'Unsynced Option Name 3', 'Add Description!', 999.99, 1, 0),
(4, 2, 'Unsynced SKU 4', 'Unsynced Option Name 4', 'Add Description!', 999.99, 1, 0),
(5, 2, 'Unsynced SKU 5', 'Unsynced Option Name 5', 'Add Description!', 999.99, 1, 0),
(6, 2, 'Unsynced SKU 6', 'Unsynced Option Name 6', 'Add Description!', 999.99, 1, 0),
(7, 3, 'Unsynced SKU 7', 'Unsynced Option Name 7', 'Add Description!', 999.99, 1, 0),
(8, 3, 'Unsynced SKU 8', 'Unsynced Option Name 8', 'Add Description!', 999.99, 1, 0);

INSERT INTO `qgen_quote_device_configuration_options` (`quoteDeviceOptionId`, `optionId`, `masterDeviceId`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 4, 1),
(4, 1, 2),
(5, 3, 2),
(6, 5, 2),
(7, 1, 3),
(8, 5, 3);

