<?php

use Phinx\Migration\AbstractMigration;

class SynnexProducts extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('DROP TABLE IF EXISTS `synnex_products`');

        $this->execute('
CREATE TABLE IF NOT EXISTS `synnex_products` (

  `Trading_Partner_Code`      char(10) null,
  `Detail_Record_ID`          char(5)  null,
  `Manufacturer_Part`         varchar(30)  null,
  `SYNNEX_Internal_Use_1`     varchar(20) null,
  `SYNNEX_SKU`                int null,
  `Status_Code`               char(1) null,
  `Part_Description`          varchar(80) null,
  `Manufacturer_Name`         varchar(25) null,
  `SYNNEX_Internal_Use_2`     varchar(6) null,
  `Qty_on_Hand`               int null,
  `SYNNEX_Internal_Use_3`     int null,
  `SYNNEX_Internal_Use_4`     int null,
  `MSRP`                      decimal(10,2) null,
  `Warehouse_Qty_on_Hand_1`   int null,
  `Warehouse_Qty_on_Hand_2`   int null,
  `Returnable_Flag`           char(1) null,
  `Warehouse_Qty_on_Hand_3`   int null,
  `Parcel_Shippable`          char(1) null,
  `Warehouse_Qty_on_Hand_4`   int null,
  `Warehouse_Qty_on_Hand_5`   int null,
  `Media_Type`                char(4) null,
  `Warehouse_Qty_on_Hand_6`   int null,
  `SYNNEX_CAT_Code`           varchar(14) null,
  `Warehouse_Qty_on_Hand_7`   int null,
  `SYNNEX_Internal_Use_5`     int null,
  `Ship_Weight`               decimal(10,2) null,
  `Serialized_Flag`           char(1) null,
  `Warehouse_Qty_on_Hand_8`   int null,
  `Warehouse_Qty_on_Hand_9`   int null,
  `Warehouse_Qty_on_Hand_10`  int null,
  `SYNNEX_Reserved_Use_1`     varchar(10) null,
  `UPC_Code`                  varchar(14) null,
  `UNSPSC_Code`               varchar(10) null,
  `SYNNEX_Internal_Use_6`     varchar(12) null,
  `SKU_Created_Date`          char(6) null,
  `One_Source_Flag`           varchar(12) null,
  `ETA_Date`                  char(6) null,
  `ABC_Code`                  char(1) null,
  `Kit_Stand_Alone_Flag`      char(1) null,
  `State_GOV_Price`           decimal(10,2) null,
  `Federal_GOV_Price`         decimal(10,2) null,
  `EDUcational_Price`         decimal(10,2) null,
  `TAA_Flag`                  char(1) null,
  `GSA_Pricing`               decimal(10,2) null,
  `Long_Description_1`      varchar(80) null,
  `Long_Description_2`      varchar(80) null,
  `Long_Description_3`      varchar(80) null,
  `Length`                    varchar(10) null,
  `Width`                     varchar(10) null,
  `Height`                    varchar(10) null,
  `Warehouse_Qty_on_Hand_11`  int null,
  `GSA_NTE_Price`             decimal(10,2) null,
  `Platform_Type`             varchar(7) null,
  `Product_Description_FR`  varchar(100) null,
  `SYNNEX_Reserved_Use_2`     varchar(10) null,
  `Warehouse_Qty_on_Hand_12`  int null,
  `Warehouse_Qty_on_Hand_13`  int null,
  `Warehouse_Qty_on_Hand_14`  int null,
  `Warehouse_Qty_on_Hand_15`  int null,
  `Replacement_Sku`           int null,
  `Minimum_Order_Qty`         int null,
  `Purchasing_Requirements`   varchar(8) null,
  `Gov_Class`                 varchar(12) null,
  `Warehouse_Qty_on_Hand_16`  int null,
  `MFG_Drop_Ship_Warehouse_QTY`  int null,

  `tonerId` int(11) DEFAULT NULL,
  `masterDeviceId` int(11) DEFAULT NULL,
  `computerId` int(11) DEFAULT NULL,
  `peripheralId` int(11) DEFAULT NULL,

  PRIMARY KEY (`SYNNEX_SKU`),
  KEY `masterDeviceId` (`masterDeviceId`),
  KEY `computerId` (`computerId`),
  KEY `peripheralId` (`peripheralId`),
  KEY `tonerId` (`tonerId`)

) ENGINE=InnoDB');

        $this->execute('DROP TABLE IF EXISTS `synnex_prices`');

        $this->execute('
CREATE TABLE IF NOT EXISTS `synnex_prices` (
  `dealerId`                            int(11) NOT NULL,
  `SYNNEX_SKU`                          int null,
  `Contract_Price`                      decimal(10,2) null,
  `Unit_Cost`                           decimal(10,2) null,
  `Promotion_Flag`                      char(1) null,
  `Promotion_Comment`                   varchar(250) null,
  `Promotion_Expiration_Date`           char(8) null,
  PRIMARY KEY (`dealerId`,`SYNNEX_SKU`),
  KEY `synnex_part_number` (`SYNNEX_SKU`),
  CONSTRAINT FOREIGN KEY (`dealerId`) REFERENCES `dealers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`SYNNEX_SKU`) REFERENCES `synnex_products` (`SYNNEX_SKU`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB');

        $this->execute('drop view if exists _view_dealer_toner_costs');
        $this->execute('create view _view_dealer_toner_costs as
SELECT dealerId, tonerId, cost, 1 as isUsingDealerPricing
	FROM dealer_toner_attributes
	where cost is not null
union
select dealerId, tonerId, customer_price as cost, 2 as isUsingDealerPricing
	from ingram_prices
	join ingram_products on ingram_prices.ingram_part_number = ingram_products.ingram_part_number
	where ingram_products.tonerId is not null
union
select dealerId, tonerId, Unit_Cost as cost, 3 as isUsingDealerPricing
	from synnex_prices
	join synnex_products on synnex_prices.SYNNEX_SKU = synnex_products.SYNNEX_SKU
	where synnex_products.tonerId is not null');

    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('DROP TABLE IF EXISTS `synnex_prices`');
        $this->execute('DROP TABLE IF EXISTS `synnex_products`');
    }
}