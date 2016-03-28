<?php

use Phinx\Migration\AbstractMigration;

class TechData extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("replace into suppliers set id=3, name='Tech Data'");

        $this->execute('drop table IF EXISTS techdata_prices');
        $this->execute('drop table IF EXISTS techdata_products');

        $this->execute('
create table IF NOT EXISTS techdata_products(
Matnr bigint not null,
Qty int default null,
ShortDescription varchar(255) default null,
LongDescription varchar(255) default null,
ManufPartNo varchar(255) default null,
Manufacturer varchar(255) default null,
ManufacturerGlobalDescr varchar(255) default null,
GTIN varchar(255) default null,
ProdFamilyID varchar(255) default null,
ProdFamily varchar(255) default null,
ProdClassID varchar(255) default null,
ProdClass varchar(255) default null,
ProdSubClassID varchar(255) default null,
ProdSubClass varchar(255) default null,
ArticleCreationDate varchar(255) default null,
CNETavailable varchar(255) default null,
CNETid varchar(255) default null,
ListPrice varchar(255) default null,
Weight varchar(255) default null,
Length varchar(255) default null,
Width varchar(255) default null,
Heigth varchar(255) default null,
NoReturn varchar(255) default null,
MayRequireAuthorization varchar(255) default null,
EndUserInformation varchar(255) default null,
FreightPolicyException varchar(255) default null,
tonerId int default null,
masterDeviceId int default null,
computerId int default null,
peripheralId int default null,
PRIMARY KEY (`Matnr`),
KEY ( `tonerId` ),
KEY ( `masterDeviceId` ),
KEY ( `computerId` ),
KEY ( `peripheralId` )
) ENGINE=InnoDB;
        ');

        $this->execute('
CREATE TABLE IF NOT EXISTS `techdata_prices` (
  `dealerId` int not null,
  `Matnr` bigint NOT NULL,
    `CustBestPrice` decimal(12,2) DEFAULT 0,
  `Promotion` char(1) DEFAULT NULL,
  PRIMARY KEY (`dealerId`,`Matnr`),
  FOREIGN KEY ( `dealerId` ) REFERENCES `dealers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
        ');


        #--
        $this->execute('drop view if exists _view_dist_stock_price');
        $this->execute('
create view _view_dist_stock_price as
select 1 as dist, dealer_toner_attributes.tonerId, null as stock, dealer_toner_attributes.dealerId, dealer_toner_attributes.cost
    from
      dealer_toner_attributes
    where cost is not null
union
select 2 as dist, ingram_products.tonerId, ingram_products.availability_flag as stock, ingram_prices.dealerId, ingram_prices.customer_price as cost
    from
      ingram_products
      join ingram_prices on ingram_products.ingram_part_number = ingram_prices.ingram_part_number
    where ingram_products.tonerId is not null
union
select 3 as dist, synnex_products.tonerId, synnex_products.Qty_on_Hand as stock, synnex_prices.dealerId, synnex_prices.Unit_Cost as cost
    from
      synnex_products
      join synnex_prices on synnex_products.SYNNEX_SKU = synnex_prices.SYNNEX_SKU
    where synnex_products.tonerId is not null
union
select 4 as dist, techdata_products.tonerId, techdata_products.Qty as stock, techdata_prices.dealerId, currency_exchange.rate * techdata_prices.CustBestPrice as cost
    from
      techdata_products
      join techdata_prices on techdata_products.Matnr = techdata_prices.Matnr
      join dealers on dealers.id = techdata_prices.dealerId
      join currency_exchange on currency_exchange.currency = dealers.currency
    where techdata_products.tonerId is not null
');

        $this->execute('drop view if exists _view_dist_stock_price_in_stock');
        $this->execute("
create view _view_dist_stock_price_in_stock as
select * from _view_dist_stock_price where (dist=2 and stock='Y') or (dist=3 and stock>0) or (dist=4 and stock>0) order by cost
");
        $this->execute('drop view if exists _view_dist_stock_price_not_in_stock');
        $this->execute("
create view _view_dist_stock_price_not_in_stock as
select * from _view_dist_stock_price where (dist=2 and stock='N') or (dist=3 and stock=0) or (dist=4 and stock=0) order by cost
");
        #--

        $this->execute('drop view if exists _view_device_dist_stock_price');
        $this->execute('
create view _view_device_dist_stock_price as
select 2 as dist, ingram_products.masterDeviceId, ingram_products.availability_flag as stock, ingram_prices.dealerId, ingram_prices.customer_price as cost
    from
      ingram_products
      join ingram_prices on ingram_products.ingram_part_number = ingram_prices.ingram_part_number
    where ingram_products.masterDeviceId is not null
union
select 3 as dist, synnex_products.masterDeviceId, synnex_products.Qty_on_Hand as stock, synnex_prices.dealerId, synnex_prices.Unit_Cost as cost
    from
      synnex_products
      join synnex_prices on synnex_products.SYNNEX_SKU = synnex_prices.SYNNEX_SKU
    where synnex_products.masterDeviceId is not null
union
select 4 as dist, techdata_products.masterDeviceId, techdata_products.Qty as stock, techdata_prices.dealerId, currency_exchange.rate * techdata_prices.CustBestPrice as cost
    from
      techdata_products
      join techdata_prices on techdata_products.Matnr = techdata_prices.Matnr
      join dealers on dealers.id = techdata_prices.dealerId
      join currency_exchange on currency_exchange.currency = dealers.currency
    where techdata_products.masterDeviceId is not null
');

        $this->execute('drop view if exists _view_device_dist_stock_price_in_stock');
        $this->execute("
create view _view_device_dist_stock_price_in_stock as
select * from _view_device_dist_stock_price where (dist=2 and stock='Y') or (dist=3 and stock>0) or (dist=4 and stock>0) order by cost
");
        $this->execute('drop view if exists _view_device_dist_stock_price_not_in_stock');
        $this->execute("
create view _view_device_dist_stock_price_not_in_stock as
select * from _view_device_dist_stock_price where (dist=2 and stock='N') or (dist=3 and stock=0) or (dist=4 and stock=0) order by cost
");
        $this->execute('drop view if exists _view_device_dist_stock_price_ordered');
        $this->execute('
create view _view_device_dist_stock_price_ordered as
select * from _view_device_dist_stock_price where dist=1
union
select * from _view_device_dist_stock_price_in_stock
union
select * from _view_device_dist_stock_price_not_in_stock
');
        $this->execute('drop view if exists _view_device_dist_stock_price_grouped');
        $this->execute('
create view _view_device_dist_stock_price_grouped as
select * from _view_device_dist_stock_price_ordered group by dealerId, masterDeviceId
');

        #--

        $this->execute('drop view if exists _view_computer_dist_stock_price');
        $this->execute('
create view _view_computer_dist_stock_price as
select 2 as dist, ingram_products.computerId, ingram_products.availability_flag as stock, ingram_prices.dealerId, ingram_prices.customer_price as cost
    from
      ingram_products
      join ingram_prices on ingram_products.ingram_part_number = ingram_prices.ingram_part_number
    where ingram_products.computerId is not null
union
select 3 as dist, synnex_products.computerId, synnex_products.Qty_on_Hand as stock, synnex_prices.dealerId, synnex_prices.Unit_Cost as cost
    from
      synnex_products
      join synnex_prices on synnex_products.SYNNEX_SKU = synnex_prices.SYNNEX_SKU
    where synnex_products.computerId is not null
union
select 4 as dist, techdata_products.computerId, techdata_products.Qty as stock, techdata_prices.dealerId, currency_exchange.rate * techdata_prices.CustBestPrice as cost
    from
      techdata_products
      join techdata_prices on techdata_products.Matnr = techdata_prices.Matnr
      join dealers on dealers.id = techdata_prices.dealerId
      join currency_exchange on currency_exchange.currency = dealers.currency
    where techdata_products.computerId is not null
');

        $this->execute('drop view if exists _view_computer_dist_stock_price_in_stock');
        $this->execute("
create view _view_computer_dist_stock_price_in_stock as
select * from _view_computer_dist_stock_price where (dist=2 and stock='Y') or (dist=3 and stock>0) or (dist=4 and stock>0) order by cost
");
        $this->execute('drop view if exists _view_computer_dist_stock_price_not_in_stock');
        $this->execute("
create view _view_computer_dist_stock_price_not_in_stock as
select * from _view_computer_dist_stock_price where (dist=2 and stock='N') or (dist=3 and stock=0) or (dist=4 and stock=0) order by cost
");
        $this->execute('drop view if exists _view_computer_dist_stock_price_ordered');
        $this->execute('
create view _view_computer_dist_stock_price_ordered as
select * from _view_computer_dist_stock_price where dist=1
union
select * from _view_computer_dist_stock_price_in_stock
union
select * from _view_computer_dist_stock_price_not_in_stock
');
        $this->execute('drop view if exists _view_computer_dist_stock_price_grouped');
        $this->execute('
create view _view_computer_dist_stock_price_grouped as
select * from _view_computer_dist_stock_price_ordered group by dealerId, computerId
');

        #--

        $this->execute('drop view if exists _view_peripheral_dist_stock_price');
        $this->execute('
create view _view_peripheral_dist_stock_price as
select 2 as dist, ingram_products.peripheralId, ingram_products.availability_flag as stock, ingram_prices.dealerId, ingram_prices.customer_price as cost
    from
      ingram_products
      join ingram_prices on ingram_products.ingram_part_number = ingram_prices.ingram_part_number
    where ingram_products.peripheralId is not null
union
select 3 as dist, synnex_products.peripheralId, synnex_products.Qty_on_Hand as stock, synnex_prices.dealerId, synnex_prices.Unit_Cost as cost
    from
      synnex_products
      join synnex_prices on synnex_products.SYNNEX_SKU = synnex_prices.SYNNEX_SKU
    where synnex_products.peripheralId is not null
union
select 4 as dist, techdata_products.peripheralId, techdata_products.Qty as stock, techdata_prices.dealerId, currency_exchange.rate * techdata_prices.CustBestPrice as cost
    from
      techdata_products
      join techdata_prices on techdata_products.Matnr = techdata_prices.Matnr
      join dealers on dealers.id = techdata_prices.dealerId
      join currency_exchange on currency_exchange.currency = dealers.currency
    where techdata_products.peripheralId is not null
');

        $this->execute('drop view if exists _view_peripheral_dist_stock_price_in_stock');
        $this->execute("
create view _view_peripheral_dist_stock_price_in_stock as
select * from _view_peripheral_dist_stock_price where (dist=2 and stock='Y') or (dist=3 and stock>0) or (dist=4 and stock>0) order by cost
");
        $this->execute('drop view if exists _view_peripheral_dist_stock_price_not_in_stock');
        $this->execute("
create view _view_peripheral_dist_stock_price_not_in_stock as
select * from _view_peripheral_dist_stock_price where (dist=2 and stock='N') or (dist=3 and stock=0) or (dist=4 and stock=0) order by cost
");
        $this->execute('drop view if exists _view_peripheral_dist_stock_price_ordered');
        $this->execute('
create view _view_peripheral_dist_stock_price_ordered as
select * from _view_peripheral_dist_stock_price where dist=1
union
select * from _view_peripheral_dist_stock_price_in_stock
union
select * from _view_peripheral_dist_stock_price_not_in_stock
');
        $this->execute('drop view if exists _view_peripheral_dist_stock_price_grouped');
        $this->execute('
create view _view_peripheral_dist_stock_price_grouped as
select * from _view_peripheral_dist_stock_price_ordered group by dealerId, peripheralId
');

        #--




    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}