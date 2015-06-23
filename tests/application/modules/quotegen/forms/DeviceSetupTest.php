<?php
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\DeviceSetupForm;

/**
 * Class Quotegen_Form_DeviceSetupTest
 */
class Quotegen_Form_DeviceSetupTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    public function setUp()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->query('replace into manufacturers set id=1');
        $db->query('replace into toner_configs set id=1');
        parent::setUp();
    }

    public function tearDown()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->query('delete from manufacturers where id=1');
        $db->query('delete from toner_configs where id=1');
        parent::tearDown();
    }


    /**
     * @return DeviceSetupForm
     */
    public function getForm ()
    {
        return new DeviceSetupForm();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_DeviceSetupTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_DeviceSetupTest.xml");
    }
}