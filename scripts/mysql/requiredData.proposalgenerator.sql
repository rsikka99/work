INSERT INTO `rms_providers` (`id`, `name`) VALUES
(1, 'PrintFleet'),
(2, 'FM Audit'),
(3, 'Xerox');

INSERT INTO `part_types` (`id`, `name`) VALUES
(1, 'OEM'   ),
(2, 'COMP'  );

INSERT INTO `pricing_configs` (`id` , `name`, `color_toner_part_type_id`, `mono_toner_part_type_id`) VALUES
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
(1,'BLACK'),
(2,'CYAN'),
(3,'MAGENTA'),
(4,'YELLOW'),
(5,'3 COLOR'),
(6,'4 COLOR');

/* Default System Report Settings */
INSERT INTO `report_settings` (`id`,`actualPageCoverageMono`,`actualPageCoverageColor`,`laborCostPerPage`,`partsCostPerPage`,`adminCostPerPage`,`assessmentReportMargin`,`grossMarginReportMargin`,`monthlyLeasePayment`,`defaultPrinterCost`,`leasedBwCostPerPage`,`leasedColorCostPerPage`,`mpsBwCostPerPage`,`mpsColorCostPerPage`,`kilowattsPerHour`,`assessmentPricingConfigId`,`grossMarginPricingConfigId`,`costThreshold`, `targetMonochromeCostPerPage`,`targetColorCostPerPage`, `replacementPricingConfigId`) VALUES
(1,6,24,0.002,0.0015,0.0006,20,20,250,1000,0.015,0.08,0.02,0.09,0.1,2,3,10,0.018,0.02, 3);

/* Default System Survey Settings */
INSERT INTO `survey_settings` (`id`, `pageCoverageMono`, `pageCoverageColor`) VALUES
(1,6,24);

INSERT INTO `dealer_survey_settings` (`dealerId`, `surveySettingId`) VALUES
(1,1);

INSERT INTO `dealer_report_settings` (`dealerId`, `reportSettingId`) VALUES
(1,1);