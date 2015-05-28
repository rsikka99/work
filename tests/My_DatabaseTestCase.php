<?php

class My_DatabaseTestCase extends Zend_Test_PHPUnit_DatabaseTestCase {
    public $fixtures = [];
    private $_connectionMock;

    protected function getConnection()
    {
        if ($this->_connectionMock == null) {
            $connection = Zend_Db_Table::getDefaultAdapter();
            $this->_connectionMock = $this->createZendDbConnection(
                $connection, 'zfunittests'
            );
            Zend_Db_Table_Abstract::setDefaultAdapter($connection);
        }
        return $this->_connectionMock;
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
            if (!$this->_fixtureDataset) $this->_fixtureDataset = new PHPUnit_Extensions_Database_DataSet_CsvDataSet();
        }
        return $this->_fixtureDataset;
    }

    public function setUp()
    {
        parent::setUp();
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
