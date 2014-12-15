<?php
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\QuoteForm;

/**
 * Class Quotegen_Form_QuoteTest
 */
class Quotegen_Form_QuoteTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
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