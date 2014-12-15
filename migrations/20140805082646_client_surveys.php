<?php

use Phinx\Migration\AbstractMigration;

class ClientSurveys extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->dropTable('assessment_surveys');

        $surveysTable = $this->table('surveys', array('id' => false, 'primary_key' => 'clientId'));
        $surveysTable
            ->addColumn('clientId', 'integer')
            ->addForeignKey(array('clientId'), 'clients', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addColumn('surveyed_at', 'datetime')
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->addColumn('name', 'string', array('null' => true))
            //@formatter:off
            ->addColumn('costOfInkAndToner',             'decimal', array('precision' => '18', 'scale' => '9', 'null'    => true))
            ->addColumn('costOfLabor',                   'decimal', array('precision' => '18', 'scale' => '9', 'null'    => true))
            ->addColumn('costToExecuteSuppliesOrder',    'decimal', array('precision' => '18', 'scale' => '9', 'default' => 50))
            ->addColumn('averageItHourlyRate',           'decimal', array('precision' => '18', 'scale' => '9', 'default' => 40))
            ->addColumn('numberOfSupplyOrdersPerMonth',  'decimal', array('precision' => '18', 'scale' => '9', 'null'    => true))
            ->addColumn('hoursSpentOnIt',                'decimal', array('precision' => '18', 'scale' => '9', 'null'    => true))
            ->addColumn('averageMonthlyBreakdowns',      'decimal', array('precision' => '18', 'scale' => '9', 'null'    => true))
            ->addColumn('pageCoverageMonochrome',        'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('pageCoverageColor',             'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('percentageOfInkjetPrintVolume', 'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('averageRepairTime',             'decimal', array('precision' => '18', 'scale' => '9'))
            //@formatter:on
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->dropTable('surveys');

        $this->execute('CREATE TABLE IF NOT EXISTS `assessment_surveys` (
    `reportId`                      INT    NOT NULL,
    `costOfInkAndToner`             DOUBLE NULL,
    `costOfLabor`                   DOUBLE NULL,
    `costToExecuteSuppliesOrder`    DOUBLE NOT NULL DEFAULT 50.00,
    `averageItHourlyRate`           DOUBLE NOT NULL DEFAULT 40.00,
    `numberOfSupplyOrdersPerMonth`  DOUBLE NOT NULL,
    `hoursSpentOnIt`                INT    NULL,
    `averageMonthlyBreakdowns`      DOUBLE NULL,
    `pageCoverageMonochrome`        DOUBLE NOT NULL,
    `pageCoverageColor`             DOUBLE NOT NULL,
    `percentageOfInkjetPrintVolume` DOUBLE NOT NULL,
    `averageRepairTime`             DOUBLE NOT NULL,
    PRIMARY KEY (`reportId`),
    CONSTRAINT `assessment_surveys_ibfk_1`
    FOREIGN KEY (`reportId`)
    REFERENCES `assessments` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');
    }
}