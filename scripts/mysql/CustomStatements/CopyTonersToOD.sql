INSERT INTO dealer_toner_attributes (tonerId, dealerId, cost, dealerSku)
    SELECT
        toners.id  AS tonerId,
        3               AS dealerId,
        toners.cost,
        toners.sku AS dealerSku
    FROM toners;