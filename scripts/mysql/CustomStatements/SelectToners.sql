SELECT
    pgen_toners.id            AS TonerId,
    manufacturers.displayname AS Manufacturer,
    pgen_toner_colors.name    AS TonerColor,
    ' '                       AS MFGSKU,
    pgen_toners.sku           AS ODSKU,
    pgen_toners.cost          AS ODCOST,
    pgen_toners.yield
FROM `pgen_toners`
    JOIN pgen_toner_colors ON pgen_toners.tonerColorId = pgen_toner_colors.id
    JOIN manufacturers ON pgen_toners.manufacturerId = manufacturers.id