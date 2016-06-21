<?php

class My_DatabaseTestCase extends Zend_Test_PHPUnit_DatabaseTestCase {

    public $fixture_fk = [
        'dealer_features'=>['features'],
        'users'=>['dealers'],
        'clients'=>['addresses','dealers'],
        'addresses'=>['countries'],
        'rms_uploads'=>['users', 'rms_providers','dealers','dealer_rms_providers','rms_devices', 'rms_upload_rows' ],
        'device_instances'=>['rms_uploads','rms_devices'],
        'rms_devices'=>['manufacturers'],
        'base_printer'=>['base_product','base_printing_device','toner_configs','base_printer_cartridge','manufacturers'],
        'base_printer_cartridge'=>['base_product','base_printer_consumable','users','manufacturers','toner_configs','toner_colors'],
        'device_swap_reasons'=>['dealers','device_swap_reason_categories'],
        'ext_computer'=>['ext_hardware','dealers','ext_dealer_hardware'],
        'dealer_settings'=>['toner_vendor_ranking_sets','fleet_settings','quote_settings','generic_settings','optimization_settings','shop_settings'],
        'hardware_optimizations'=>['clients','rms_uploads'],
    ];

    public $fixtures = [];
    private $_fixturesLoaded = [];

    /** @var PHPUnit_Extensions_Database_DataSet_YamlDataSet */
    private $_fixtureDataset = null;
    private $_connectionMock;

    public function user2() {
        $user = \MPSToolbox\Legacy\Mappers\UserMapper::getInstance()->find(2);
        $user->currency = 'CAD';
        Zend_Auth::getInstance()->getStorage()->write($user);
    }

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

    private function loadFixture($name) {
        if (isset($this->_fixturesLoaded[$name])) return;

        if (isset($this->fixture_fk[$name])) {
            foreach ($this->fixture_fk[$name] as $fk) {
                if (!isset($this->_fixturesLoaded[$fk])) {
                    $this->loadFixture($fk);
                }
            }
        }
        $path = APPLICATION_BASE_PATH . '/tests/fixtures/' . $name . '.yml';
        if ($this->_fixtureDataset === null) {
            $this->_fixtureDataset = new PHPUnit_Extensions_Database_DataSet_YamlDataSet($path);
        } else {
            $this->_fixtureDataset->addYamlFile($path);
        }
        $this->_fixturesLoaded[$name] = true;
    }

    protected function getDataSet()
    {
        if (!isset($this->_fixtureDataset)) {
            $this->_fixtureDataset = null;
            $this->_fixturesLoaded = [];
            foreach ($this->fixtures as $name) {
                $this->loadFixture($name);
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
