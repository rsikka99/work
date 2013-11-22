<?php
/**
 * Class Proposalgen_Service_Rms_Upload_Xerox
 */
class Proposalgen_Service_Rms_Upload_Xerox extends Proposalgen_Service_Rms_Upload_Abstract
{
    /**
     * How to read the date coming in from the csv
     *
     * @var string
     */
    protected $_incomingDateFormat = "F j Y";

    /**
     * The number of lines to trim off the top of the csv
     *
     * @var int
     */
    protected $_linesToTrim = 4;

    /**
     * Column mapping for CSV -> Upload Row.
     * This lets us accept different column names for a report and map the values to these.
     *
     * @var array
     */
    protected $_columnMapping = array(
        'tag'           => 'assetId',
        'meter date 2'  => 'monitorStartDate',
        'meter date 1'  => 'monitorEndDate',
        'ip address'    => 'ipAddress',
        'make'          => 'manufacturer',
        'model'         => 'modelName',
        'serial number' => 'serialNumber',
        'bw meter 2'    => 'startMeterBlack',
        'bw meter 1'    => 'endMeterBlack',
        'color meter 2' => 'startMeterColor',
        'color meter 1' => 'endMeterColor',
        'total meter 2' => 'startMeterLife',
        'total meter 1' => 'endMeterLife'
    );
}