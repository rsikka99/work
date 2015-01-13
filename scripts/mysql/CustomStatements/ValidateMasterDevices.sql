SELECT
    master_devices.id    AS masterDeviceId,
    master_devices.manufacturerId    AS masterManufacturerId,
    manufacturers.displayname AS manufacturer,
    master_devices.modelName,
    toner_configs.name   AS TonerConfiguration,
    CASE master_devices.tonerConfigId
    WHEN 1 THEN
        IF((
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM device_toners
                   JOIN toners ON device_toners.toner_id = toners.id
               WHERE device_toners.master_device_id = masterDeviceId AND toners.tonerColorId = 1 AND toners.manufacturerId = masterManufacturerId
           ) IS TRUE, TRUE, FALSE)
    WHEN 2 THEN
        IF((
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM device_toners
                   JOIN toners ON device_toners.toner_id = toners.id
               WHERE device_toners.master_device_id = masterDeviceId AND toners.tonerColorId = 1 AND toners.manufacturerId = masterManufacturerId
           ) IS TRUE
           AND
           (

               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM device_toners
                   JOIN toners ON device_toners.toner_id = toners.id
               WHERE device_toners.master_device_id = masterDeviceId AND toners.tonerColorId = 2 AND toners.manufacturerId = masterManufacturerId
           ) IS TRUE
           AND
           (
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM device_toners
                   JOIN toners ON device_toners.toner_id = toners.id
               WHERE device_toners.master_device_id = masterDeviceId AND toners.tonerColorId = 3 AND toners.manufacturerId = masterManufacturerId
           ) IS TRUE
           AND
           (
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM device_toners
                   JOIN toners ON device_toners.toner_id = toners.id
               WHERE device_toners.master_device_id = masterDeviceId AND toners.tonerColorId = 4 AND toners.manufacturerId = masterManufacturerId
           ) IS TRUE
        , TRUE, FALSE)
    WHEN 3 THEN
        IF((
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM device_toners
                   JOIN toners ON device_toners.toner_id = toners.id
               WHERE device_toners.master_device_id = masterDeviceId AND toners.tonerColorId = 1 AND toners.manufacturerId = masterManufacturerId
           ) IS TRUE
           AND
           (
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM device_toners
                   JOIN toners ON device_toners.toner_id = toners.id
               WHERE device_toners.master_device_id = masterDeviceId AND toners.tonerColorId = 5 AND toners.manufacturerId = masterManufacturerId
           ) IS TRUE
        , TRUE, FALSE)
    WHEN 4 THEN
        IF((
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM device_toners
                   JOIN toners ON device_toners.toner_id = toners.id
               WHERE device_toners.master_device_id = masterDeviceId AND toners.tonerColorId = 6 AND toners.manufacturerId = masterManufacturerId
           ) IS TRUE, TRUE, FALSE)
    END                       AS ValidToners,
    (
        SELECT
    COUNT(*)
        FROM device_toners
            JOIN toners ON device_toners.toner_id = toners.id
        WHERE device_toners.master_device_id = masterDeviceId AND toners.manufacturerId = masterManufacturerId
    )                         AS OEMTonerCount,
    (
        SELECT
    COUNT(*)
        FROM device_toners
            JOIN toners ON device_toners.toner_id = toners.id
        WHERE device_toners.master_device_id = masterDeviceId AND toners.manufacturerId != masterManufacturerId
    )                         AS COMPTonerCount,
    master_devices.isLeased
FROM `master_devices`
    JOIN toner_configs ON master_devices.tonerConfigId = toner_configs.id
    JOIN manufacturers ON master_devices.manufacturerId = manufacturers.id
WHERE master_devices.isLeased = 0
ORDER BY ValidToners ASC, manufacturers.fullname ASC, master_devices.modelName ASC