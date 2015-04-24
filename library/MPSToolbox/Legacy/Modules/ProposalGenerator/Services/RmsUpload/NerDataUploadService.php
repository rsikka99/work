<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload;

/**
 * Class NerDataUploadService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload
 */
class NerDataUploadService extends AbstractRmsUploadService
{
    /**
     * How to read the date coming in from the CSV
     *
     * @var string
     */
    protected $_incomingDateFormat = [
        "n/j/Y",
        "m/d/Y",
        "n/d/Y G:i",
    ];

    /**
     * Column mapping for CSV -> Upload Row.
     * This lets us accept different column names for a report and map the values to these.
     *
     * @var array
     */
    protected $_columnMapping = [
        'rmsvendorname'        => 'rmsVendorName',
        'rmsreportversion'     => 'rmsReportVersion',
        'rmsmodelid'           => 'rmsModelId',
        'deviceid'             => 'assetId',
        'manufacturer'         => 'manufacturer',
        'modelname'            => 'modelName',
        'managementstatus'     => 'isManaged',
        'managementprogram'    => 'managementProgram',
        'serialnumber'         => 'serialNumber',
        'ipaddress'            => 'ipAddress',
        'iscolor'              => 'isColor',
        'iscopier'             => 'isCopier',
        'isfax'                => 'isFax',
        'isduplex'             => 'isDuplex',
        'ppmblack'             => 'ppmBlack',
        'ppmcolor'             => 'ppmColor',
        'introductiondate'     => 'launchDate',
        'adoptiondate'         => 'adoptionDate',
        'dutycycle'            => 'dutyCycle',
        'oemtonerblacksku'     => 'blackTonerSku',
        'oemtonerblackyield'   => 'blackTonerYield',
        'oemtonerblackcost'    => 'blackTonerCost',
        'oemtonercyansku'      => 'cyanTonerSku',
        'oemtonercyanyield'    => 'cyanTonerYield',
        'oemtonercyancost'     => 'cyanTonerCost',
        'oemtonermagentasku'   => 'magentaTonerSku',
        'oemtonermagentayield' => 'magentaTonerYield',
        'oemtonermagentacost'  => 'magentaTonerCost',
        'oemtoneryellowsku'    => 'yellowTonerSku',
        'oemtoneryellowyield'  => 'yellowTonerYield',
        'oemtoneryellowcost'   => 'yellowTonerCost',
        'operatingwattage'     => 'wattsOperating',
        'standbywattage'       => 'wattsIdle',
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
        'startmetera3black'    => 'startMeterPrintA3Black',
        'endmetera3black'      => 'endMeterPrintA3Black',
        'startmetera3color'    => 'startMeterPrintA3Color',
        'endmetera3color'      => 'endMeterPrintA3Color',
        'tonerlevelblack'      => 'tonerLevelBlack',
        'tonerlevelcyan'       => 'tonerLevelCyan',
        'tonerlevelmagenta'    => 'tonerLevelMagenta',
        'tonerlevelyellow'     => 'tonerLevelYellow',
        'pagecoverageblack'    => 'pageCoverageBlack',
        'pagecoveragecyan'     => 'pageCoverageCyan',
        'pagecoveragemagenta'  => 'pageCoverageMagenta',
        'pagecoverageyellow'   => 'pageCoverageYellow',
        'discoverydate'        => 'discoveryDate',
        'monitorstartdate'     => 'monitorStartDate',
        'monitorenddate'       => 'monitorEndDate',
    ];
}