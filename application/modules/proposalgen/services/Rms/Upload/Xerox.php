<?php
class Proposalgen_Service_Rms_Upload_Xerox extends Proposalgen_Service_Rms_Upload_Abstract
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
        'tag'           => 'assetId',
        'meter date 1'  => 'monitorStartDate',
        'meter date 2'  => 'monitorEndDate',
        'ip address'    => 'ipAddress',
        'make'          => 'manufacturer',
        'model'         => 'modelName',
        'serial number' => 'serialNumber',
        'bw meter 1'    => 'startMeterBlack',
        'bw meter 2'    => 'endMeterBlack',
        'color meter 1' => 'startMeterColor',
        'color meter 2' => 'endMeterColor',
        'total meter 1' => 'startMeterLife',
        'total meter 2' => 'endMeterLife'
    );
}