INSERT INTO `pgen_master_devices` (`id`, `manufacturer_id`, `printer_model`, `toner_config_id`, `is_copier`, `is_scanner`, `is_fax`, `is_duplex`, `cost`, `launch_date`, `date_created`, `watts_power_normal`, `watts_power_idle`) VALUES
(1, 7, 'Color Laserjet Cm3530 Mfp', 2, 1, 1, 0, 1, 2155.90, '2008-10-07 00:00:00', NOW(), 652, 18),
(2, 7, 'Color Laserjet Cm2320nf', 2, 1, 1, 1, 0, 699.99, '2008-09-03 00:00:00', NOW(), 100, 10),
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


