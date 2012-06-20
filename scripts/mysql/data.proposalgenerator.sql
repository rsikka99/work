INSERT INTO `proposalgenerator_part_types` (`id`, `name`) VALUES
(1, 'OEM'   ),
(2, 'COMP'  );

INSERT INTO `proposalgenerator_pricing_configs` (`id` , `name`, `color_toner_part_type_id`, `mono_toner_part_type_id`) VALUES
(1, 'NONE SELECTED', NULL, NULL),
(2, 'OEM', 1, 1),
(3, 'COMP', 2, 2),
(4, 'OEM Mono, COMP Color', 2, 1),
(5, 'OEM Color, COMP Mono', 1, 2);
    
INSERT INTO `proposalgenerator_toner_configs` (`id`, `name`) VALUES
(1, 'BLACK ONLY'),
(2, '3 COLOR - SEPARATED'),
(3, '3 COLOR - COMBINED'),
(4, '4 COLOR - COMBINED');

INSERT INTO `proposalgenerator_toner_colors` (`id`, `name`) VALUES
(1,'BLACK'),
(2,'CYAN'),
(3,'MAGENTA'),
(4,'YELLOW'),
(5,'3 COLOR'),
(6,'4 COLOR');

INSERT INTO `proposalgenerator_ticket_statuses` (`id`, `name`) VALUES 
(1, 'New'      ),
(2, 'Open'     ),
(3, 'Closed'   ),
(4, 'Rejected' );

INSERT INTO `proposalgenerator_ticket_categories` (`id`, `name`) VALUES 
(1, 'Printfleet'   ),
(2, 'FM Audit'     );

/* Default System Report Settings */
INSERT INTO `proposalgenerator_report_settings` (`id`,`actualPageCoverageMono`,`actualPageCoverageColor`,`serviceCostPerPage`,`adminCostPerPage`,`assessmentReportMargin`,`grossMarginReportMargin`,`monthlyLeasePayment`,`defaultPrinterCost`,`leasedBwCostPerPage`,`leasedColorCostPerPage`,`mpsBwCostPerPage`,`mpsColorCostPerPage`,`kilowattsPerHour`,`assessmentPricingConfigId`,`grossMarginPricingConfigId`) VALUES
(1,6,24,0.0035,0.0006,20,20,250,1000,0.015,0.08,0.02,0.09,0.1,2,3);

/* Default System Survey Settings */
INSERT INTO `proposalgenerator_survey_settings` (`id`, `page_coverage_mono`, `page_coverage_color`) VALUES
(1,6,24);

/*
Question Set data
*/
INSERT INTO `proposalgenerator_question_sets` (`id`, `name`) VALUES
(1, 'Office Depot Questions ver1');

INSERT INTO `proposalgenerator_questions` (`id`, `description`) VALUES
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

INSERT INTO `proposalgenerator_questionset_questions` (`question_id`, `questionset_id`) VALUES
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


INSERT INTO `proposalgenerator_master_devices` (`id`, `manufacturer_id`, `printer_model`, `toner_config_id`, `is_copier`, `is_scanner`, `is_fax`, `is_duplex`, `device_price`, `launch_date`, `date_created`, `watts_power_normal`, `watts_power_idle`) VALUES
(1, 7, 'Color Laserjet Cm3530 Mfp', 3, 1, 1, 0, 1, 2155.90, '2008-10-07 00:00:00', NOW(), 652, 18),
(2, 7, 'Color Laserjet Cm2320nf', 3, 1, 1, 1, 0, 699.99, '2008-09-03 00:00:00', NOW(), 100, 10),
(3, 7, 'Laserjet 2200d', 1, 0, 0, 0, 1, 99.00, '2001-03-20 00:00:00', NOW(), 400, 12);