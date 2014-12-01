<?php

/**
 * Class Assessment_Form_Assessment_SurveyTest
 */
class Assessment_Form_Assessment_SurveyTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * Gets the form to be used in testing
     *
     * @return Assessment_Form_Assessment_Survey|Zend_Form
     */
    public function getForm ()
    {
        return new Assessment_Form_Assessment_Survey();
    }

    /**
     * @return array|mixed
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_SurveyTest.xml");
    }


    /**
     * @return array|mixed
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_SurveyTest.xml");
    }

}