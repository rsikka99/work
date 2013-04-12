SELECT
    master_devices.id    AS masterDeviceId,
    manufacturers.displayname AS manufacturer,
    master_devices.modelName,
    toner_configs.name   AS TonerConfiguration,
    (SELECT COUNT(*) FROM device_toners WHERE device_toners.master_device_id = masterDeviceId) AS TonerCount
FROM `master_devices`
    JOIN toner_configs ON master_devices.tonerConfigId = toner_configs.id
    JOIN manufacturers ON master_devices.manufacturerId = manufacturers.id
    LEFT JOIN device_toners ON device_toners.master_device_id = master_devices.id
WHERE device_toners.master_device_id IS NULL AND master_devices.isLeased = 0