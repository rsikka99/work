<?php

if (!class_exists('TestRmsUploadService')) {
    class TestRmsUploadService extends \MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload\AbstractRmsUploadService {
        public function mapColumn($lowerCaseHeader) { parent::mapColumn($lowerCaseHeader); }
        public function validateHeaders ($csvHeaders) { return parent::validateHeaders($csvHeaders); }
    }
}

/**
 * Class MPSToolbox_Legacy_Modules_ProposalGenerator_Services_RmsUpload_AbstractRmsUploadServiceTest
 * @property TestRmsUploadService $service
 */
class MPSToolbox_Legacy_Modules_ProposalGenerator_Services_RmsUpload_AbstractRmsUploadServiceTest extends My_DatabaseTestCase {

    public function setUp() {
        $this->service = new TestRmsUploadService();
    }

    public function test_mapColumn() {
        $this->service->mapColumn('abc');
        $arr = $this->service->getMappedHeaders();
        $this->assertEquals(['abc'], $arr);
        $arr = $this->service->getFieldsPresent();
        $this->assertEquals([], $arr);

        $this->service->setColumnMapping(['XyZ'=>'OPq']);
        $this->service->mapColumn('XyZ');
        $arr = $this->service->getMappedHeaders();
        $this->assertEquals(['abc','OPq'], $arr);
        $arr = $this->service->getFieldsPresent();
        $this->assertEquals(['OPq'], $arr);
    }

    public function test_validateHeaders() {
        $this->service->setRequiredHeaders(['rmsModelId'=>false,'assetId'=>true]);
        $this->service->setColumnMapping([
            'Aaa'             => 'rmsModelId',
            'Bbb'             => 'assetId',
        ]);
        $result = $this->service->validateHeaders([]);
        $this->assertEquals('File is missing required these headers: [Bbb]. Are you sure you selected the right RMS Vendor?', $result);

        $result = $this->service->validateHeaders(['Aaa','assetId']);
        $this->assertEquals(true, $result);
    }

    public function test_processCsvFile() {
        $result = $this->service->processCsvFile('non-existing-file.csv');
        $this->assertEquals(true, $result);

        $this->service->setColumnMapping([
            'a'             => 'rmsVendorName',
            'b'             => 'modelName',
            'c'             => 'manufacturer',
            'd'             => 'monitorStartDate',
            'e'             => 'monitorEndDate',
            'f'             => 'startMeterBlack',
            'g'             => 'endMeterBlack',
            'h'             => 'startMeterColor',
            'i'             => 'endMeterColor',
            'j'             => 'isColor',
        ]);

        $tmp=array_search('uri', @array_flip(stream_get_meta_data($fp=tmpfile())));
        file_put_contents($tmp,
"b,c,d,e,f,g,h,i,j\n1,2,1/1/2010,1/2/2010,100,1000,200,2000,TRUE
1,2,1/1/2010,1/2/2010,100,1000,,,FALSE
1,2,1/1/2010,1/2/2010,100,1000,200,2000,
1,2,1/1/2010,1/2/2010,100,1000,,,
1,2,1/1/2010,1/2/2010,100,1000,200,2000,FALSE
");
        $this->service->setIncomingDateFormat('d/m/Y');
        $result = $this->service->processCsvFile($tmp);
        $this->assertEquals(true, $result);
        $this->assertEquals(4,count($this->service->validCsvLines));
        $this->assertEquals(1,count($this->service->invalidCsvLines));
    }

    public function test_processCsvFile_validate() {
        $this->service->setColumnMapping([
            'a'             => 'rmsVendorName',
            'b'             => 'modelName',
            'c'             => 'manufacturer',
            'd'             => 'monitorStartDate',
            'e'             => 'monitorEndDate',
            'f'             => 'startMeterBlack',
            'g'             => 'endMeterBlack',
            'h'             => 'startMeterColor',
            'i'             => 'endMeterColor',
            'j'             => 'isColor',
        ]);

        $tmp=array_search('uri', @array_flip(stream_get_meta_data($fp=tmpfile())));
        file_put_contents($tmp, "b,c,d,e,f,g,h,i,j\n1,2,1/1/2010,1/2/2010,100,1000,200,2000,FALSE\n");
        $this->service->setIncomingDateFormat('d/m/Y');
        $result = $this->service->processCsvFile($tmp);
        $this->assertEquals(true, $result);
        $this->assertEquals(0,count($this->service->validCsvLines));
        $this->assertEquals(1,count($this->service->invalidCsvLines));
        $line = current($this->service->invalidCsvLines);
        $this->assertEquals('Color meter values must be zero for Monochrome devices',$line->validationErrorMessage);
    }



}