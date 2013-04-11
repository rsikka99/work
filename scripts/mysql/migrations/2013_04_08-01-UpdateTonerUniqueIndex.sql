ALTER TABLE pgen_toners DROP INDEX sku, DROP INDEX sku_2;
ALTER TABLE pgen_toners ADD INDEX sku (`sku` ASC, `manufacturerId` ASC);