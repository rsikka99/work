INSERT INTO `quotegen_categories` (`id`, `name`, `description`) VALUES
(1,'Toner','This belongs to toner'),
(2,'Paper','This item belongs to anything to do with paper'),
(3,'Color Pages','Pages'),
(4,'Paper Tray','Any items related to paper');

INSERT INTO `quotegen_options` (`id`, `name`,`description`,`price`,`sku`) VALUES
(1, 'Paper tray', 'A nice paper try', 3.00,' ABC123'),
(2, 'Hard drive', 'A big hard drive', 44.00, 'ZXY123'),
(3, 'Color toner', 'Yay, you get color toner', 22.00, 'ZXY321'),
(4, 'Finisher', 'One that is the best', 320.00, 'ABC321'),
(5, 'Wifi', 'I can has wifis', 225.00, 'ASDF12'),
(6, 'Color pages', 'Yay you get color pages', 35.00, 'WASD23');

INSERT INTO `quotegen_option_categories` (`optionId`, `categoryId`) VALUES
(1,1),
(1,2),
(3,1),
(4,4),
(6,3),
(6,1);

INSERT INTO `quotegen_quote_settings` (`id`, `pageCoverageMonochrome`, `pageCoverageColor`, `deviceMargin`, `pageMargin`, `pricingConfigId`) VALUES
(1, 6.00, 24.00, 20, 20, 2);

INSERT INTO `quotegen_quote_settings` (`id`) VALUES
(2),
(3);

INSERT INTO `quotegen_user_quote_settings` (`userId`,`quoteSettingId`) VALUES
(1,2),
(2,3);


INSERT INTO `quotegen_leasing_schemas` (`id`, `name`) VALUES
(1,'default');

INSERT INTO `quotegen_leasing_schema_ranges` (`id`, `leasingSchemaId`, `startRange`) VALUES
(1, 1, '0');

INSERT INTO `quotegen_leasing_schema_terms` (`id`, `leasingSchemaId`, `months`) VALUES
(1, 1, 12);

INSERT INTO `quotegen_leasing_schema_rates` (`leasingSchemaTermId`, `leasingSchemaRangeId`, `rate`) VALUES
(1, 1, '0.0955');