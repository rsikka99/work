<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload;

/**
 * Class PrintFleetVersionTwoUploadService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload
 */
class PrintFleetVersionTwoUploadService extends AbstractRmsUploadService
{
    /**
     * How to read the date coming in from the CSV
     *
     * @var string
     */
    protected $_incomingDateFormat = [
        "m/d/Y h:i:s A",
        "m/d/Y G:i",
    ];

    /**
     * Column mapping for CSV -> Upload Row.
     * This lets us accept different column names for a report and map the values to these.
     *
     * @var array
     */
    protected $_columnMapping = [
        'printermodelid'       => 'rmsModelId',
        'managementstatus'     => 'isManaged',
        'startdate'            => 'monitorStartDate',
        'enddate'              => 'monitorEndDate',
        'dateadoption'         => 'adoptionDate',
        'discoverydate'        => 'discoveryDate',
        'dateintroduction'     => 'launchDate',
        'ipaddress'            => 'ipAddress',
        'is_color'             => 'isColor',
        'is_copier'            => 'isCopier',
        'is_fax'               => 'isFax',
        'is_a3'                => 'isA3',
        'is_duplex'            => 'isDuplex',
        'manufacturer'         => 'manufacturer',
        'devicename'           => 'rawDeviceName',
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
        'start11x7'            => 'startMeterPrintA3Black',
        'end11x7'              => 'endMeterPrintA3Black',
        'start11x17color'      => 'startMeterPrintA3Color',
        'end11x17color'        => 'endMeterPrintA3Color',
        'tonerlevel_black'     => 'tonerLevelBlack',
        'tonerlevel_cyan'      => 'tonerLevelCyan',
        'tonerlevel_magenta'   => 'tonerLevelMagenta',
        'tonerlevel_yellow'    => 'tonerLevelYellow',
        'black coverage'       => 'pageCoverageMonochrome',
        'cyan coverage'        => 'pageCoverageCyan',
        'magenta coverage'     => 'pageCoverageMagenta',
        'yellow coverage'      => 'pageCoverageYellow',
        'location'             => 'location',
    ];
}