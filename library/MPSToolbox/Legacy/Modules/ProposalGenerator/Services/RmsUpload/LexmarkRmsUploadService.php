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
    protected $_incomingDateFormat = "MM/dd/yyyy HH:ii:ss";

    /**
     * Column mapping for CSV -> Upload Row.
     * This lets us accept different column names for a report and map the values to these.
     *
     * @var array
     */
    protected $_columnMapping = [

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
        'startMeterPrintBl..' => 'startMeterPrintBlack',
        'endMeterPrintBla..'  => 'endMeterPrintBlack',
        'startMeterPrintC..'  => 'startMeterPrintColor',
        'endMeterPrintCo..'   => 'endMeterPrintColor',
        'startMeterCopyBl..'  => 'startMeterCopyBlack',
        'endMeterCopyBl..'    => 'endMeterCopyBlack',
        'startMeterCopyC..'   => 'startMeterCopyColor',
        'endMeterCopyC..'     => 'endMeterCopyColor',
        'startMeterScan'      => 'startMeterScan',
        'endMeterScan'        => 'endMeterScan',
        'startMeterFax'       => 'startMeterFax',
        'endMeterFax'         => 'endMeterFax',
        'startMeterA3Black'   => 'startMeterPrintA3Black',
        'endMeterA3Black'     => 'endMeterPrintA3Black',
        'startMeterA3Color'   => 'startMeterPrintA3Color',
        'endMeterA3Color'     => 'endMeterPrintA3Color',
        'canReportToner..'    => 'reportsTonerLevels',
        'tonerLevelBlack'     => 'tonerLevelBlack',
        'tonerLevelCyan'      => 'tonerLevelCyan',
        'tonerLevelMagen..'   => 'tonerLevelMagenta',
        'tonerLevelYellow'    => 'tonerLevelYellow',
        'pageCoverageBl..'    => 'pageCoverageMonochrome',
        'pageCoverageC..'     => 'pageCoverageCyan',
        'pageCoverageM..'     => 'pageCoverageMagenta',
        'pageCoverageY..'     => 'pageCoverageYellow',
        'managementStat..'    => 'isManaged',
        'managementPro..'     => 'managementProgram',
        'location'            => 'location',
    ];
}

