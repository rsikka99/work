<?php

class Proposalgen_Model_DeviceInstanceMeterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Proposalgen_Model_DeviceInstanceMeter
     */
    protected $_deviceInstanceMeter;

    /**
     * @param bool $forceNewObject
     *
     * @return Proposalgen_Model_DeviceInstanceMeter
     */
    public function getDeviceInstanceMeter ($forceNewObject = false)
    {
        if (!isset($this->_deviceInstanceMeter) || $forceNewObject)
        {
            $this->_deviceInstanceMeter = new Proposalgen_Model_DeviceInstanceMeter();
        }

        return $this->_deviceInstanceMeter;
    }

    /**
     * This function loads an XML file of good data into arrays to be tested in the model
     */
    public function goodAverageDailyPageCountData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_deviceInstanceMeterTest.xml");
        $data = array();
        foreach ($xml->deviceInstanceMeter as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     *
     * @dataProvider goodAverageDailyPageCountData
     */
    public function testCalculateAverageDailyPageCount ($data)
    {
        $data                = (array)$data;
        $deviceInstanceMeter = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->populate($data);
        $this->assertTrue(bccomp($deviceInstanceMeter->calculateAverageDailyPageCount($data['startMeter'], $data['endMeter']), $data['expectedPageCount']) === 0);
    }

    public function testCalculateMpsMonitorInterval ()
    {
        $deviceInstanceMeter                   = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate   = "2016-12-04 01:25:00";
        $dateInterval                          = $deviceInstanceMeter->calculateMpsMonitorInterval();

        $this->assertTrue($dateInterval->y === 3);
        $this->assertTrue($dateInterval->m === 2);
        $this->assertTrue($dateInterval->d === 1);
        $this->assertTrue($dateInterval->days === 1158);
        $this->assertTrue($dateInterval->h === 1);
        $this->assertTrue($dateInterval->i === 25);
    }

    public function testHasPrintedInLife ()
    {
        $deviceInstanceMeter                   = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate   = "2013-12-03 00:00:00";
        $deviceInstanceMeter->endMeterLife     = 200;

        $this->assertTrue($deviceInstanceMeter->hasPrintedInLife());
    }

    public function testHasPrintedInTimeSpan ()
    {
        $deviceInstanceMeter                   = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate   = "2013-12-03 00:00:00";
        $deviceInstanceMeter->startMeterLife   = 200;
        $deviceInstanceMeter->endMeterLife     = 500;

        $this->assertTrue($deviceInstanceMeter->hasPrintedInTimeSpan());
    }

    public function testBlackPageCount ()
    {
        $deviceInstanceMeter                   = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate   = "2013-10-04 00:00:00";
        $deviceInstanceMeter->startMeterBlack  = 1000;
        $deviceInstanceMeter->endMeterBlack    = 50000;

        $this->assertTrue(bccomp($deviceInstanceMeter->getBlackPageCount()->getDaily(), 49000) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getBlackPageCount()->getMonthly(), 1489600) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getBlackPageCount()->getYearly(), 17896858) === 0);
    }

    public function testColorPageCount ()
    {
        $deviceInstanceMeter                   = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate   = "2013-10-04 00:00:00";
        $deviceInstanceMeter->startMeterColor  = 1000;
        $deviceInstanceMeter->endMeterColor    = 50000;

        $this->assertTrue(bccomp($deviceInstanceMeter->getColorPageCount()->getDaily(), 49000) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getColorPageCount()->getMonthly(), 1489600) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getColorPageCount()->getYearly(), 17896858) === 0);
    }

    public function testCombinedPageCount ()
    {
        $deviceInstanceMeter                   = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate   = "2013-10-04 00:00:00";
        $deviceInstanceMeter->startMeterColor  = 0;
        $deviceInstanceMeter->endMeterColor    = 24000;
        $deviceInstanceMeter->startMeterBlack  = 0;
        $deviceInstanceMeter->endMeterBlack    = 25000;

        $this->assertTrue(bccomp($deviceInstanceMeter->getCombinedPageCount()->getDaily(), 49000) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getCombinedPageCount()->getMonthly(), 1489600) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getCombinedPageCount()->getYearly(), 17896858) === 0);
    }

    public function testPrintBlackPageCount ()
    {
        $deviceInstanceMeter                       = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate     = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate       = "2013-10-04 00:00:00";
        $deviceInstanceMeter->startMeterPrintBlack = 1000;
        $deviceInstanceMeter->endMeterPrintBlack   = 50000;

        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintBlackPageCount()->getDaily(), 49000) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintBlackPageCount()->getMonthly(), 1489600) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintBlackPageCount()->getYearly(), 17896858) === 0);
    }

    public function testColorPrintPageCount ()
    {
        $deviceInstanceMeter                       = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate     = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate       = "2013-10-04 00:00:00";
        $deviceInstanceMeter->startMeterPrintColor = 1000;
        $deviceInstanceMeter->endMeterPrintColor   = 50000;

        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintColorPageCount()->getDaily(), 49000) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintColorPageCount()->getMonthly(), 1489600) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintColorPageCount()->getYearly(), 17896858) === 0);
    }

    public function testPrintCombinedPageCount ()
    {
        $deviceInstanceMeter                       = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate     = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate       = "2013-10-04 00:00:00";
        $deviceInstanceMeter->startMeterPrintColor = 0;
        $deviceInstanceMeter->endMeterPrintColor   = 24000;
        $deviceInstanceMeter->startMeterPrintBlack = 0;
        $deviceInstanceMeter->endMeterPrintBlack   = 25000;

        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintCombinedPageCount()->getDaily(), 49000) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintCombinedPageCount()->getMonthly(), 1489600) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintCombinedPageCount()->getYearly(), 17896858) === 0);
    }

    public function testCopyBlackPageCount ()
    {
        $deviceInstanceMeter                      = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate    = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate      = "2013-10-04 00:00:00";
        $deviceInstanceMeter->startMeterCopyBlack = 1000;
        $deviceInstanceMeter->endMeterCopyBlack   = 50000;

        $this->assertTrue(bccomp($deviceInstanceMeter->getCopyBlackPageCount()->getDaily(), 49000) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getCopyBlackPageCount()->getMonthly(), 1489600) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getCopyBlackPageCount()->getYearly(), 17896858) === 0);
    }

    public function testColorCopyPageCount ()
    {
        $deviceInstanceMeter                      = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate    = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate      = "2013-10-04 00:00:00";
        $deviceInstanceMeter->startMeterCopyColor = 1000;
        $deviceInstanceMeter->endMeterCopyColor   = 50000;

        $this->assertTrue(bccomp($deviceInstanceMeter->getCopyColorPageCount()->getDaily(), 49000) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getCopyColorPageCount()->getMonthly(), 1489600) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getCopyColorPageCount()->getYearly(), 17896858) === 0);
    }

    public function testCopyCombinedPageCount ()
    {
        $deviceInstanceMeter                      = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate    = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate      = "2013-10-04 00:00:00";
        $deviceInstanceMeter->startMeterCopyColor = 0;
        $deviceInstanceMeter->endMeterCopyColor   = 24000;
        $deviceInstanceMeter->startMeterCopyBlack = 0;
        $deviceInstanceMeter->endMeterCopyBlack   = 25000;

        $this->assertTrue(bccomp($deviceInstanceMeter->getCopyCombinedPageCount()->getDaily(), 49000) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getCopyCombinedPageCount()->getMonthly(), 1489600) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getCopyCombinedPageCount()->getYearly(), 17896858) === 0);
    }

    public function testScanPageCount ()
    {
        $deviceInstanceMeter                   = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate   = "2013-10-04 00:00:00";
        $deviceInstanceMeter->startMeterScan   = 1000;
        $deviceInstanceMeter->endMeterScan     = 50000;

        $this->assertTrue(bccomp($deviceInstanceMeter->getScanPageCount()->getDaily(), 49000) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getScanPageCount()->getMonthly(), 1489600) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getScanPageCount()->getYearly(), 17896858) === 0);
    }

    public function testFaxPageCount ()
    {
        $deviceInstanceMeter                   = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate   = "2013-10-04 00:00:00";
        $deviceInstanceMeter->startMeterFax    = 1000;
        $deviceInstanceMeter->endMeterFax      = 50000;

        $this->assertTrue(bccomp($deviceInstanceMeter->getFaxPageCount()->getDaily(), 49000) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getFaxPageCount()->getMonthly(), 1489600) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getFaxPageCount()->getYearly(), 17896858) === 0);
    }

    public function testPrintA3BlackPageCount ()
    {
        $deviceInstanceMeter                         = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate       = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate         = "2013-10-04 00:00:00";
        $deviceInstanceMeter->startMeterPrintA3Black = 1000;
        $deviceInstanceMeter->endMeterPrintA3Black   = 50000;

        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintA3BlackPageCount()->getDaily(), 49000) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintA3BlackPageCount()->getMonthly(), 1489600) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintA3BlackPageCount()->getYearly(), 17896858) === 0);
    }

    public function testColorPrintA3PageCount ()
    {
        $deviceInstanceMeter                         = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate       = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate         = "2013-10-04 00:00:00";
        $deviceInstanceMeter->startMeterPrintA3Color = 1000;
        $deviceInstanceMeter->endMeterPrintA3Color   = 50000;

        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintA3ColorPageCount()->getDaily(), 49000) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintA3ColorPageCount()->getMonthly(), 1489600) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintA3ColorPageCount()->getYearly(), 17896858) === 0);
    }

    public function testPrintA3CombinedPageCount ()
    {
        $deviceInstanceMeter                         = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate       = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate         = "2013-10-04 00:00:00";
        $deviceInstanceMeter->startMeterPrintA3Color = 0;
        $deviceInstanceMeter->endMeterPrintA3Color   = 24000;
        $deviceInstanceMeter->startMeterPrintA3Black = 0;
        $deviceInstanceMeter->endMeterPrintA3Black   = 25000;

        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintA3CombinedPageCount()->getDaily(), 49000) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintA3CombinedPageCount()->getMonthly(), 1489600) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getPrintA3CombinedPageCount()->getYearly(), 17896858) === 0);
    }

    public function testLifePageCount ()
    {
        $deviceInstanceMeter                   = $this->getDeviceInstanceMeter(true);
        $deviceInstanceMeter->monitorStartDate = "2013-10-03 00:00:00";
        $deviceInstanceMeter->monitorEndDate   = "2013-10-04 00:00:00";
        $deviceInstanceMeter->startMeterLife   = 1000;
        $deviceInstanceMeter->endMeterLife     = 50000;

        $this->assertTrue(bccomp($deviceInstanceMeter->getLifePageCount()->getDaily(), 49000) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getLifePageCount()->getMonthly(), 1489600) === 0);
        $this->assertTrue(bccomp($deviceInstanceMeter->getLifePageCount()->getYearly(), 17896858) === 0);
    }
}