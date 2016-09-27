<?php

use Phinx\Migration\AbstractMigration;

class PrintfleetApiKey extends AbstractMigration
{
    public function up()
    {
        $this->execute('create table printfleet_api_key ( `host` varchar(255) not null, `key` text not null, primary key (`host`)) ENGINE=InnoDB');
        $this->execute("insert into printfleet_api_key set `host`='pagetrac.com', `key`='eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJvZmYiOmZhbHNlLCJpc3MiOm51bGwsInN1YiI6IlJlbnQgdGhlIFByaW50ZXIiLCJhdWQiOiI1NGZlOTczOC05ZWJmLTRmNDAtOTdjYi00NzcxZTMwNWE1MjUiLCJpYXQiOjE0Njg1MDk4MTIsIm5iZiI6bnVsbCwiZXhwIjpudWxsfQ.L2NIa2PKmExuU9zuJzPYq82T2zDekgRdCCjtayGv5ydcSo-s-DBE3uMZYnpRV6FWWBmDG7DlCKg2-bYnPK-plA'");
    }
    public function down()
    {

    }
}