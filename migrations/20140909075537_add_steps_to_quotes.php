<?php

use Phinx\Migration\AbstractMigration;

class AddStepsToQuotes extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $quotesTable = $this->table('quotes');
        $quotesTable
            ->addColumn('stepName', 'string')
            ->save();

        $this->execute("UPDATE quotes SET stepName='add_hardware' WHERE stepName IS NULL");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $quotesTable = $this->table('quotes');
        $quotesTable
            ->removeColumn('stepName', 'string')
            ->save();
    }
}