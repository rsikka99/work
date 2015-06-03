<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload;

/**
 * Class LexmarkRmsUploadService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload
 */
class LexmarkRmsUploadService extends AbstractRmsUploadService
{
    /**
     * How to read the date coming in from the CSV
     *
     * @var string
     */
    protected $_incomingDateFormat = [
        "m/d/Y",
        "m/d/Y H:i:s"
    ];

    public function getColumnMapping() {
        if (empty($this->_columnMapping)) {
            $this->_columnMapping = [];
            foreach ([
 'rmsVendorName'       => 'rmsVendorName',
 'rmsReportVersion'    => 'rmsReportVersion',
 'rmsModelId'          => 'rmsModelId',
 'deviceId'            => 'assetId',
 'monitorStartDate'    => 'monitorStartDate',
 'monitorEndDate'      => 'monitorEndDate',
 'adoptionDate'        => 'adoptionDate',
 'discoveryDate'       => 'discoveryDate',
 'introductionDate'    => 'launchDate',
 'ipAddress'           => 'ipAddress',
 'isColor'             => 'isColor',
 'isCopier'            => 'isCopier',
 'isFax'               => 'isFax',
 'isDuplex'            => 'isDuplex',
 'manufacturer'        => 'manufacturer',
 'rawModelName'        => 'rawDeviceName',
 'modelName'           => 'modelName',
 'ppmMono'             => 'ppmBlack',
 'ppmColor'            => 'ppmColor',
 'serialNumber'        => 'serialNumber',
 'operatingWattage'    => 'wattsOperating',
 'standbyWattage'      => 'wattsIdle',
 'startMeterBlack'     => 'startMeterBlack',
 'endMeterBlack'       => 'endMeterBlack',
 'startMeterColor'     => 'startMeterColor',
 'endMeterColor'       => 'endMeterColor',
 'startMeterLife'      => 'startMeterLife',
 'endMeterLife'        => 'endMeterLife',
 'startMeterPrintBlack'=> 'startMeterPrintBlack',
 'endMeterPrintBlack'  => 'endMeterPrintBlack',
 'startMeterPrintColor'=> 'startMeterPrintColor',
 'endMeterPrintColor'  => 'endMeterPrintColor',
 'startMeterCopyBlack' => 'startMeterCopyBlack',
 'endMeterCopyBlack'   => 'endMeterCopyBlack',
 'startMeterCopyColor' => 'startMeterCopyColor',
 'endMeterCopyColor'   => 'endMeterCopyColor',
 'startMeterScan'      => 'startMeterScan',
 'endMeterScan'        => 'endMeterScan',
 'startMeterFax'       => 'startMeterFax',
 'endMeterFax'         => 'endMeterFax',
 'startMeterA3Black'   => 'startMeterPrintA3Black',
 'endMeterA3Black'     => 'endMeterPrintA3Black',
 'startMeterA3Color'   => 'startMeterPrintA3Color',
 'endMeterA3Color'     => 'endMeterPrintA3Color',
 'reportsTonerLevels'  => 'reportsTonerLevels',
 'tonerLevelBlack'     => 'tonerLevelBlack',
 'tonerLevelCyan'      => 'tonerLevelCyan',
 'tonerLevelMagenta'   => 'tonerLevelMagenta',
 'tonerLevelYellow'    => 'tonerLevelYellow',
 'pageCoverageMonochrome'=> 'pageCoverageMonochrome',
 'pageCoverageCyan'    => 'pageCoverageCyan',
 'pageCoverageMagenta' => 'pageCoverageMagenta',
 'pageCoverageYellow'  => 'pageCoverageYellow',
 'isManaged'           => 'isManaged',
 'managementProgram'   => 'managementProgram',
 'location'            => 'location',
                ] as $key=>$value) {
                $this->_columnMapping[strtolower($key)] = $value;
            }
        }
        return $this->_columnMapping;
    }

}

