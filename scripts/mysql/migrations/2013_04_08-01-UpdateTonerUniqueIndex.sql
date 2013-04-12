ALTER TABLE toners DROP INDEX sku, DROP INDEX sku_2;
ALTER TABLE toners ADD INDEX sku (`sku` ASC, `manufacturerId` ASC);