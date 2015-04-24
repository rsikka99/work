<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload;

/**
 * Class FmAuditUploadService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload
 */
class FmAuditVersionFourUploadService extends AbstractRmsUploadService
{
    /**
     * How to read the date coming in from the CSV
     *
     * @var string
     */
    protected $_incomingDateFormat = [
        "m/d/Y h:i A",
    ];

    /**
     * Column mapping for CSV -> Upload Row.
     * This lets us accept different column names for a report and map the values to these.
     *
     * @var array
     */
    protected $_columnMapping = [
        'rmsvendorname'             => 'rmsVendorName',
        'rmsreportversion'          => 'rmsReportVersion',
        'clientname'                => 'clientName',
        'rmsmodelid'                => 'rmsModelId',
        'deviceid'                  => 'assetId',
        'monitorstartdate'          => 'monitorStartDate',
        'monitorenddate'            => 'monitorEndDate',
        'date adoption'            => 'adoptionDate',
        'discoverydate'             => 'discoveryDate',
        'launch date'              => 'launchDate',
        'lastseendate'              => 'lastSeenDate',
        'launch date'              => 'launchDate',
        'ipaddress'                 => 'ipAddress',
        'iscolor'                   => 'isColor',
        'is copier'                => 'isCopier',
        'is fax'                   => 'isFax',
        'is a3'                    => 'isA3',
        'is duplex'                => 'isDuplex',
        'manufacturer'             => 'manufacturer',
        'modelname'                 => 'modelName',
        //'manangementStatus' => '?',
        'ppm black'                => 'ppmBlack',
        'ppm color'                => 'ppmColor',
        'serialnumber'              => 'serialNumber',
        'watts operating'          => 'wattsOperating',
        'watts idle'               => 'wattsIdle',
        'black prod code oem'      => 'blackTonerSku',
        'black yield'              => 'blackTonerYield',
        'black prod cost oem'      => 'blackTonerCost',
        'cyan prod code oem'       => 'cyanTonerSku',
        'cyan yield'               => 'cyanTonerYield',
        'cyan prod cost oem'       => 'cyanTonerCost',
        'magenta prod code oem'    => 'magentaTonerSku',
        'magenta yield'            => 'magentaTonerYield',
        'magenta prod cost oem'    => 'magentaTonerCost',
        'yellow prod code oem'     => 'yellowTonerSku',
        'yellow yield'             => 'yellowTonerYield',
        'yellow prod cost oem'     => 'yellowTonerCost',
        'startmeterblack'           => 'startMeterBlack',
        'endmeterblack'             => 'endMeterBlack',
        'startmetercolor'           => 'startMeterColor',
        'endmetercolor'             => 'endMeterColor',
        'startmeterlife'            => 'startMeterLife',
        'endmeterlife'              => 'endMeterLife',
        'startmeterprintblack'      => 'startMeterPrintBlack',
        'endmeterprintblack'        => 'endMeterPrintBlack',
        'startmeterprintcolor'      => 'startMeterPrintColor',
        'endmeterprintcolor'        => 'endMeterPrintColor',
        'startmetercopyblack'       => 'startMeterCopyBlack',
        'endmetercopyblack'         => 'endMeterCopyBlack',
        'startmetercopycolor'       => 'startMeterCopyColor',
        'endmetercopycolor'         => 'endMeterCopyColor',
        'startmeterscan'            => 'startMeterScan',
        'endmeterscan'              => 'endMeterScan',
        'startmeterfax'             => 'startMeterFax',
        'endmeterfax'               => 'endMeterFax',
        //'startMeterA3Life' => '?',
        //'endMeterA3Life' => '?',
        'tonerlevelblack'           => 'tonerLevelBlack',
        'tonerlevelcyan'            => 'tonerLevelCyan',
        'tonerlevelmagenta'         => 'tonerLevelMagenta',
        'tonerlevelyellow'          => 'tonerLevelYellow',
        'pagecoverageblack'         => 'pageCoverageBlack',
        'pagecoveragecyan'          => 'pageCoverageCyan',
        'pagecoveragemagenta'       => 'pageCoverageMagenta',
        'pagecoverageyellow'        => 'pageCoverageYellow',
        'location'                  => 'location',
    ];
}
