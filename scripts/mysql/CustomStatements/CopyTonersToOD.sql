INSERT INTO dealer_toner_attributes (tonerId, dealerId, cost, dealerSku)
    SELECT
        pgen_toners.id  AS tonerId,
        3               AS dealerId,
        pgen_toners.cost,
        pgen_toners.sku AS dealerSku
    FROM pgen_toners;