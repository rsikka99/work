SELECT
    pgen_master_devices.*,
    pgen_toner_configs.name   AS TonerConfiguration,
    manufacturers.displayname AS manufacturer
FROM `pgen_master_devices`
    JOIN pgen_toner_configs ON pgen_master_devices.tonerConfigId = pgen_toner_configs.id
    JOIN manufacturers ON pgen_master_devices.manufacturerId = manufacturers.id