<?php

use Phinx\Migration\AbstractMigration;

class AddCustomerPricingFeature extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("INSERT INTO features (id, name) VALUES ('client_pricing', 'Client Pricing Uploads')");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("DELETE FROM features where id='client_pricing'");
    }
}