<?php
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUploadService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsProviderModel;

class Zend_Form_Element_FileMock extends Zend_Form_Element_File {
    public $filename = 'unit_test.csv';
    public function isUploaded() {
        return true;
    }
    public function receive() {
        return true;
    }
    public function isValid($value, $context = null) {
        return true;
    }
    public function getFileName($value=null, $path=true) {
        return $this->filename;
    }
}

/**
 * Class MPSToolbox_Legacy_Modules_ProposalGenerator_Services_RmsUploadServiceTest
 * @property RmsUploadService service
 */
class MPSToolbox_Legacy_Modules_ProposalGenerator_Services_RmsUploadServiceTest extends PHPUnit_Framework_TestCase
{
    public function setUp() {
        $userId = 2;
        $dealerId = 2;
        $clientId = 7;
        $rmsUpload = null;
        $this->service = new RmsUploadService(
            $userId, $dealerId, $clientId, $rmsUpload
        );
    }
    public function tearDown() {

    }

    public function processUpload_files() {
        return [
            [RmsProviderModel::RMS_PROVIDER_FMAUDIT, 'FMAudit/FMAudExport-partial.csv'],

            [RmsProviderModel::RMS_PROVIDER_PRINT_AUDIT, 'Print Audit/Flying Disc Manufacturing.csv'],
            [RmsProviderModel::RMS_PROVIDER_PRINT_AUDIT, 'Print Audit/Oceanview Landscape Companies.csv'],
            [RmsProviderModel::RMS_PROVIDER_PRINT_AUDIT, 'Print Audit/Ottawa Valley Water District.csv'],
            [RmsProviderModel::RMS_PROVIDER_PRINT_AUDIT, 'Print Audit/Sonic Avionics.csv'],

            [RmsProviderModel::RMS_PROVIDER_PRINTFLEET_TWO, 'PrintFleet/OD  Custom Device Report Sample.csv'],
            [RmsProviderModel::RMS_PROVIDER_PRINTFLEET_TWO, 'PrintFleet 2/OD - Covan (94 Rows).csv'],
            [RmsProviderModel::RMS_PROVIDER_PRINTFLEET_TWO, 'PrintFleet 2/OD - One Three Television (14 Rows).csv'],

            [RmsProviderModel::RMS_PROVIDER_PRINTFLEET_THREE, 'PrintFleet 3/2014_04_15 - Custom Report 2.csv'],
            [RmsProviderModel::RMS_PROVIDER_PRINTFLEET_THREE, 'PrintFleet 3/small - Custom Report 2.csv'],

            [RmsProviderModel::RMS_PROVIDER_XEROX, 'Xerox/Sample2.csv'],
            [RmsProviderModel::RMS_PROVIDER_XEROX, 'Xerox/XOPA_Export-92fbe.csv'],

            [RmsProviderModel::RMS_PROVIDER_LEXMARK, 'Lexmark/denver.csv'],
            [RmsProviderModel::RMS_PROVIDER_LEXMARK, 'Lexmark/pittsburgh.csv'],
            [RmsProviderModel::RMS_PROVIDER_LEXMARK, 'Lexmark/seattle.csv'],
            [RmsProviderModel::RMS_PROVIDER_LEXMARK, 'Lexmark/philadelphia.csv'],
            [RmsProviderModel::RMS_PROVIDER_LEXMARK, 'Lexmark/dallas.csv'],

            [RmsProviderModel::RMS_PROVIDER_LEXMARK, 'Lexmark/corporate.csv'],
        ];
    }

    /**
     * @dataProvider processUpload_files
     */
    public function test_processUpload($rmsProviderId, $filename) {
        $form = $this->service->getForm();
        $elements = $form->getElements();
        $elements['file'] = new Zend_Form_Element_FileMock('uploadFile');
        $form->uploadFile = $elements['file'];
        $form->uploadFile->filename = APPLICATION_BASE_PATH.'/docs/Sample Import Files/'.$filename;

        $data=[
            'rmsProviderId'=>$rmsProviderId, // <<< lexmark
            'uploadFile'=>$form->uploadFile->filename,
            'performUpload'=>true,
            'goBack'=>null,
        ];
        $dealerId = 2;
        $result = $this->service->processUpload($data, $dealerId);
        if (!$result) print_r($this->service->errorMessages);
        $this->assertTrue($result);
    }

}