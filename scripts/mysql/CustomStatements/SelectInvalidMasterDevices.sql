SELECT
    pgen_master_devices.id    AS masterDeviceId,
    manufacturers.displayname AS manufacturer,
    pgen_master_devices.modelName,
    pgen_toner_configs.name   AS TonerConfiguration,
    (SELECT COUNT(*) FROM pgen_device_toners WHERE pgen_device_toners.master_device_id = masterDeviceId) AS TonerCount
FROM `pgen_master_devices`
    JOIN pgen_toner_configs ON pgen_master_devices.tonerConfigId = pgen_toner_configs.id
    JOIN manufacturers ON pgen_master_devices.manufacturerId = manufacturers.id
    LEFT JOIN pgen_device_toners ON pgen_device_toners.master_device_id = pgen_master_devices.id
WHERE pgen_device_toners.master_device_id IS NULL AND pgen_master_devices.isLeased = 0