<?php

use Phinx\Migration\AbstractMigration;

class MultiRent extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('alter table dealers add rent_options text null default null');
        $this->execute('alter table dealers add plan_options text null default null');
        $this->execute('alter table devices add rent_values text null default null');
        $this->execute('alter table devices add plan_values text null default null');
        $this->execute('alter table devices add plan_page_values text null default null');
        $this->execute('alter table devices add tags varchar(255) null default null');

        $this->execute("update dealers set rent_options='36 Month, 48 Month, 60 Month' where id=14");
        $this->execute("update dealers set plan_options='Low, Med Low, Med, Med High, High' where id=14");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('alter table dealers drop rent_options, drop plan_options');
        $this->execute('alter table devices drop rent_values, drop plan_values, drop plan_page_values, drop tags');
    }
}