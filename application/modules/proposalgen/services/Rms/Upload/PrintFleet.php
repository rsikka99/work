<?php

/**
 * Class Proposalgen_Service_Rms_Upload_PrintFleet
 */
class Proposalgen_Service_Rms_Upload_PrintFleet extends Proposalgen_Service_Rms_Upload_Abstract
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
    protected $_incomingDateFormat = array(
        "Y-m-d\TH:i:s",
        "Y-m-d\TH:i:s.u",
        "Y-m-d\TH:i:s.uO",
        DateTime::ISO8601,
        "m/d/Y G:i",
    );

    /**
     * Column mapping for CSV -> Upload Row.
     * This lets us accept different column names for a report and map the values to these.
     *
     * @var array
     */
    protected $_columnMapping = array(
        'rmsvendorname'        => 'rmsVendorName',
        'rmsreportversion'     => 'rmsReportVersion',
        'rmsmodelid'           => 'rmsModelId',
        'deviceid'             => 'assetId',
        'managementstatus'     => 'isManaged',
        'managementprogram'    => 'managementProgram',
        'monitorstartdate'     => 'monitorStartDate',
        'monitorenddate'       => 'monitorEndDate',
        'adoptiondate'         => 'adoptionDate',
        'discoverydate'        => 'discoveryDate',
        'introductiondate'     => 'launchDate',
        'ipaddress'            => 'ipAddress',
        'iscolor'              => 'isColor',
        'iscopier'             => 'isCopier',
        'isfax'                => 'isFax',
        'isa3'                 => 'isA3',
        'isduplex'             => 'isDuplex',
        'manufacturer'         => 'manufacturer',
        'modelname'            => 'modelName',
        'rawdevicename'        => 'rawDeviceName',
        'ppmblack'             => 'ppmBlack',
        'ppmcolor'             => 'ppmColor',
        'serialnumber'         => 'serialNumber',
        'wattsoperating'       => 'wattsOperating',
        'wattsidle'            => 'wattsIdle',
        'blacktonersku'        => 'blackTonerSku',
        'blacktoneryield'      => 'blackTonerYield',
        'cyantonersku'         => 'cyanTonerSku',
        'cyantoneryield'       => 'cyanTonerYield',
        'magentatonersku'      => 'magentaTonerSku',
        'magentatoneryield'    => 'magentaTonerYield',
        'yellowtonersku'       => 'yellowTonerSku',
        'yellowtoneryield'     => 'yellowTonerYield',
        'startmeterblack'      => 'startMeterBlack',
        'endmeterblack'        => 'endMeterBlack',
        'startmetercolor'      => 'startMeterColor',
        'endmetercolor'        => 'endMeterColor',
        'startmeterlife'       => 'startMeterLife',
        'endmeterlife'         => 'endMeterLife',
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
        'startmetera3black'    => 'startMeterPrintA3Black',
        'endmetera3black'      => 'endMeterPrintA3Black',
        'startmetera3color'    => 'startMeterPrintA3Color',
        'endmetera3color'      => 'endMeterPrintA3Color',
        'tonerlevelblack'      => 'tonerLevelBlack',
        'tonerlevelcyan'       => 'tonerLevelCyan',
        'tonerlevelmagenta'    => 'tonerLevelMagenta',
        'tonerlevelyellow'     => 'tonerLevelYellow',
        'monocoverage'         => 'pageCoverageMonochrome',
        'colorcoverage'        => 'pageCoverageColor',
    );
}