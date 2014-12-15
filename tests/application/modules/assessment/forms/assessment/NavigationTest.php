<?php
use MPSToolbox\Legacy\Modules\Assessment\Forms\AssessmentNavigationForm;

/**
 * Class Assessment_Form_Assessment_NavigationTest
 */
class Assessment_Form_Assessment_NavigationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AssessmentNavigationForm
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new AssessmentNavigationForm();
        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_form = null;
    }

    // Why are we testing this?
    public function testFormLoadsAllButtons ()
    {
        $this->assertInstanceOf('Zend_Form_Element_Button', $this->_form->getElement('goBack'));
        $this->assertInstanceOf('Zend_Form_Element_Button', $this->_form->getElement('save'));
        $this->assertInstanceOf('Zend_Form_Element_Button', $this->_form->getElement('saveAndContinue'));
    }
}