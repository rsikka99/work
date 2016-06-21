<?php

class DistributorUpdateServiceTest extends My_DatabaseTestCase {

    public $fixtures = [
        'base_printer_cartridge',
        'base_printer',
        'devices',
        'dealers',
        'suppliers',
        'dealer_suppliers',
    ];

    public function test_getDealerSuppliers() {
        $service = new \MPSToolbox\Services\DistributorUpdateService();
        $result = $service->getDealerSuppliers();
        $this->assertEquals(1, count($result));
    }

    public function test_updatePrices() {
        $service = new \MPSToolbox\Services\DistributorUpdateService();

        $ftpClient = $this->getMock('\Tangent\Ftp\NcFtp');
        $service->setFtpClient($ftpClient);

        $zip = $this->getMock('\Zend_Filter_Compress_Zip');
        $service->setZipAdapter($zip);

        file_put_contents(APPLICATION_BASE_PATH . '/data/cache/PRICE.TXT',
'"A","64822L      ","3719","BROTHER - SUPPLIES                 ","BLACK TONER CARTRIDGE FOR      ","HL3040CN HL3070CW MFC9010CN        ",0000000000000108.99,"TN210BK             ",000001.70,"0012502622567",0014.00,0005.00,0007.00," ",0000000000000071.83,"O","Y"," ","L-SUPL","TONR","1010","Y","N"
"A","35816T      ","0187","HP INC. - OFFICEJET PRO X          ","ML OFFICEJET PRO X476DN MFP CLR","INKJET P/S/C/F SB ADF USB PRINTER  ",0000000000000776.38,"Q5400A          ",000062.30,"0887111491893",0023.50,0019.80,0026.00," ",0000000000000584.50,"O","Y"," ","INKMFP","PRNT","0733","Y","N"'
        );

        $arr = $service->getDealerSuppliers();
        $service->updatePrices($arr[0]);
    }

}