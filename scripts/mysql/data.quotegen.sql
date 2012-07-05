INSERT INTO `quotegen_categories` (`id`, `name`, `description`) VALUES
(1,'Paper Tray','Increases the paper capacity of a printer'),
(2,'Finishers','These perform various tasks to finish off your prints'),
(3,'Networking','Allows a printer to gain network connectivity'),
(4,'Hard Drives','Hard Drives');

INSERT INTO `quotegen_options` (`id`, `name`,`description`,`price`,`sku`) VALUES
(1, '500 Sheet Paper tray', 'The standard 500 sheet paper tray', 3.00,'PPRTRY-500'),
(2, '40 GB Hard drive', '40gb Hard Drive', 44.00, 'HDD-WD40'),
(3, '60 GB Hard drive', '60 GB Hard Drive', 22.00, 'HDD-WD60'),
(4, 'Stapler/Collator', 'Staples and collates pages', 320.00, 'FNSHRSTPLCOL'),
(5, 'Stapler', 'Staples papers together to form booklets', 225.00, 'FNSHRSTPL'),
(6, 'WiFi Adapter', 'a/b/g/n WiFi adapter', 35.00, 'NTWKWIFI-ABGN-11');

INSERT INTO `quotegen_option_categories` (`optionId`, `categoryId`) VALUES
(1,1),
(1,4),
(3,4),
(4,2),
(6,2),
(6,3);

INSERT INTO `quotegen_quote_settings` (`id`, `pageCoverageMonochrome`, `pageCoverageColor`, `deviceMargin`, `pageMargin`, `pricingConfigId`) VALUES
(1, 6.00, 24.00, 20, 20, 2);

INSERT INTO `quotegen_quote_settings` (`id`) VALUES
(2),
(3);

INSERT INTO `quotegen_user_quote_settings` (`userId`,`quoteSettingId`) VALUES
(1,2),
(2,3);

-- Add Devices
INSERT INTO `quotegen_devices` (`masterDeviceId`, `sku`) VALUES 
(1, 'FAKESKU3123'),
(2, 'FAKESKU4113'),
(3, 'FAKESKU7553');

-- Add Options To the Devices
INSERT INTO `quotegen_device_options` (`masterDeviceId`, `optionId`) VALUES
(1, 1),
(1, 2),
(1, 4),

(2, 1),
(2, 3),
(2, 5),

(3, 1),
(3, 5);

-- Add some leasing schemas
INSERT INTO `quotegen_leasing_schemas` (`id`, `name`) VALUES
(1,'default');

INSERT INTO `quotegen_leasing_schema_ranges` (`id`, `leasingSchemaId`, `startRange`) VALUES
(1, 1, '0');

INSERT INTO `quotegen_leasing_schema_terms` (`id`, `leasingSchemaId`, `months`) VALUES
(1, 1, 12);

INSERT INTO `quotegen_leasing_schema_rates` (`leasingSchemaTermId`, `leasingSchemaRangeId`, `rate`) VALUES
(1, 1, '0.0955');

-- ---------------------------------
-- Create a quote with some devices.
-- ---------------------------------
INSERT INTO `quotegen_quotes` (`id`, `clientId`, `dateCreated`, `dateModified`, `quoteDate`, `userId`, `clientDisplayName`, `leaseRate`, `leaseTerm`) VALUES
(1, 2, NOW(), NOW(), NOW(), 1, NULL, NULL, NULL);

INSERT INTO `quotegen_quote_devices` (`id`, `quoteId`, `margin`, `name`, `sku`, `oemCostPerPageMonochrome`, `oemCostPerPageColor`, `compCostPerPageMonochrome`, `compCostPerPageColor`, `price`, `quantity`, `packagePrice`, `residual`) VALUES
(1, 1, 20.00, 'Unsynced Device Name 1', 'Unsynced SKU 1', 999, 999, 999, 999, 999.99, 1, 999.99, 0),
(2, 1, 20.00, 'Unsynced Device Name 2', 'Unsynced SKU 2', 999, 999, 999, 999, 999.99, 1, 999.99, 0),
(3, 1, 20.00, 'Unsynced Device Name 3', 'Unsynced SKU 3', 999, 999, 999, 999, 999.99, 1, 999.99, 0);

INSERT INTO `quotegen_quote_device_configurations` (`masterDeviceId`, `quoteDeviceId`) VALUES
(1, 1),
(2, 2),
(3, 3);

INSERT INTO `quotegen_quote_device_options` (`id`, `quoteDeviceId`, `sku`, `name`, `description`, `price`, `quantity`, `includedQuantity`) VALUES
(1, 1, 'Unsynced SKU 1', 'Unsynced Option Name 1', 'Add Description!', 999.99, 1, 0),
(2, 1, 'Unsynced SKU 2', 'Unsynced Option Name 2', 'Add Description!', 999.99, 1, 0),
(3, 1, 'Unsynced SKU 3', 'Unsynced Option Name 3', 'Add Description!', 999.99, 1, 0),
(4, 2, 'Unsynced SKU 4', 'Unsynced Option Name 4', 'Add Description!', 999.99, 1, 0),
(5, 2, 'Unsynced SKU 5', 'Unsynced Option Name 5', 'Add Description!', 999.99, 1, 0),
(6, 2, 'Unsynced SKU 6', 'Unsynced Option Name 6', 'Add Description!', 999.99, 1, 0),
(7, 3, 'Unsynced SKU 7', 'Unsynced Option Name 7', 'Add Description!', 999.99, 1, 0),
(8, 3, 'Unsynced SKU 8', 'Unsynced Option Name 8', 'Add Description!', 999.99, 1, 0);

INSERT INTO `quotegen_quote_device_configuration_options` (`quoteDeviceOptionId`, `optionId`) VALUES
(1, 1),
(2, 2),
(3, 4),
(4, 1),
(5, 3),
(6, 5),
(7, 1),
(8, 5);

