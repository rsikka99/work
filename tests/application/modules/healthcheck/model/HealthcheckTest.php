<?php
use MPSToolbox\Legacy\Modules\HealthCheck\Models\HealthCheckModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ClientModel;

/**
 * Class Healthcheck_Model_HealthcheckTest
 */
class Healthcheck_Model_HealthcheckTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var HealthCheckModel
     */
    protected $_healthcheckModel;

    public function setUp ()
    {
        $this->_healthcheckModel = new HealthCheckModel();
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
         * @var $rmsUpload RmsUploadModel
         */
        $rmsUpload = $this->getMock('MPSToolbox\Legacy\Modules\ProposalGenerator\Models\Proposalgen_Model_Rms_Upload');
        $this->_healthcheckModel->setRmsUpload($rmsUpload);
        $this->assertEquals($rmsUpload, $this->_healthcheckModel->getRmsUpload());
    }

    public function testCanSetClient ()
    {
        /**
         * @var $client ClientModel
         */
        $client = $this->getMock('MPSToolbox\Legacy\Modules\QuoteGenerator\Models\Quotegen_Model_Client');
        $this->_healthcheckModel->setClient($client);
        $this->assertEquals($client, $this->_healthcheckModel->getClient());
    }
}