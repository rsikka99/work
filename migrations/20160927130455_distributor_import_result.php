<?php

use Phinx\Migration\AbstractMigration;

class DistributorImportResult extends AbstractMigration
{
    public function up()
    {
        $this->execute('
create table distributor_import_result (
  `id` int not null AUTO_INCREMENT,
  `dealerId` int not null,
  `supplierId` int not null,
  `dt` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `products_added` int not null,
  `products_deleted` int not null,
  `products_updated` int not null,
  `products_total` int not null,
  `prices_added` int not null,
  `prices_deleted` int not null,
  `prices_updated` int not null,
  `prices_total` int not null,
  primary key (`id`)
) ENGINE=InnoDB
');
    }

    public function down()
    {

    }
}