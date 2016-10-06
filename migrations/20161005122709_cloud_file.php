<?php

use Phinx\Migration\AbstractMigration;

class CloudFile extends AbstractMigration
{
    public function up()
    {
        $this->query("
create table cloud_file (
  `id` int not null AUTO_INCREMENT,
  `type` enum('image','video','file') default null,
  `orderBy` int not null default 1,
  `baseProductId` int not null,
  `handle` varchar(255) not null,
  `format` varchar(255) not null,
  `url` varchar(255) not null,
  `size` int not null default 0,
  `width` int null,
  `height` int null,
  `localFile` varchar(255) null,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  primary key (id),
  key (`baseProductId`),
  CONSTRAINT FOREIGN KEY (`baseProductId`) REFERENCES `base_product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
");
    }

    public function down()
    {

    }
}