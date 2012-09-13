-- System Quote settings
INSERT INTO `qgen_quote_settings` (`id`, `pageCoverageMonochrome`, `pageCoverageColor`, `deviceMargin`, `pageMargin`, `pricingConfigId`, `adminCostPerPage`, `serviceCostPerPage`, `monochromeOverageRatePerPage`, `colorOverageRatePerPage`) VALUES
(1, 4.5, 20, 15, 20, 2, 0.0035, 0.0016, 0.02, 0.09);

-- ---------------------------------
-- Create a default leasing schema
-- ---------------------------------
INSERT INTO `qgen_leasing_schemas` (`id`, `name`) VALUES
(1,'default');

INSERT INTO `qgen_leasing_schema_ranges` (`id`, `leasingSchemaId`, `startRange`) VALUES
(1, 1, 0),
(2, 1, 3000),
(3, 1, 10000),
(4, 1, 25000);


INSERT INTO `qgen_leasing_schema_terms` (`id`, `leasingSchemaId`, `months`) VALUES
(1, 1, 12),
(2, 1, 24),
(3, 1, 36),
(4, 1, 39),
(5, 1, 42),
(6, 1, 48),
(7, 1, 60),
(8, 1, 63);

INSERT INTO `qgen_leasing_schema_rates` (`leasingSchemaTermId`, `leasingSchemaRangeId`, `rate`) VALUES
(1, 1, 0.0990),
(1, 2, 0.0965),
(1, 3, 0.0954),
(1, 4, 0.0944),

(2, 1, 0.0529),
(2, 2, 0.0502),
(2, 3, 0.0501),
(2, 4, 0.0499),

(3, 1, 0.0364),
(3, 2, 0.0338),
(3, 3, 0.0336),
(3, 4, 0.0333),

(4, 1, 0.0343),
(4, 2, 0.0322),
(4, 3, 0.0320),
(4, 4, 0.0317),

(5, 1, 0.0335),
(5, 2, 0.0308),
(5, 3, 0.0301),
(5, 4, 0.0300),

(6, 1, 0.0309),
(6, 2, 0.0273),
(6, 3, 0.0269),
(6, 4, 0.0268),

(7, 1, 0.0269),
(7, 2, 0.0229),
(7, 3, 0.0222),
(7, 4, 0.0221),

(8, 1, 0.0260),
(8, 2, 0.0217),
(8, 3, 0.0214),
(8, 4, 0.0213);