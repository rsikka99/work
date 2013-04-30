<?php

/**
 * Class Proposalgen_Service_Rms_Upload_PrintFleet
 */
class Proposalgen_Service_Rms_Upload_PrintFleet extends Proposalgen_Service_Rms_Upload_Abstract
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
        'startdate'            => 'monitorStartDate',
        'enddate'              => 'monitorEndDate',
        'dateadoption'         => 'adoptionDate',
        'discoverydate'        => 'discoveryDate',
        'dateintroduction'     => 'launchDate',
        'duty_cycle'           => 'dutyCycle',
        'ipaddress'            => 'ipAddress',
        'is_color'             => 'isColor',
        'is_copier'            => 'isCopier',
        'is_scanner'           => 'isScanner',
        'is_fax'               => 'isFax',
        'manufacturer'         => 'manufacturer',
        'modelname'            => 'modelName',
        'ppm_black'            => 'ppmBlack',
        'ppm_color'            => 'ppmColor',
        'serialnumber'         => 'serialNumber',
        'wattspowernormal'     => 'wattsOperating',
        'wattspoweridle'       => 'wattsIdle',
        'black_prodcodeoem'    => 'blackTonerSku',
        'black_yield'          => 'blackTonerYield',
        'black_prodcostoem'    => 'blackTonerCost',
        'cyan_prodcodeoem'     => 'cyanTonerSku',
        'cyan_yield'           => 'cyanTonerYield',
        'cyan_prodcostoem'     => 'cyanTonerCost',
        'magenta_prodcodeoem'  => 'magentaTonerSku',
        'magenta_yield'        => 'magentaTonerYield',
        'magenta_prodcostoem'  => 'magentaTonerCost',
        'yellow_prodcodeoem'   => 'yellowTonerSku',
        'yellow_yield'         => 'yellowTonerYield',
        'yellow_prodcostoem'   => 'yellowTonerCost',
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
        'tonerlevel_black'     => 'tonerLevelBlack',
        'tonerlevel_cyan'      => 'tonerLevelCyan',
        'tonerlevel_magenta'   => 'tonerLevelMagenta',
        'tonerlevel_yellow'    => 'tonerLevelYellow'
    );
}