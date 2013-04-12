SELECT
    toners.id            AS TonerId,
    manufacturers.displayname AS Manufacturer,
    toner_colors.name    AS TonerColor,
    ' '                       AS MFGSKU,
    toners.sku           AS ODSKU,
    toners.cost          AS ODCOST,
    toners.yield
FROM `toners`
    JOIN toner_colors ON toners.tonerColorId = toner_colors.id
    JOIN manufacturers ON toners.manufacturerId = manufacturers.id