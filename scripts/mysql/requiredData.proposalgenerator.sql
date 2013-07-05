INSERT INTO `rms_providers` (`id`, `name`) VALUES
(1, 'PrintFleet'),
(2, 'FM Audit'),
(3, 'Xerox');

INSERT INTO `part_types` (`id`, `name`) VALUES
(1, 'OEM'),
(2, 'COMP');

INSERT INTO `pricing_configs` (`id`, `name`, `color_toner_part_type_id`, `mono_toner_part_type_id`) VALUES
(1, 'USE DEFAULT', NULL, NULL),
(2, 'OEM', 1, 1),
(3, 'COMP', 2, 2),
(4, 'OEM Mono, COMP Color', 2, 1),
(5, 'OEM Color, COMP Mono', 1, 2);

INSERT INTO `toner_configs` (`id`, `name`) VALUES
(1, 'BLACK ONLY'),
(2, '3 COLOR - SEPARATED'),
(3, '3 COLOR - COMBINED'),
(4, '4 COLOR - COMBINED');

INSERT INTO `toner_colors` (`id`, `name`) VALUES
(1, 'BLACK'),
(2, 'CYAN'),
(3, 'MAGENTA'),
(4, 'YELLOW'),
(5, '3 COLOR'),
(6, '4 COLOR');

INSERT INTO `toner_vendor_ranking_sets` (`id`) VALUES
(1),
(2),
(3),
(4);

INSERT INTO `toner_vendor_rankings` (`tonerVendorRankingSetId`,`manufacturerId`,`rank`) VALUES
(1,1,1),
(2,1,1),
(3,1,1),
(4,1,1);


/* Default System Report Settings */
INSERT INTO `assessment_settings` (`id`, `actualPageCoverageMono`, `actualPageCoverageColor`, `laborCostPerPage`, `partsCostPerPage`, `adminCostPerPage`, `assessmentReportMargin`, `grossMarginReportMargin`, `monthlyLeasePayment`, `defaultPrinterCost`, `leasedBwCostPerPage`, `leasedColorCostPerPage`, `mpsBwCostPerPage`, `mpsColorCostPerPage`, `kilowattsPerHour`, `customerMonochromeRankSetId`, `customerColorRankSetId`, `dealerMonochromeRankSetId`, `dealerColorRankSetId`, `costThreshold`, `targetMonochromeCostPerPage`, `targetColorCostPerPage`) VALUES
(1, 6, 24, 0.002, 0.0015, 0.0006, 20, 20, 250, 1000, 0.015, 0.08, 0.02, 0.09, 0.1, 1, 2, 3, 4, 10, 0.018, 0.02);

/* Default System Survey Settings */
INSERT INTO `survey_settings` (`id`, `pageCoverageMono`, `pageCoverageColor`) VALUES
(1, 6, 24);

/* Default System Report Settings */
INSERT INTO `healthcheck_settings` (`id`, `pageCoverageMonochrome`,`pageCoverageColor`,`actualPageCoverageMono`, `actualPageCoverageColor`, `laborCostPerPage`, `partsCostPerPage`, `adminCostPerPage`, `healthcheckMargin`, `monthlyLeasePayment`, `defaultPrinterCost`, `leasedBwCostPerPage`, `leasedColorCostPerPage`, `mpsBwCostPerPage`, `mpsColorCostPerPage`, `kilowattsPerHour`, `healthcheckPricingConfigId`, `averageItHourlyRate`,`costToExecuteSuppliesOrder`,`numberOfSupplyOrdersPerMonth`) VALUES
(1, 6, 24, 6, 24, 0.002, 0.0015, 0.0006, 20, 250, 1000, 0.015, 0.08, 0.02, 0.09, 0.1, 2, 40,50,22);

INSERT INTO `hardware_optimization_settings` (`id`, `pageCoverageMonochrome`,`pageCoverageColor`,`costThreshold`,`adminCostPerPage`, `laborCostPerPage`, `partsCostPerPage`, `dealerPricingConfigId`, `targetColorCostPerPage`, `targetMonochromeCostPerPage`, `replacementPricingConfigId`) VALUES
(1,  6, 24, 20, 0.0006, 0.002, 0.0015, 3, 0.10, 0.02, 3);
