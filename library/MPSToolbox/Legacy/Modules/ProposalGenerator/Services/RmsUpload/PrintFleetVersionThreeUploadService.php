<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload;

use DateTime;

/**
 * Class PrintFleetVersionThreeUploadService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload
 */
class PrintFleetVersionThreeUploadService extends AbstractRmsUploadService
{
    /**
     * The csv value enclosure
     *
     * @var string
     */
    protected $csv_enclosure = '"';

    /**
     * How to read the date coming in from the CSV
     *
     * @var string
     */
    protected $_incomingDateFormat = [
        'Y-m-d\TH:i:s',
        'Y-m-d\TH:i:s.u',
        'Y-m-d\TH:i:s.uO',
        DateTime::ISO8601,
        'm/d/Y',
        'm/d/Y G:i',
        "m/d/Y h:i:s A",
    ];

    /**
     * Column mapping for CSV -> Upload Row.
     * This lets us accept different column names for a report and map the values to these.
     *
     * @var array
     */
    protected $_columnMapping = [
        'rmsvendorname'            => 'rmsVendorName',
        'rmsreportversion'         => 'rmsReportVersion',
        'rmsmodelid'               => 'rmsModelId',
        'deviceid'                 => 'assetId',
        'managementstatus'         => 'isManaged',
        'managementprogram'        => 'managementProgram',
        'monitorstartdate'         => 'monitorStartDate',
        'monitorenddate'           => 'monitorEndDate',
        'adoptiondate'             => 'adoptionDate',
        'discoverydate'            => 'discoveryDate',
        'introductiondate'         => 'launchDate',
        'ipaddress'                => 'ipAddress',
        'iscolor'                  => 'isColor',
        'iscopier'                 => 'isCopier',
        'isfax'                    => 'isFax',
        'isa3'                     => 'isA3',
        'isduplex'                 => 'isDuplex',
        'manufacturer'             => 'manufacturer',
        'modelname'                => 'modelName',
        'rawdevicename'            => 'rawDeviceName',
        'ppmblack'                 => 'ppmBlack',
        'ppmcolor'                 => 'ppmColor',
        'serialnumber'             => 'serialNumber',
        'wattsoperating'           => 'wattsOperating',
        'wattsidle'                => 'wattsIdle',
        'blacktonersku'            => 'blackTonerSku',
        'blacktoneryield'          => 'blackTonerYield',
        'cyantonersku'             => 'cyanTonerSku',
        'cyantoneryield'           => 'cyanTonerYield',
        'magentatonersku'          => 'magentaTonerSku',
        'magentatoneryield'        => 'magentaTonerYield',
        'yellowtonersku'           => 'yellowTonerSku',
        'yellowtoneryield'         => 'yellowTonerYield',
        'startmeterblack'          => 'startMeterBlack',
        'endmeterblack'            => 'endMeterBlack',
        'startmetercolor'          => 'startMeterColor',
        'endmetercolor'            => 'endMeterColor',
        'startmeterlife'           => 'startMeterLife',
        'endmeterlife'             => 'endMeterLife',
        'startmeterprintblack'     => 'startMeterPrintBlack',
        'endmeterprintblack'       => 'endMeterPrintBlack',
        'startmeterprintcolor'     => 'startMeterPrintColor',
        'endmeterprintcolor'       => 'endMeterPrintColor',
        'startmetercopyblack'      => 'startMeterCopyBlack',
        'endmetercopyblack'        => 'endMeterCopyBlack',
        'startmetercopycolor'      => 'startMeterCopyColor',
        'endmetercopycolor'        => 'endMeterCopyColor',
        'startmeterscan'           => 'startMeterScan',
        'endmeterscan'             => 'endMeterScan',
        'startmeterfax'            => 'startMeterFax',
        'endmeterfax'              => 'endMeterFax',
        'startmetervirtuala3black' => 'startMeterPrintA3Black',
        'endmetervirtuala3black'   => 'endMeterPrintA3Black',
        'startmetervirtuala3color' => 'startMeterPrintA3Color',
        'endmetervirtuala3color'   => 'endMeterPrintA3Color',
        'tonerlevelblack'          => 'tonerLevelBlack',
        'tonerlevelcyan'           => 'tonerLevelCyan',
        'tonerlevelmagenta'        => 'tonerLevelMagenta',
        'tonerlevelyellow'         => 'tonerLevelYellow',
        'monocoverage'             => 'pageCoverageMonochrome',
        'colorcoverage'            => 'pageCoverageColor',
        'location'                 => 'location',
    ];
}