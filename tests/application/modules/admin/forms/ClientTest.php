<?php

/**
 * Class Admin_Form_ClientTest
 */
class Admin_Form_ClientTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    /**
     * Builds the form to be used for testing
     *
     * @param bool $dealerManagement
     * @param bool $isAdmin
     *
     * @return \Admin_Form_Client
     */
    public function getForm ($dealerManagement = true, $isAdmin = false)
    {
        $view = $this->getMock('Zend_View', array('IsAllowed'));

        $view->expects($this->any())
             ->method('IsAllowed')
             ->will($this->returnValue($isAdmin));

        return new Admin_Form_Client($dealerManagement, array('view' => $view));
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_clientFormTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_clientFormTest.xml");
    }

    public function testDealerFieldExists ()
    {
        $this->assertInstanceOf('Zend_Form_Element_Select', $this->getForm(false, true)->getElement('dealerId'));
    }

    public function testDealerFieldDoesNotExist ()
    {
        $this->assertNotInstanceOf('Zend_Form_Element_Select', $this->getForm()->getElement('dealerId'));
    }
}