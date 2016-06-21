<?php

use Phinx\Migration\AbstractMigration;

class BaseProductFix2 extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("drop view toners");
        $this->execute(
"create view toners as
    select
        base_product.`id`,`sku`,`cost`,`pageYield` as `yield`,`manufacturerId`,`colorId` as `tonerColorId`,`userId`,`isSystemProduct` as `isSystemDevice`,`imageFile`,`imageUrl`,`name`,`weight`,`UPC`
    from
        base_product
            join base_printer_consumable on base_printer_consumable.id=base_product.id
            join base_printer_cartridge on base_printer_cartridge.id=base_printer_consumable.id
    where
        quantity=1
        and `type` in ('Inkjet Cartridge','Laser Cartridge','Print Head','Monochrome Toner','Color Toner')
");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}