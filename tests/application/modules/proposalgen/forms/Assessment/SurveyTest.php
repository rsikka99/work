<?php

/**
 * Class Proposalgen_Form_Assessment_SurveyTestTest
 */
class Proposalgen_Form_Assessment_SurveyTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    /**
     * @return Proposalgen_Form_Assessment_Survey
     */
    public function getForm ()
    {
        return new Proposalgen_Form_Assessment_Survey();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_SurveyTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_SurveyTest.xml");
    }

}