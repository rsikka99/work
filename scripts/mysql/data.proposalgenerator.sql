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

INSERT INTO `pgen_ticket_statuses` (`id`, `name`) VALUES 
(1, 'New'      ),
(2, 'Open'     ),
(3, 'Closed'   ),
(4, 'Rejected' );

INSERT INTO `pgen_ticket_categories` (`id`, `name`) VALUES 
(1, 'Printfleet'   ),
(2, 'FM Audit'     );

/* Default System Report Settings */
INSERT INTO `pgen_report_settings` (`id`,`actualPageCoverageMono`,`actualPageCoverageColor`,`serviceCostPerPage`,`adminCostPerPage`,`assessmentReportMargin`,`grossMarginReportMargin`,`monthlyLeasePayment`,`defaultPrinterCost`,`leasedBwCostPerPage`,`leasedColorCostPerPage`,`mpsBwCostPerPage`,`mpsColorCostPerPage`,`kilowattsPerHour`,`assessmentPricingConfigId`,`grossMarginPricingConfigId`) VALUES
(1,6,24,0.0035,0.0006,20,20,250,1000,0.015,0.08,0.02,0.09,0.1,2,3);

/* Default System Survey Settings */
INSERT INTO `pgen_survey_settings` (`id`, `page_coverage_mono`, `page_coverage_color`) VALUES
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


INSERT INTO `pgen_master_devices` (`id`, `manufacturer_id`, `printer_model`, `toner_config_id`, `is_copier`, `is_scanner`, `is_fax`, `is_duplex`, `cost`, `launch_date`, `date_created`, `watts_power_normal`, `watts_power_idle`) VALUES
(1, 7, 'Color Laserjet Cm3530 Mfp', 3, 1, 1, 0, 1, 2155.90, '2008-10-07 00:00:00', NOW(), 652, 18),
(2, 7, 'Color Laserjet Cm2320nf', 3, 1, 1, 1, 0, 699.99, '2008-09-03 00:00:00', NOW(), 100, 10),
(3, 7, 'Laserjet 2200d', 1, 0, 0, 0, 1, 99.00, '2001-03-20 00:00:00', NOW(), 400, 12);

-- Clover 3
-- HP 7
-- Black: 1, Cyan: 2, Magenta: 3, Yellow: 4
-- OEM: 1, COMP: 2
INSERT INTO `pgen_toners` (`id`, `sku`, `cost`, `yield`, `part_type_id`, `manufacturer_id`, `toner_color_id`) VALUES
-- Color Laserjet Cm3530 Mfp
(1,  '866370', 224.20, 7000 , 1, 7, 2),
(2,  '866540', 224.20, 7000 , 1, 7, 3),
(3,  '866545', 224.20, 7000 , 1, 7, 4),
(4,  '866365', 160.13, 10500, 1, 7, 1),
-- Color Laserjet Cm2320nf
(5,  '287850', 104.27, 3500 , 1, 7, 1),
(6,  '287855', 102.78, 2800 , 1, 7, 2),
(7,  '287865', 102.78, 2800 , 1, 7, 3),
(8,  '287860', 102.78, 2800 , 1, 7, 4),
(9,  '699279', 55.69 , 3500 , 2, 3, 1),
(10, '699297', 55.69 , 2800 , 2, 3, 2),
(11, '699342', 55.69 , 2800 , 2, 3, 3),
(12, '699324', 55.69 , 2800 , 2, 3, 4),
-- Laserjet 2200d
(13, '808256', 88.97 , 5000 , 1, 7, 1),
(14, '775081', 30.62 , 5000 , 2, 3, 1);

INSERT INTO `pgen_device_toners` (`toner_id`, `master_device_id`) VALUES
-- Color Laserjet Cm3530 Mfp
(1, 1),
(2, 1),
(3, 1),
(4, 1),
-- Color Laserjet Cm2320nf
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
-- Laserjet 2200d
(13, 3),
(14, 3);
