<?php

/**
 * Class Proposalgen_Service_Rms_Upload_PrintAudit
 */
class Proposalgen_Service_Rms_Upload_PrintAudit extends Proposalgen_Service_Rms_Upload_Abstract
{
    /**
     * How to read the date coming in from the csv
     *
     * @var string
     */
    protected $_incomingDateFormat = array(
        "m/d/Y",
    );

    /**
     * Column mapping for CSV -> Upload Row.
     * This lets us accept different column names for a report and map the values to these.
     *
     * @var array
     */
    protected $_columnMapping = array(
//        'rmsModelId'             => 'rmsModelId',
        'assetid'                => 'assetId',
        'ismanaged'              => 'isManaged',
        'monitordate'            => 'monitorStartDate',
        'monitordate2'            => 'monitorEndDate',
//        'adoptionDate'           => 'adoptionDate',
        'discoverydate'          => 'discoveryDate',
        'launchdate'             => 'launchDate',
        'dutycycle'              => 'dutyCycle',
        'ipaddress'              => 'ipAddress',
        'iscolor'                => 'isColor',
//        'isCopier'               => 'isCopier',
//        'isFax'                  => 'isFax',
        'manufacturer'           => 'manufacturer',
        'name'              => 'modelName',
        'ppmblack'               => 'ppmBlack',
        'ppmcolor'               => 'ppmColor',
        'serialnumber'           => 'serialNumber',
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
    );


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
            $monitorDates                = explode(' - ', $csvData['monitorStartDate']);
            $csvData['monitorStartDate'] = $monitorDates[0];
            $csvData['monitorEndDate']   = $monitorDates[1];
        }

        return parent::processData($csvData);
    }
}