SELECT
    master_devices.id    AS masterDeviceId,
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
               WHERE device_toners.master_device_id = masterDeviceId AND toners.tonerColorId = 1
           ) IS TRUE, TRUE, FALSE)
    WHEN 2 THEN
        IF((
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM device_toners
                   JOIN toners ON device_toners.toner_id = toners.id
               WHERE device_toners.master_device_id = masterDeviceId AND toners.tonerColorId = 1
           ) IS TRUE
           AND
           (

               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM device_toners
                   JOIN toners ON device_toners.toner_id = toners.id
               WHERE device_toners.master_device_id = masterDeviceId AND toners.tonerColorId = 2
           ) IS TRUE
           AND
           (
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM device_toners
                   JOIN toners ON device_toners.toner_id = toners.id
               WHERE device_toners.master_device_id = masterDeviceId AND toners.tonerColorId = 3
           ) IS TRUE
           AND
           (
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM device_toners
                   JOIN toners ON device_toners.toner_id = toners.id
               WHERE device_toners.master_device_id = masterDeviceId AND toners.tonerColorId = 4
           ) IS TRUE
        , TRUE, FALSE)
    WHEN 3 THEN
        IF((
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM device_toners
                   JOIN toners ON device_toners.toner_id = toners.id
               WHERE device_toners.master_device_id = masterDeviceId AND toners.tonerColorId = 1
           ) IS TRUE
           AND
           (
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM device_toners
                   JOIN toners ON device_toners.toner_id = toners.id
               WHERE device_toners.master_device_id = masterDeviceId AND toners.tonerColorId = 5
           ) IS TRUE
        , TRUE, FALSE)
    WHEN 4 THEN
        IF((
               SELECT
                   IF(COUNT(*) > 0, TRUE, FALSE) AS ValidTonerConfiguration
               FROM device_toners
                   JOIN toners ON device_toners.toner_id = toners.id
               WHERE device_toners.master_device_id = masterDeviceId AND toners.tonerColorId = 6
           ) IS TRUE, TRUE, FALSE)
    END                       AS ValidToners,
    (
        SELECT
    COUNT(*)
        FROM device_toners
        WHERE device_toners.master_device_id = masterDeviceId
    )                         AS TonerCount,
    master_devices.isLeased
FROM `master_devices`
    JOIN toner_configs ON master_devices.tonerConfigId = toner_configs.id
    JOIN manufacturers ON master_devices.manufacturerId = manufacturers.id