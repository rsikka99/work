<?php
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\QuoteForm;

/**
 * Class Quotegen_Form_QuoteTest
 */
class Quotegen_Form_QuoteTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    public function setUp()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->query('replace into dealers set id=10001');
        $db->query('replace into clients set id=10001, dealerid=10001');
        parent::setUp();
    }

    public function tearDown()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->query('delete from clients where id=10001');
        $db->query('delete from dealers where id=10001');
        parent::tearDown();
    }


    /**
     * @return QuoteForm
     */
    public function getForm ()
    {
        return new QuoteForm();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_QuoteTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_QuoteTest.xml");
    }

}