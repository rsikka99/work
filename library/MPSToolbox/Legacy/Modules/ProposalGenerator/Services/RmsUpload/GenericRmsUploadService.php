<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload;

/**
 * Class GenericRmsUploadService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload
 */
class GenericRmsUploadService extends AbstractRmsUploadService
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
        'printermodelid'         => 'rmsModelId',
        'modelname'              => 'modelName',
        'manufacturer'           => 'manufacturer',
        'serialnumber'           => 'serialNumber',
        'ipaddress'              => 'ipAddress',
        'is_color'               => 'isColor',
        'is_copier'              => 'isCopier',
        'is_fax'                 => 'isFax',
        'is_a3'                  => 'isA3',
        'is_duplex'              => 'isDuplex',
        'ppm_black'              => 'ppmBlack',
        'ppm_color'              => 'ppmColor',
        'dateintroduction'       => 'introductionDate',
        'dateadoption'           => 'adoptionDate',
        'discoverydate'          => 'discoveryDate',
        'black_prodcodeoem'      => 'oemTonerBlackSku',
        'black_yield'            => 'oemTonerBlackYield',
        'black_prodcostoem'      => 'oemTonerBlackCost',
        'cyan_prodcodeoem'       => 'oemTonerCyanSku',
        'cyan_yield'             => 'oemTonerCyanYield',
        'cyan_prodcostoem'       => 'oemTonerCyanCost',
        'magenta_prodcodeoem'    => 'oemTonerMagentaSku',
        'magenta_yield'          => 'oemTonerMagentaYield',
        'magenta_prodcostoem'    => 'oemTonerMagentaCost',
        'yellow_prodcodeoem'     => 'oemTonerYellowSku',
        'yellow_yield'           => 'oemTonerYellowYield',
        'yellow_prodcostoem'     => 'oemTonerYellowCost',
        'wattspowernormal'       => 'wattsOperating',
        'wattspoweridle'         => 'wattsIdle',
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
        'tonerlevel_black'       => 'tonerLevelBlack',
        'tonerlevel_cyan'        => 'tonerLevelCyan',
        'tonerlevel_magenta'     => 'tonerLevelMagenta',
        'tonerlevel_yellow'      => 'tonerLevelYellow',
        'startdate'              => 'monitorStartDate',
        'enddate'                => 'monitorEndDate',
        'location'               => 'location',
    ];
}