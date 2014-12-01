<?php

/**
 * Class Memjetoptimization_Form_SettingTest
 */
class Memjetoptimization_Form_SettingTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    /**
     * @return Memjetoptimization_Form_Setting|Zend_Form
     */
    public function getForm ()
    {
        return new Memjetoptimization_Form_Setting();
    }

    /**
     * @return array|mixed
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_memjetFormTest.xml");

    }

    /**
     * @return array|mixed
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_memjetFormTest.xml");

    }
}