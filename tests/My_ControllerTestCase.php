<?php
abstract class My_ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{

    public $fixtures = ['dealers','users','clients'];

    public function appBootstrap()
    {
        $this->_application = $GLOBALS['application'];
        $this->_application->bootstrap();
    }

    public function setUp()
    {
        $this->bootstrap = array($this, 'appBootstrap');

        parent::setUp();
        $this->_application->getBootstrap()->getPluginResource('frontcontroller')->init();
        $this->_application->getBootstrap()->getPluginResource('view')->init();
        Zend_Layout::startMvc(['layoutPath'=>APPLICATION_PATH.'/layouts/scripts']);
        include APPLICATION_PATH . '/configs/routes.php';

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $connection = new PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection($db->getConnection());

        $dataSet = $this->getDataSet();

        if ($dataSet instanceof PHPUnit_Extensions_Database_DataSet_IDataSet) {
            $setupOperation = PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT();
            $setupOperation->execute($connection, $dataSet);
        }
    }

    protected function getDataSet()
    {
        if (!isset($this->_fixtureDataset)) {
            $this->_fixtureDataset = null;
            foreach ($this->fixtures as $name) {
                $path = APPLICATION_BASE_PATH . '/tests/fixtures/' . $name . '.yml';
                if ($this->_fixtureDataset === null) {
                    $this->_fixtureDataset = new PHPUnit_Extensions_Database_DataSet_YamlDataSet($path);
                } else {
                    $this->_fixtureDataset->addYamlFile($path);
                }
            }
        }
        return $this->_fixtureDataset;
    }

    public function setup_fixtures(array $fixtures) {
        $this->fixtures = $fixtures;
        unset($this->_fixtureDataset);
        $this->getDatabaseTester()->setDataSet($this->getDataSet());
        $this->getDatabaseTester()->onSetUp();
    }

    public function tearDown() {
        /**
         * @var PHPUnit_Extensions_Database_DataSet_ITable $tableName
         */
        $db = Zend_Db_Table::getDefaultAdapter();
        $dataSet=$this->getDataSet();
        if ($dataSet) foreach ($dataSet->getReverseIterator() as $tableName) {
            $db->query('TRUNCATE '.$tableName->getTableMetaData()->getTableName());
        }
    }
}