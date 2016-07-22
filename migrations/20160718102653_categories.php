<?php

use Phinx\Migration\AbstractMigration;

class Categories extends AbstractMigration
{
    public function up()
    {
        try { $this->execute("ALTER TABLE base_product DROP FOREIGN KEY base_product_ibfk_3"); } catch(\Exception $ex) {}
        try { $this->execute("alter table base_product drop column categoryId"); } catch(\Exception $ex) {}
        try { $this->execute("ALTER TABLE base_category DROP FOREIGN KEY base_category_ibfk_1"); } catch(\Exception $ex) {}
        $this->execute('drop table if exists dealer_sku');
        $this->execute('drop table if exists base_sku');
        $this->execute('drop table if exists dealer_category_price_level');
        $this->execute('drop table if exists dealer_category');
        $this->execute('drop table if exists base_category');

        $this->execute('
create table base_category (
  id int not null AUTO_INCREMENT,
  `parent` int null default null,
  `name` varchar(255) not null,
  `properties` text null default null,
  primary key (id),
  key (`parent`),
  CONSTRAINT FOREIGN KEY (`parent`) REFERENCES `base_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
        ');

        $this->execute("
insert into base_category (id,`parent`,`name`) values
  (009,     null, 'Printers'),
  (009053,  009,    'Inkjet'),
  (009058,  009,    'Monochrome Laser'),
  (009059,  009,    'Color Laser'),

  (009088,  null, 'Printer Supplies'),
  (0090881, 009088, 'OEM Printer Supplies'),
  (0090882, 009088, 'Compatible Printer Supplies'),

  (002, null, 'Computers'),
  (002026, 002, 'Personal Computers'),
  (002662, 002, 'Tablets'),
  (002057, 002, 'Laptops'),
  (002317, 002, 'Workstations'),
  (002316, 002, 'Servers'),

  (005, null, 'Monitors'),
  (005038, 005, 'LCD Monitors'),
  (005093, 005, 'Projector'),
  (005501, 005, 'Plasma Displays'),
  (005510, 005, 'Touch Screens'),

  (010, null, 'Service'),
  (010023, 010, 'Computer Warranties'),
  (010087, 010, 'Printer Warranties'),

  (011, null, 'Software'),
  (011075, 011, 'Operating Systems'),
  (011025, 011, 'Creativity Application'),
  (011091, 011, 'Productivity Applications'),

  (012,   null, 'Storage'),
  (012046,012 , 'External Storage')
        ");

        $this->execute(
<<<SQL
            update base_category set properties='[
  {
    "name":"grade",
    "type":"select",
    "attributes":{
      "label":"Grade",
      "required":false,
      "allowEmpty":true,
      "multiOptions":{
        "Good":"Good",
        "Better":"Better",
        "Best":"Best"
      }
    }
  }
  ,
  {
    "name":"webcam",
    "type":"checkbox",
    "attributes":{
      "label":"Webcam"
    }
  }
  ,
  {
    "name":"mediaDrive",
    "type":"checkbox",
    "attributes":{
      "label":"CD/DVD Drive"
    }
  }
  ,
  {
    "name":"usb",
    "type":"select",
    "attributes":{
      "label":"USB Version",
      "required":false,
      "allowEmpty":true,
      "multiOptions":{
        "":"",
        "USB 1.x":"USB 1.x",
        "USB 2.0":"USB 2.0",
        "USB 3.0":"USB 3.0",
        "USB 3.1":"USB 3.1",
        "USB Type-C":"USB Type-C"
      }
    }
  }
  ,
  {
    "name":"os",
    "type":"select",
    "attributes":{
      "label":"OS",
      "required":false,
      "allowEmpty":true,
      "multiOptions":{
        "":"",
        "Windows 7":"Windows 7",
        "Windows 8":"Windows 8",
        "Windows 10":"Windows 10",
        "Mac OS X":"Mac OS X",
        "Linux":"Linux",
        "Windows Phone":"Windows Phone",
        "iOS":"iOS",
        "Android":"Android",
        "Windows Server 2008":"Windows Server 2008",
        "Windows Server 2012":"Windows Server 2012",
        "Windows Server 2016":"Windows Server 2016"
      }
    }
  }
  ,
  {
    "name":"ram",
    "type":"text_int",
    "attributes":{
      "label":"RAM (GB)",
      "required":false,
      "allowEmpty":true
    }
  }
  ,
  {
    "name":"hdd",
    "type":"text_int",
    "attributes":{
      "label":"Disk Size (GB)",
      "required":false,
      "allowEmpty":true
    }
  }
  ,
  {
    "name":"ssd",
    "type":"checkbox",
    "attributes":{
      "label":"SSD"
    }
  }
  ,
  {
    "name":"screenSize",
    "type":"text_float",
    "attributes":{
      "label":"Screen Size (Inch)",
      "required":false,
      "allowEmpty":true
    }
  }
  ,
  {
    "name":"hdDisplay",
    "type":"checkbox",
    "attributes":{
      "label":"HD Display"
    }
  }
  ,
  {
    "name":"displayType",
    "type":"select",
    "attributes":{
      "label":"Display Type",
      "required":false,
      "allowEmpty":true,
      "multiOptions":{
        "":"",
        "TFT-LCD":"TFT-LCD",
        "LED":"LED",
        "IPS":"IPS",
        "VA":"VA",
        "Plasma":"Plasma"
      }
    }
  }
  ,
  {
    "name":"processorName",
    "type":"text",
    "attributes":{
      "label":"Processor Name",
      "maxlength":"255",
      "required":false,
      "allowEmpty":true
    }
  }
  ,
  {
    "name":"processorSpeed",
    "type":"text_float",
    "attributes":{
      "label":"Processor Speed (Ghz)",
      "required":false,
      "allowEmpty":true
    }
  }
]
' where id=002
SQL
);

        $this->execute("ALTER TABLE base_category AUTO_INCREMENT=100000;");

        $this->execute('
create table dealer_category (
  `dealerId` int not null,
  `categoryId` int not null,
  `taxable` tinyint not null default 1,
  `name` varchar(255) null default null,
  `orderBy` int not null default 1,
  primary key (dealerId, categoryId),
  key (`dealerId`),
  key (`categoryId`),
  CONSTRAINT FOREIGN KEY (`dealerId`) REFERENCES `dealers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`categoryId`) REFERENCES `base_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
        ');

        $this->execute('
create table dealer_category_price_level (
  priceLevelId int not null,
  categoryId int not null,
  `margin` decimal(10,2) not null default 30,
  primary key (priceLevelId, categoryId),
  key (`priceLevelId`),
  key (`categoryId`),
  CONSTRAINT FOREIGN KEY (`priceLevelId`) REFERENCES `dealer_price_levels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`categoryId`) REFERENCES `base_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
        ');

        $this->execute('
create table base_sku (
  id int not null,
  `properties` text null default null,
  primary key (id),
  CONSTRAINT FOREIGN KEY (`id`) REFERENCES `base_product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
        ');

        $this->execute('
create table dealer_sku (
  skuId int not null,
  dealerId int not null,
  `dealerSku` varchar(255) null default null,
  `cost` decimal(10,2) null default null,
  `fixedPrice` decimal(10,2) null default null,
  `taxable` tinyint not null default 1,
  dataSheetUrl varchar(255) null,
  reviewsUrl varchar(255) null,
  online tinyint not null default 0,
  onlineDescription text null,
  webId varchar(255) null,
  primary key (skuId,dealerId),
  key (skuId),
  key (dealerId),
  CONSTRAINT FOREIGN KEY (`skuId`) REFERENCES `base_sku` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`dealerId`) REFERENCES `dealers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
        ');

        $this->execute('ALTER TABLE `ingram_products` ADD `skuId` INT NULL AFTER `masterDeviceId`, ADD INDEX (`skuId`)');
        $this->execute('ALTER TABLE `techdata_products` ADD `skuId` INT NULL AFTER `masterDeviceId`, ADD INDEX (`skuId`)');
        $this->execute('ALTER TABLE `synnex_products` ADD `skuId` INT NULL AFTER `masterDeviceId`, ADD INDEX (`skuId`)');

        $this->execute('CREATE TABLE `ingram_manufacturer` ( `name` VARCHAR(255) NOT NULL , `manufacturerId` INT NOT NULL , PRIMARY KEY (`name`), INDEX (`manufacturerId`)) ENGINE = InnoDB');
        $this->execute('CREATE TABLE `techdata_manufacturer` ( `name` VARCHAR(255) NOT NULL , `manufacturerId` INT NOT NULL , PRIMARY KEY (`name`), INDEX (`manufacturerId`)) ENGINE = InnoDB');
        $this->execute('CREATE TABLE `synnex_manufacturer` ( `name` VARCHAR(255) NOT NULL , `manufacturerId` INT NOT NULL , PRIMARY KEY (`name`), INDEX (`manufacturerId`)) ENGINE = InnoDB');

        $this->execute("insert into ingram_manufacturer (name, manufacturerId) VALUES
          ('LEXMARK%', 7),
          ('XEROX%', 10),
          ('EPSON%', 64),
          ('HP INC.%', 5),
          ('CANON%', 11),
          ('DELL%', 4),
          ('BROTHER%', 1),
          ('SAMSUNG%', 22),
          ('FUJITSU%', 72)
        ");

        $this->execute("insert into techdata_manufacturer (name, manufacturerId) VALUES
          ('Microsoft', 68),
          ('Lexmark', 7),
          ('Lexmark Accessories', 7),
          ('LEXMARK SDI', 7),
          ('XEROX LA', 10),
          ('XEROX ISCS SUPPLIES', 10),
          ('Epson', 64),
          ('Epson Latin America', 64),
          ('EPSON L-PRINTERS LATIN AMERICA', 64),
          ('HP %', 5),
          ('Canon', 11),
          ('DELL %', 4),
          ('Brother %', 1),
          ('Samsung %', 22),
          ('Fujitsu', 72)
        ");

        $this->execute("insert into synnex_manufacturer (name, manufacturerId) VALUES
          ('MICROSOFT SWL', 68),
          ('LEXMARK', 7),
          ('XEROX', 10),
          ('EPSON', 64),
          ('EPSON POS', 64),
          ('HEWLETT PACKARD ENTERPRIS', 5),
          ('HP INC.', 5),
          ('HP', 5),
          ('CANON', 11),
          ('TOSHIBA', 23),
          ('DELL', 4),
          ('DELL CANADA', 4),
          ('DELL CONSUMER', 4),
          ('BROTHER', 1),
          ('SAMSUNG', 22),
          ('FUJITSU', 72),
          ('TOSHIBA TEC', 23),
          ('TOSHIBA SECURITY', 23),
          ('PANASONIC', 26),
          ('KONICA COPIER/FAX SUPPLIE', 17),
          ('KONICA MINOLTA', 17),
          ('KYOCERA DOCUMENT SOLUTION', 18),
          ('RICOH', 15),
          ('OKI PRINTING SOLUTIONS', 21),
          ('CLOVER IMAGING GROUP', 63)
        ");

        $this->execute("
alter table base_product add column categoryId int null default null, add index(categoryId), add CONSTRAINT FOREIGN KEY (`categoryId`) REFERENCES `base_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ");

        $this->execute("update base_printer set tech='Laser' where tech is null");

        $this->execute("update base_product set categoryId=009053 where base_type='printer' and id in (select id from base_printer where tech='Ink')");
        $this->execute("update base_product set categoryId=009058 where base_type='printer' and id in (select id from base_printer where tech!='Ink' and tonerConfigId=1)");
        $this->execute("update base_product set categoryId=009059 where base_type='printer' and id in (select id from base_printer where tech!='Ink' and tonerConfigId!=1)");

        $this->execute("
update base_product set categoryId=0090881 where (base_type='printer_consumable' or base_type='printer_cartridge') and manufacturerId in (select manufacturerId from oem_manufacturers)
        ");
        $this->execute("
update base_product set categoryId=0090882 where (base_type='printer_consumable' or base_type='printer_cartridge') and manufacturerId not in (select manufacturerId from oem_manufacturers)
        ");


    }
}