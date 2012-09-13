INSERT INTO `pgen_master_devices` (`id`, `manufacturer_id`, `printer_model`, `toner_config_id`, `is_copier`, `is_fax`, `is_scanner`, `is_duplex`, `is_replacement_device`, `watts_power_normal`, `watts_power_idle`, `cost`, `service_cost_per_page`, `launch_date`, `date_created`, `duty_cycle`, `ppm_black`, `ppm_color`, `is_leased`, `leased_toner_yield`) VALUES
(4, 7, 'Color LaserJet CM6040f', 2, 1, 1, 1, 1, 0, NULL, 10, 7683.54, NULL, '2011-02-26 00:00:00', '2012-09-12 11:04:21', NULL, NULL, NULL, 0, NULL),
(5, 7, 'LaserJet M9050', 1, 1, 0, 1, 1, 0, NULL, 10, 8970.12, NULL, '2012-07-10 00:00:00', '2012-09-12 11:07:43', NULL, NULL, NULL, 0, NULL),
(6, 7, 'LaserJet Enterprise 500 M525dn MFP', 1, 1, 0, 1, 1, 0, NULL, 10, 1572.91, NULL, '2012-09-01 00:00:00', '2012-09-12 11:10:01', NULL, NULL, NULL, 0, NULL),
(7, 7, 'LaserJet 600 M602DN', 1, 0, 0, 0, 1, 0, NULL, 10, 1116.89, NULL, '2012-04-19 00:00:00', '2012-09-12 11:13:17', NULL, NULL, NULL, 0, NULL),
(8, 26, 'ColorQube 8870', 2, 0, 0, 0, 1, 0, NULL, 10, 2010.06, NULL, '2010-10-18 00:00:00', '2012-09-12 11:16:32', NULL, NULL, NULL, 0, NULL),
(9, 7, 'LaserJet Enterprise M4555fskm MFP', 1, 1, 1, 1, 1, 0, NULL, 10, 4057.56, NULL, '2011-04-30 00:00:00', '2012-09-12 11:18:26', NULL, NULL, NULL, 0, NULL);

INSERT INTO `pgen_toners` (`id`, `sku`, `cost`, `yield`, `part_type_id`, `manufacturer_id`, `toner_color_id`) VALUES
(15, '444670', 290.25, 21000, 1, 7, 2),
(16, '444705', 290.29, 21000, 1, 7, 3),
(17, '444690', 290.26, 21000, 1, 7, 4),
(18, '395025', 49.05, 19500, 1, 7, 1),
(19, '743161', 244.67, 30000, 1, 7, 1),
(20, '164107', 133.57, 30000, 2, 3, 1),
(21, '554553', 186.28, 12500, 1, 7, 1),
(22, '646593', 237.11, 24000, 1, 7, 1),
(23, 'S7938358', 77.31, 17300, 1, 26, 2),
(24, 'S7938359', 77.31, 17300, 1, 26, 3),
(25, 'S7938360', 77.31, 17300, 1, 26, 4),
(26, 'S7938361', 222.25, 16700, 1, 26, 1);

INSERT INTO `pgen_device_toners` (`toner_id`, `master_device_id`) VALUES
(15, 4),
(16, 4),
(17, 4),
(18, 4),
(19, 5),
(20, 5),
(21, 6),
(22, 7),
(23, 8),
(24, 8),
(25, 8),
(26, 8),
(22, 9);