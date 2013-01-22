<?php

class Default_Service_Rms_Upload_FmAudit extends Default_Service_Rms_Upload_Abstract
{
    /**
     * How to read the date coming in from the csv
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
    protected $_columnMapping = array(
        'printermodelid'       => 'rmsModelId',
        'modelname'            => 'modelName',
        'manufacturer'         => 'manufacturer',
        'serialnumber'         => 'serialNumber',
        'deviceipaddress'      => 'ipAddress',
        'is_color'             => 'isColor',
        'is_copier'            => 'isCopier',
        'is_scanner'           => 'isScanner',
        'is_fax'               => 'isFax',
        'ppm_black'            => 'ppmBlack',
        'ppm_color'            => 'ppmColor',
        'launchdate'           => 'introductionDate',
        'dateadoption'         => 'adoptionDate',
        'discoverydate'        => 'discoveryDate',
        'black_prodcodeoem'    => 'oemTonerBlackSku',
        'black_yield'          => 'oemTonerBlackYield',
        'black_prodcostoem'    => 'oemTonerBlackCost',
        'cyan_prodcodeoem'     => 'oemTonerCyanSku',
        'cyan_yield'           => 'oemTonerCyanYield',
        'cyan_prodcostoem'     => 'oemTonerCyanCost',
        'magenta_prodcodeoem'  => 'oemTonerMagentaSku',
        'magenta_yield'        => 'oemTonerMagentaYield',
        'magenta_prodcostoem'  => 'oemTonerMagentaCost',
        'yellow_prodcodeoem'   => 'oemTonerYellowSku',
        'yellow_yield'         => 'oemTonerYellowYield',
        'yellow_prodcostoem'   => 'oemTonerYellowCost',
        'dutycycle'            => 'dutyCycle',
        'wattsoperating'       => 'wattsOperating',
        'wattsidle'            => 'wattsIdle',
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
        'tonerlevel_black'     => 'tonerLevelBlack',
        'tonerlevel_cyan'      => 'tonerLevelCyan',
        'tonerlevel_magenta'   => 'tonerLevelMagenta',
        'tonerlevel_yellow'    => 'tonerLevelYellow',
        'startdate'            => 'monitorStartDate',
        'enddate'              => 'monitorEndDate'
    );
}