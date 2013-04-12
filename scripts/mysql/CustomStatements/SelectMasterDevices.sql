SELECT
    master_devices.*,
    toner_configs.name   AS TonerConfiguration,
    manufacturers.displayname AS manufacturer
FROM `master_devices`
    JOIN toner_configs ON master_devices.tonerConfigId = toner_configs.id
    JOIN manufacturers ON master_devices.manufacturerId = manufacturers.id