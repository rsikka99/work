<?php
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\PageCountsModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceMeterModel;


/**
 * @property PageCountsModel blackModel
 * @property PageCountsModel colorModel
 */
class MPSToolbox_Legacy_Modules_ProposalGenerator_Models_PageCountsModelTest extends PHPUnit_Framework_TestCase
{
    public function setUp() {
        $this->blackModel = new PageCountsModel();

        $meter = new DeviceInstanceMeterModel([
            "startMeterBlack"        => 30000,
            "endMeterBlack"          => 60000,
            "startMeterColor"        => 0,
            "endMeterColor"          => 0,
            "startMeterPrintBlack"   => 10000,
            "endMeterPrintBlack"     => 20000,
            "startMeterPrintColor"   => 0,
            "endMeterPrintColor"     => 0,
            "startMeterCopyBlack"    => 10000,
            "endMeterCopyBlack"      => 20000,
            "startMeterCopyColor"    => 0,
            "endMeterCopyColor"      => 0,
            "startMeterFax"          => 0,
            "endMeterFax"            => 0,
            "startMeterScan"         => 0,
            "endMeterScan"           => 0,
            "startMeterPrintA3Black" => 10000,
            "endMeterPrintA3Black"   => 20000,
            "startMeterPrintA3Color" => 0,
            "endMeterPrintA3Color"   => 0,
            "startMeterLife"         => 0,
            "endMeterLife"           => 0,
            "monitorStartDate"       => '2015-01-01',
            "monitorEndDate"         => '2015-02-01',
        ]);

        $this->blackModel->getBlackPageCount()->add($meter->getBlackPageCount());
        $this->blackModel->getColorPageCount()->add($meter->getColorPageCount());
        $this->blackModel->getCopyBlackPageCount()->add($meter->getCopyBlackPageCount());
        $this->blackModel->getCopyColorPageCount()->add($meter->getCopyColorPageCount());
        $this->blackModel->getFaxPageCount()->add($meter->getFaxPageCount());
        $this->blackModel->getPrintA3BlackPageCount()->add($meter->getPrintA3BlackPageCount());
        $this->blackModel->getPrintA3ColorPageCount()->add($meter->getPrintA3ColorPageCount());
        $this->blackModel->getScanPageCount()->add($meter->getScanPageCount());
        $this->blackModel->getLifePageCount()->add($meter->getLifePageCount());

        $this->colorModel = new PageCountsModel();

        $meter = new DeviceInstanceMeterModel([
            "startMeterBlack"        => 0,
            "endMeterBlack"          => 0,
            "startMeterColor"        => 0,
            "endMeterColor"          => 0,
            "startMeterPrintBlack"   => 0,
            "endMeterPrintBlack"     => 0,
            "startMeterPrintColor"   => 0,
            "endMeterPrintColor"     => 0,
            "startMeterCopyBlack"    => 0,
            "endMeterCopyBlack"      => 0,
            "startMeterCopyColor"    => 0,
            "endMeterCopyColor"      => 0,
            "startMeterFax"          => 0,
            "endMeterFax"            => 0,
            "startMeterScan"         => 0,
            "endMeterScan"           => 0,
            "startMeterPrintA3Black" => 0,
            "endMeterPrintA3Black"   => 0,
            "startMeterPrintA3Color" => 0,
            "endMeterPrintA3Color"   => 0,
            "startMeterLife"         => 0,
            "endMeterLife"           => 0,
            "monitorStartDate"       => '2015-01-01',
            "monitorEndDate"         => '2015-02-01',
        ]);

        $this->colorModel->getBlackPageCount()->add($meter->getBlackPageCount());
        $this->colorModel->getColorPageCount()->add($meter->getColorPageCount());
        $this->colorModel->getCopyBlackPageCount()->add($meter->getCopyBlackPageCount());
        $this->colorModel->getCopyColorPageCount()->add($meter->getCopyColorPageCount());
        $this->colorModel->getFaxPageCount()->add($meter->getFaxPageCount());
        $this->colorModel->getPrintA3BlackPageCount()->add($meter->getPrintA3BlackPageCount());
        $this->colorModel->getPrintA3ColorPageCount()->add($meter->getPrintA3ColorPageCount());
        $this->colorModel->getScanPageCount()->add($meter->getScanPageCount());
        $this->colorModel->getLifePageCount()->add($meter->getLifePageCount());

    }
    public function tearDown() {

    }

    public function test_processUpload() {
        $before = $this->blackModel->getCombinedPageCount()->getMonthly();
        $this->blackModel->processPageRatio(75);
        $after = $this->blackModel->getCombinedPageCount()->getMonthly();
        $this->assertEquals($before,$after);
    }

}