INSERT INTO `pgen_rms_providers` (`id`, `name`) VALUES
(1, 'PrintFleet'),
(2, 'FM Audit'),
(3, 'Xerox');

INSERT INTO `pgen_part_types` (`id`, `name`) VALUES
(1, 'OEM'   ),
(2, 'COMP'  );

INSERT INTO `pgen_pricing_configs` (`id` , `name`, `color_toner_part_type_id`, `mono_toner_part_type_id`) VALUES
(1, 'USE DEFAULT', NULL, NULL),
(2, 'OEM', 1, 1),
(3, 'COMP', 2, 2),
(4, 'OEM Mono, COMP Color', 2, 1),
(5, 'OEM Color, COMP Mono', 1, 2);
    
INSERT INTO `pgen_toner_configs` (`id`, `name`) VALUES
(1, 'BLACK ONLY'),
(2, '3 COLOR - SEPARATED'),
(3, '3 COLOR - COMBINED'),
(4, '4 COLOR - COMBINED');

INSERT INTO `pgen_toner_colors` (`id`, `name`) VALUES
(1,'BLACK'),
(2,'CYAN'),
(3,'MAGENTA'),
(4,'YELLOW'),
(5,'3 COLOR'),
(6,'4 COLOR');

/* Default System Report Settings */
INSERT INTO `pgen_report_settings` (`id`,`actualPageCoverageMono`,`actualPageCoverageColor`,`serviceCostPerPage`,`adminCostPerPage`,`assessmentReportMargin`,`grossMarginReportMargin`,`monthlyLeasePayment`,`defaultPrinterCost`,`leasedBwCostPerPage`,`leasedColorCostPerPage`,`mpsBwCostPerPage`,`mpsColorCostPerPage`,`kilowattsPerHour`,`assessmentPricingConfigId`,`grossMarginPricingConfigId`,`costThreshold`, `targetMonochrome`,`targetColor`) VALUES
(1,6,24,0.0035,0.0006,20,20,250,1000,0.015,0.08,0.02,0.09,0.1,2,3,10,0.018,0.02);

/* Default System Survey Settings */
INSERT INTO `pgen_survey_settings` (`id`, `pageCoverageMono`, `pageCoverageColor`) VALUES
(1,6,24);

/*
Question Set data
*/
INSERT INTO `pgen_question_sets` (`id`, `name`) VALUES
(1, 'Office Depot Questions ver1');

INSERT INTO `pgen_questions` (`id`, `description`) VALUES
(4, 'The name of the company the report is being created for'),
(5, 'Number of employees on site'),
(6, 'Rate MPS Goals: Ensure print hardware matches print volume needs'),
(7, 'Rate MPS Goals: Increase uptime and productivity for your employees'),
(8, 'Rate MPS Goals: Streamline logistics for supplies, service and hardware acquisition'),
(9, 'Rate MPS Goals: Reduce environmental impact'),
(10, 'Rate MPS Goals: Reduce costs'),
(11, 'Yearly Ink and Toner Costs (Excl. leased copiers)'),
(12, 'Yearly Service Costs (Excl. leased copiers)'),
(14, 'Average supplies purchase order cost'),
(15, 'Average hourly rate for IT personnel'),
(16, 'How many printer supplies/service/hardware vendors do you deal with'),
(17, 'How many times per month do you order supplies'),
(18, 'Hours per week for IT peronnel spent servicing/supporting printers'),
(19, 'IP based location tracking mechanism for printers?'),
(20, 'Average printer repair time'),
(21, 'Estimated B&W page coverage'),
(22, 'Estimated Color page coverage'),
(23, 'Percentage print volume on inkjet/desktop printers'),
(24, 'Monthly repair calls for broken printers'),
(30, 'Company Address');

INSERT INTO `pgen_questionset_questions` (`question_id`, `questionset_id`) VALUES
(4,1),
(5,1),
(6,1),
(7,1),
(8,1),
(9,1),
(10,1),
(11,1),
(12,1),      
(14,1),
(15,1),
(16,1),
(17,1),
(18,1),      
(19,1),
(20,1),
(21,1),
(22,1),
(23,1),
(24,1),      
(30,1);