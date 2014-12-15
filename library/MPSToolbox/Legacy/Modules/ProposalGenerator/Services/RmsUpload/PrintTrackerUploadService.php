<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload;

use DateTime;

/**
 * Class PrintTrackerUploadService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload
 */
class PrintTrackerUploadService extends AbstractRmsUploadService
{
    /**
     * How to read the date coming in from the CSV
     *
     * @var string
     */
    protected $_incomingDateFormat = DateTime::ISO8601;


    /**
     * Column mapping for CSV -> Upload Row.
     * This lets us accept different column names for a report and map the values to these.
     *
     * @var array
     */
    protected $_columnMapping = array(
        'rmsvendorname'        => 'rmsVendorName',
        'rmsreportversion'     => 'rmsReportVersion',
        'clientname'           => 'clientName',
        'rmsmodelid'           => 'rmsModelId',
        'manufacturer'         => 'manufacturer',
        'modelname'            => 'modelName',
        'rawdevicename'        => 'rawDeviceName',
        'managementstatus'     => 'managementStatus',
        'serialnumber'         => 'serialNumber',
        'ipaddress'            => 'ipAddress',
        'startmeterlife'       => 'startMeterLife',
        'endmeterlife'         => 'endMeterLife',
        'startmeterblack'      => 'startMeterBlack',
        'endmeterblack'        => 'endMeterBlack',
        'startmetercolor'      => 'startMeterColor',
        'endmetercolor'        => 'endMeterColor',
        'startmeterprintblack' => 'startMeterPrintBlack',
        'endmeterprintblack'   => 'endMeterPrintBlack',
        'startmeterprintcolor' => 'startMeterPrintColor',
        'endmeterprintcolor'   => 'endMeterPrintColor',
        'startmetercopyblack'  => 'startMeterCopyBlack',
        'endmetercopyblack'    => 'endMeterCopyBlack',
        'startmetercopycolor'  => 'startMeterCopyColor',
        'endmetercopycolor'    => 'endMeterCopyColor',
        'startmeterscan'       => 'startMeterScan',
        'endmeterscan'         => 'endMeterScan',
        'startmeterfax'        => 'startMeterFax',
        'endmeterfax'          => 'endMeterFax',
        'startmetera3black'    => 'startMeterA3Black',
        'endmetera3black'      => 'endMeterA3Black',
        'startmetera3color'    => 'startMeterA3Color',
        'endmetera3color'      => 'endMeterA3Color',
        'tonerlevelblack'      => 'tonerLevelBlack',
        'tonerlevelcyan'       => 'tonerLevelCyan',
        'tonerlevelmagenta'    => 'tonerLevelMagenta',
        'tonerlevelyellow'     => 'tonerLevelYellow',
        'canreporttonerlevels' => 'canReportTonerLevels',
        'pagecoverageblack'    => 'pageCoverageBlack',
        'pagecoveragecyan'     => 'pageCoverageCyan',
        'pagecoveragemagenta'  => 'pageCoverageMagenta',
        'pagecoverageyellow'   => 'pageCoverageYellow',
        'discoverydate'        => 'discoveryDate',
        'monitorstartdate'     => 'monitorStartDate',
        'monitorenddate'       => 'monitorEndDate',
        'lastseendate'         => 'lastSeenDate',
        'location'             => 'location',
    );
}