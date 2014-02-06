<?php

/**
 * Class Healthcheck_Model_HealthcheckTest
 */
class Healthcheck_Model_HealthcheckTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Healthcheck_Model_Healthcheck
     */
    protected $_healthcheckModel;

    public function setUp ()
    {
        $this->_healthcheckModel = new Healthcheck_Model_Healthcheck();
        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_healthcheckModel = null;
    }

    public function testCanSetRmsUpload ()
    {
        /**
         * @var $rmsUpload Proposalgen_Model_Rms_Upload
         */
        $rmsUpload = $this->getMock('Proposalgen_Model_Rms_Upload');
        $this->_healthcheckModel->setRmsUpload($rmsUpload);
        $this->assertEquals($rmsUpload, $this->_healthcheckModel->getRmsUpload());
    }

    public function testCanSetClient ()
    {
        /**
         * @var $client Quotegen_Model_Client
         */
        $client = $this->getMock('Quotegen_Model_Client');
        $this->_healthcheckModel->setClient($client);
        $this->assertEquals($client, $this->_healthcheckModel->getClient());
    }

    public function testCanSetSettings ()
    {
        /**
         * @var $settings Healthcheck_Model_Healthcheck_Setting
         */
        $settings = $this->getMock('Healthcheck_Model_Healthcheck_Setting');
        $this->_healthcheckModel->setHealthcheckSettings($settings);
        $this->assertEquals($settings, $this->_healthcheckModel->getHealthcheckSettings());
    }
}