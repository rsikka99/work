<?php

/**
 * Class Proposalgen_Service_Rms_Upload_FmAudit
 */
class Proposalgen_Service_Rms_Upload_FmAudit extends Proposalgen_Service_Rms_Upload_Abstract
{
    /**
     * How to read the date coming in from the csv
     *
     * @var string
     */
    protected $_incomingDateFormat = "m/d/Y";

    /**
     * Column mapping for CSV -> Upload Row.
     * This lets us accept different column names for a report and map the values to these.
     *
     * @var array
     */
    protected $_columnMapping = array(
        'printer model id'         => 'rmsModelId',
        'device id'                => 'assetId',
        'start date'               => 'monitorStartDate',
        'end date'                 => 'monitorEndDate',
        'date adoption'            => 'adoptionDate',
        'discovery date'           => 'discoveryDate',
        'launch date'              => 'launchDate',
        'duty cycle'               => 'dutyCycle',
        'device ip address'        => 'ipAddress',
        'is color'                 => 'isColor',
        'is copier'                => 'isCopier',
        'is fax'                   => 'isFax',
        'manufacturer'             => 'manufacturer',
        'model name'               => 'modelName',
        'ppm black'                => 'ppmBlack',
        'ppm color'                => 'ppmColor',
        'serial number'            => 'serialNumber',
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
        'start meter black'        => 'startMeterBlack',
        'end meter black'          => 'endMeterBlack',
        'start meter color'        => 'startMeterColor',
        'end meter color'          => 'endMeterColor',
        'start meter life'         => 'startMeterLife',
        'end meter life'           => 'endMeterLife',
        'start meter print black ' => 'startMeterPrintBlack',
        'end meter print black '   => 'endMeterPrintBlack',
        'start meter print color ' => 'startMeterPrintColor',
        'end meter print color '   => 'endMeterPrintColor',
        'start meter copy black'   => 'startMeterCopyBlack',
        'end meter copy black'     => 'endMeterCopyBlack',
        'start meter copy color'   => 'startMeterCopyColor',
        'end meter copy color'     => 'endMeterCopyColor',
        'start meter scan'         => 'startMeterScan',
        'end meter scan'           => 'endMeterScan',
        'start meter fax'          => 'startMeterFax',
        'end meter fax'            => 'endMeterFax',
        'toner level black'        => 'tonerLevelBlack',
        'toner level cyan'         => 'tonerLevelCyan',
        'toner level magenta'      => 'tonerLevelMagenta',
        'toner level yellow'       => 'tonerLevelYellow'
    );
}