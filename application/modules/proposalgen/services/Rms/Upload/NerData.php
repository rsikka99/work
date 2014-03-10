<?php

/**
 * Class Proposalgen_Service_Rms_Upload_NerData
 */
class Proposalgen_Service_Rms_Upload_NerData extends Proposalgen_Service_Rms_Upload_Abstract
{
    /**
     * How to read the date coming in from the CSV
     *
     * @var string
     */
    protected $_incomingDateFormat = array(
        "n/j/Y",
        "n/d/Y G:i",
    );

    /**
     * Column mapping for CSV -> Upload Row.
     * This lets us accept different column names for a report and map the values to these.
     *
     * @var array
     */
    protected $_columnMapping = array(
        'rmsmodelid'             => 'rmsModelId',
        'modelname'              => 'modelName',
        'manufacturer'           => 'manufacturer',
        'serialnumber'           => 'serialNumber',
        'ipaddress'              => 'ipAddress',
        'iscolor'                => 'isColor',
        'iscopier'               => 'isCopier',
        'isfax'                  => 'isFax',
        'isduplex'               => 'isDuplex',
        'introductiondate'       => 'introductionDate',
        'adoptiondate'           => 'adoptionDate',
        'discoverydate'          => 'discoveryDate',
        'oemtonerblacksku'       => 'oemTonerBlackSku',
        'oemtonerblackyield'     => 'oemTonerBlackYield',
        'oemtonerblackcost'      => 'oemTonerBlackCost',
        'oemtonercyansku'        => 'oemTonerCyanSku',
        'oemtonercyanyield'      => 'oemTonerCyanYield',
        'oemtonercyancost'       => 'oemTonerCyanCost',
        'oemtonermagentasku'     => 'oemTonerMagentaSku',
        'oemtonermagentayield'   => 'oemTonerMagentaYield',
        'oemtonermagentacost'    => 'oemTonerMagentaCost',
        'oemtoneryellowsku'      => 'oemTonerYellowSku',
        'oemtoneryellowyield'    => 'oemTonerYellowYield',
        'oemtoneryellowcost'     => 'oemTonerYellowCost',
        'dutycycle'              => 'dutyCycle',
        'wattsoperating'         => 'wattsOperating',
        'wattsidle'              => 'wattsIdle',
        'startmeterlife'         => 'startMeterLife',
        'endmeterlife'           => 'endMeterLife',
        'startmeterblack'        => 'startMeterBlack',
        'endmeterblack'          => 'endMeterBlack',
        'startmetercolor'        => 'startMeterColor',
        'endmetercolor'          => 'endMeterColor',
        'startmeterprintblack'   => 'startMeterPrintBlack',
        'endmeterprintblack'     => 'endMeterPrintBlack',
        'startmeterprintcolor'   => 'startMeterPrintColor',
        'endmeterprintcolor'     => 'endMeterPrintColor',
        'startmetercopyblack'    => 'startMeterCopyBlack',
        'endmetercopyblack'      => 'endMeterCopyBlack',
        'startmetercopycolor'    => 'startMeterCopyColor',
        'endmetercopycolor'      => 'endMeterCopyColor',
        'startmeterscan'         => 'startMeterScan',
        'endmeterscan'           => 'endMeterScan',
        'startmeterfax'          => 'startMeterFax',
        'endmeterfax'            => 'endMeterFax',
        'startmeterprinta3black' => 'startMeterPrintA3Black',
        'endmeterprinta3black'   => 'endMeterPrintA3Black',
        'startmeterprinta3color' => 'startMeterPrintA3Color',
        'endmeterprinta3color'   => 'endMeterPrintA3Color',
        'tonerlevelblack'        => 'tonerLevelBlack',
        'tonerlevelcyan'         => 'tonerLevelCyan',
        'tonerlevelmagenta'      => 'tonerLevelMagenta',
        'tonerlevelyellow'       => 'tonerLevelYellow',
        'monitorstartdate'       => 'monitorStartDate',
        'monitorenddate'         => 'monitorEndDate',
    );
}