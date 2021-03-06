<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload;

/**
 * Class PrintAuditUploadService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload
 */
class PrintAuditUploadService extends AbstractRmsUploadService
{
    /**
     * How to read the date coming in from the CSV
     *
     * @var string
     */
    protected $_incomingDateFormat = [
        "m/d/Y",
    ];

    /**
     * Column mapping for CSV -> Upload Row.
     * This lets us accept different column names for a report and map the values to these.
     *
     * @var array
     */
//        'rmsModelId'             => 'rmsModelId',
//        'adoptionDate'           => 'adoptionDate',
//        'isCopier'               => 'isCopier',
//        'isFax'                  => 'isFax',
//        'wattsOperating'         => 'wattsOperating',
//        'wattsIdle'              => 'wattsIdle',
//        'blackTonerSku'          => 'blackTonerSku',
//        'blackTonerYield'        => 'blackTonerYield',
//        'blackTonerCost'         => 'blackTonerCost',
//        'cyanTonerSku'           => 'cyanTonerSku',
//        'cyanTonerYield'         => 'cyanTonerYield',
//        'cyanTonerCost'          => 'cyanTonerCost',
//        'magentaTonerSku'        => 'magentaTonerSku',
//        'magentaTonerYield'      => 'magentaTonerYield',
//        'magentaTonerCost'       => 'magentaTonerCost',
//        'yellowTonerSku'         => 'yellowTonerSku',
//        'yellowTonerYield'       => 'yellowTonerYield',
//        'yellowTonerCost'        => 'yellowTonerCost',
    protected $_columnMapping = [
        'rmsmodelid'             => 'rmsModelId',
        'assetid'                => 'assetId',
        'ismanaged'              => 'isManaged',
        'monitordate'            => 'monitorStartDate',
        'monitordate2'           => 'monitorEndDate',
        'discoverydate'          => 'discoveryDate',
        'launchdate'             => 'launchDate',
        'ipaddress'              => 'ipAddress',
        'iscolor'                => 'isColor',
        'manufacturer'           => 'manufacturer',
        'name'                   => 'modelName',
        'ppmblack'               => 'ppmBlack',
        'ppmcolor'               => 'ppmColor',
        'serialnumber'           => 'serialNumber',
        'startmeterblack'        => 'startMeterBlack',
        'endmeterblack'          => 'endMeterBlack',
        'startmetercolor'        => 'startMeterColor',
        'endmetercolor'          => 'endMeterColor',
        'startmeterlife'         => 'startMeterLife',
        'endmeterlife'           => 'endMeterLife',
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
        'tonerlevelblack'        => 'tonerLevelBlack',
        'tonerlevelcyan'         => 'tonerLevelCyan',
        'tonerlevelmagenta'      => 'tonerLevelMagenta',
        'tonerlevelyellow'       => 'tonerLevelYellow',
        'pagecoveragemonochrome' => 'pageCoverageMonochrome',
        'pagecoveragecyan'       => 'pageCoverageCyan',
        'pagecoveragemagenta'    => 'pageCoverageMagenta',
        'pagecoverageyellow'     => 'pageCoverageYellow',
        'location'               => 'location',
    ];


    /**
     * Overrides the abstract process data so that we can extract the date from a single field.
     *
     * @param array $csvData
     *
     * @return array
     */
    public function processData ($csvData)
    {
        if (isset($csvData['monitorStartDate']))
        {
            $monitorDates                = explode(' - ', $csvData['monitorStartDate'],2);
            if (count($monitorDates)==2) {
                $csvData['monitorStartDate'] = $monitorDates[0];
                $csvData['monitorEndDate'] = $monitorDates[1];
            } else {
                $csvData['monitorStartDate'] = '';
                $csvData['monitorEndDate'] = '';
            }
        }

        return parent::processData($csvData);
    }
}