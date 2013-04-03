SELECT
    pgen_master_devices.id    AS masterDeviceId,
    manufacturers.displayname AS manufacturer,
    pgen_master_devices.modelName,
    pgen_toner_configs.name   AS TonerConfiguration,
    CASE pgen_master_devices.tonerConfigId
    WHEN 1 THEN
        IF((
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM pgen_device_toners
                   JOIN pgen_toners ON pgen_device_toners.toner_id = pgen_toners.id
               WHERE pgen_device_toners.master_device_id = masterDeviceId AND pgen_toners.tonerColorId = 1
           ) IS TRUE, TRUE, FALSE)
    WHEN 2 THEN
        IF((
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM pgen_device_toners
                   JOIN pgen_toners ON pgen_device_toners.toner_id = pgen_toners.id
               WHERE pgen_device_toners.master_device_id = masterDeviceId AND pgen_toners.tonerColorId = 1
           ) IS TRUE
           AND
           (

               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM pgen_device_toners
                   JOIN pgen_toners ON pgen_device_toners.toner_id = pgen_toners.id
               WHERE pgen_device_toners.master_device_id = masterDeviceId AND pgen_toners.tonerColorId = 2
           ) IS TRUE
           AND
           (
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM pgen_device_toners
                   JOIN pgen_toners ON pgen_device_toners.toner_id = pgen_toners.id
               WHERE pgen_device_toners.master_device_id = masterDeviceId AND pgen_toners.tonerColorId = 3
           ) IS TRUE
           AND
           (
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM pgen_device_toners
                   JOIN pgen_toners ON pgen_device_toners.toner_id = pgen_toners.id
               WHERE pgen_device_toners.master_device_id = masterDeviceId AND pgen_toners.tonerColorId = 4
           ) IS TRUE
        , TRUE, FALSE)
    WHEN 3 THEN
        IF((
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM pgen_device_toners
                   JOIN pgen_toners ON pgen_device_toners.toner_id = pgen_toners.id
               WHERE pgen_device_toners.master_device_id = masterDeviceId AND pgen_toners.tonerColorId = 1
           ) IS TRUE
           AND
           (
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM pgen_device_toners
                   JOIN pgen_toners ON pgen_device_toners.toner_id = pgen_toners.id
               WHERE pgen_device_toners.master_device_id = masterDeviceId AND pgen_toners.tonerColorId = 5
           ) IS TRUE
        , TRUE, FALSE)
    WHEN 4 THEN
        IF((
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM pgen_device_toners
                   JOIN pgen_toners ON pgen_device_toners.toner_id = pgen_toners.id
               WHERE pgen_device_toners.master_device_id = masterDeviceId AND pgen_toners.tonerColorId = 6
           ) IS TRUE, TRUE, FALSE)
    END                       AS ValidToners,
    (
        SELECT
    COUNT(*)
        FROM pgen_device_toners
        WHERE pgen_device_toners.master_device_id = masterDeviceId
    )                         AS TonerCount,
    pgen_master_devices.isLeased
FROM `pgen_master_devices`
    JOIN pgen_toner_configs ON pgen_master_devices.tonerConfigId = pgen_toner_configs.id
    JOIN manufacturers ON pgen_master_devices.manufacturerId = manufacturers.id