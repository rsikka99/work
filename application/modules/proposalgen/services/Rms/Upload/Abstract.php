<?php

abstract class Proposalgen_Service_Rms_Upload_Abstract
{
    /**
     * Input filter
     *
     * @var Zend_Filter_Input
     */
    protected $_inputFilter;

    /**
     * The format needed to change a Zend_Date object into a MySQL compatible time
     *
     * @var string
     */
    const ZEND_TO_MYSQL_DATEFORMAT = "yyyy-MM-dd HH:mm:ss";

    /**
     * How to read the date coming in from the csv
     *
     * @var string
     */
    protected $_incomingDateFormat = "MM/dd/yyyy HH:ii:ss";

    /**
     * The fields that must be present within the csv format
     *
     * @var array
     */
    protected $_requiredHeaders = array(
        'rmsModelId'                     => true,
        'monitorStartDate'               => true,
        'monitorEndDate'                 => true,
        'discoveryDate'                  => true,
        'adoptionDate'                   => true,
        'dutyCycle'                      => true,
        'introductionDate'               => true,
        'ipAddress'                      => true,
        'isColor'                        => true,
        'isCopier'                       => true,
        'isScanner'                      => true,
        'isFax'                          => true,
        'manufacturer'                   => true,
        'modelName'                      => true,
        'ppmBlack'                       => true,
        'ppmColor'                       => true,
        'serialNumber'                   => true,
        'wattsOperating'                 => true,
        'wattsIdle'                      => true,
        'compatibleTonerBlackSku'        => false,
        'compatibleTonerBlackYield'      => false,
        'compatibleTonerBlackCost'       => false,
        'compatibleTonerCyanSku'         => false,
        'compatibleTonerCyanYield'       => false,
        'compatibleTonerCyanCost'        => false,
        'compatibleTonerMagentaSku'      => false,
        'compatibleTonerMagentaYield'    => false,
        'compatibleTonerMagentaCost'     => false,
        'compatibleTonerYellowSku'       => false,
        'compatibleTonerYellowYield'     => false,
        'compatibleTonerYellowCost'      => false,
        'compatibleTonerThreeColorSku'   => false,
        'compatibleTonerThreeColorYield' => false,
        'compatibleTonerThreeColorCost'  => false,
        'compatibleTonerFourColorSku'    => false,
        'compatibleTonerFourColorYield'  => false,
        'compatibleTonerFourColorCost'   => false,
        'oemTonerBlackSku'               => true,
        'oemTonerBlackYield'             => true,
        'oemTonerBlackCost'              => true,
        'oemTonerCyanSku'                => true,
        'oemTonerCyanYield'              => true,
        'oemTonerCyanCost'               => true,
        'oemTonerMagentaSku'             => true,
        'oemTonerMagentaYield'           => true,
        'oemTonerMagentaCost'            => true,
        'oemTonerYellowSku'              => true,
        'oemTonerYellowYield'            => true,
        'oemTonerYellowCost'             => true,
        'oemTonerThreeColorSku'          => false,
        'oemTonerThreeColorYield'        => false,
        'oemTonerThreeColorCost'         => false,
        'oemTonerFourColorSku'           => false,
        'oemTonerFourColorYield'         => false,
        'oemTonerFourColorCost'          => false,
        'startMeterBlack'                => true,
        'endMeterBlack'                  => true,
        'startMeterColor'                => true,
        'endMeterColor'                  => true,
        'startMeterLife'                 => true,
        'endMeterLife'                   => true,
        'startMeterPrintBlack'           => true,
        'endMeterPrintBlack'             => true,
        'startMeterPrintColor'           => true,
        'endMeterPrintColor'             => true,
        'startMeterCopyBlack'            => true,
        'endMeterCopyBlack'              => true,
        'startMeterCopyColor'            => true,
        'endMeterCopyColor'              => true,
        'startMeterScan'                 => true,
        'endMeterScan'                   => true,
        'startMeterFax'                  => true,
        'endMeterFax'                    => true,
        'tonerLevelBlack'                => true,
        'tonerLevelCyan'                 => true,
        'tonerLevelMagenta'              => true,
        'tonerLevelYellow'               => true
    );

    /**
     * A list of fields that are present in the csv file
     *
     * @var array
     */
    protected $_fieldsPresent = array();

    /**
     * Column mapping for CSV -> Upload Row.
     * This lets us accept different column names for a report and map the values to these.
     *
     * @var array
     */
    protected $_columnMapping = array();

    /**
     * Valid csv lines
     *
     * @var array
     */
    protected $_csvLines = array();

    /**
     * Valid csv lines
     *
     * @var array
     */
    protected $_validCsvLines = array();

    /**
     * Invalid csv lines
     *
     * @var array
     */
    protected $_invalidCsvLines = array();

    /**
     * Raw csv header
     *
     * @var array
     */
    protected $_csvHeaders = array();

    /**
     * Mapped csv header
     *
     * @var array
     */
    protected $_mappedHeaders = array();

    /**
     * The constructor for this object.
     * Sets up filters and validators for our data.
     */
    public function __construct ()
    {
        $filters            = array(
            'printermodelid'   => array(
                'StringTrim',
                'Digits'
            ),
            'isColor'          => array(
                'StringTrim',
                array(
                    'filter'  => 'Boolean',
                    'options' => array(
                        'type' => Zend_Filter_Boolean::ALL
                    )
                ),
                'Int'
            ),
            'isCopier'         => array(
                'StringTrim',
                array(
                    'filter'  => 'Boolean',
                    'options' => array(
                        'type' => Zend_Filter_Boolean::ALL
                    )
                ),
                'Int'
            ),
            'isScanner'        => array(
                'StringTrim',
                array(
                    'filter'  => 'Boolean',
                    'options' => array(
                        'type' => Zend_Filter_Boolean::ALL
                    )
                ),
                'Int'
            ),
            'isFax'            => array(
                'StringTrim',
                array(
                    'filter'  => 'Boolean',
                    'options' => array(
                        'type' => Zend_Filter_Boolean::ALL
                    )
                ),
                'Int'
            ),
            'ppm_black'        => array(
                'StringTrim',
                'Int'
            ),
            'ppm_color'        => array(
                'StringTrim',
                'Int'
            ),
            'duty_cycle'       => array(
                'StringTrim',
                'Int'
            ),
            'wattspowernormal' => array(
                'StringTrim',
                'Int'
            ),
            'wattspoweridle'   => array(
                'StringTrim',
                'Int'
            )
        );
        $this->_inputFilter = new Zend_Filter_Input($filters, array());

        // If we haven't set a filter for the data, use this one
        $this->_inputFilter->setDefaultEscapeFilter(new Zend_Filter_StringTrim());
    }

    public function processCsvFile ($filename, $maxLineCount = 1000)
    {
        if (file_exists($filename))
        {
            // Reset arrays
            $this->_csvLines        = array();
            $this->_validCsvLines   = array();
            $this->_invalidCsvLines = array();
            $this->_csvHeaders      = array();

            $fileLines = file($filename, FILE_IGNORE_NEW_LINES);
            $lineCount = count($fileLines) - 1;

            if ($lineCount < 1)
            {
                return "File was empty";
            }

            if ($lineCount > $maxLineCount)
            {
                return "The uploaded file contains {$lineCount} printers. The maximum number of printers supported in a single report is {$maxLineCount}. Please modify your file and try again.";
            }

            //Pop off the first line since they are headers
            $this->_csvHeaders = array();
            foreach (str_getcsv(array_shift($fileLines)) as $header)
            {
                $lowerCaseHeader = strtolower($header);
                // We'll build the array of headers ourselves and ensure that they are normalized.
                // We could map column names here for different reports
                $this->_csvHeaders [] = $lowerCaseHeader;

                /*
                 * The idea here is to take an array of headers and turn them into our normalized headers. They need to
                 * be in the right order so that the values get mapped accordingly. This takes a header such as
                 * 'colorprintspeed' and turns it into 'ppm_color' so that it's normalized.
                 */
                if (array_key_exists($lowerCaseHeader, $this->_columnMapping))
                {
                    /*
                     * Add to the fields present array so that we know that the column actually exists as not all
                     * columns are required to be present.
                     */
                    $this->_fieldsPresent [] = $this->_columnMapping [$lowerCaseHeader];
                    $this->_mappedHeaders [] = $this->_columnMapping [$lowerCaseHeader];
                }
                else
                {
                    $this->_mappedHeaders [] = $lowerCaseHeader;
                }
            }

            // Make sure our headers are valid
            if ($this->validateHeaders($this->_fieldsPresent))
            {
                // Get normalized data
                foreach ($fileLines as $line)
                {
                    // Turn the line into an assoc array for us
                    $csvLine = array_combine($this->_mappedHeaders, str_getcsv($line));

                    // Only validate lines that were combined properly (meaning they werent empty and had the same column count as the headers)
                    if ($csvLine !== false)
                    {
                        $result = $this->validateLine($csvLine);

                        $uploadRow = new Proposalgen_Model_Rms_Upload_Row($csvLine);
                        // Line was valid
                        if ($result === true)
                        {
                            $this->_validCsvLines [] = $uploadRow;
                            $this->_csvLines []      = $uploadRow;
                        }
                        else
                        {
                            $uploadRow->setValidationErrorMessage($result);
                            $this->_csvLines []        = $uploadRow;
                            $this->_invalidCsvLines [] = $uploadRow;
                        }
                    }
                }
            }
            else
            {
                return "Incompatible file, check vendor selection.";
            }
        }

        return true;
    }

    /**
     * Validates that all required headers exist.
     *
     * @param array $csvHeaders
     *
     * @return boolean True if all required headers exist
     */
    public function validateHeaders ($csvHeaders)
    {
        // Loop through our standard headers
        foreach ($this->_requiredHeaders as $fieldname => $isRequired)
        {
            // If the header is required and doesn't exist in the array, return false
            if ($isRequired && !in_array($fieldname, $csvHeaders))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Validates a csv line
     *
     * @param array $csvLine
     *
     * @return boolean Returns true when line is valid, or returns an error message.
     */
    public function validateLine (&$csvLine)
    {
        // Settings
        $requiredDaysOfInformation     = 4;
        $maximumAgeInYears             = 5;
        $minimumDeviceIntroductionDate = new Zend_Date(strtotime("-{$maximumAgeInYears} years"));

        // Process our line of data
        $csvLine = $this->processData($csvLine);

        // Make sure required data is there

        // Turn all the dates into Zend_Date objects
        $monitorStartDate = (empty($csvLine ['monitorStartDate'])) ? null : new Zend_Date($csvLine ['monitorStartDate'], $this->_incomingDateFormat);
        $monitorEndDate   = (empty($csvLine ['monitorEndDate'])) ? null : new Zend_Date($csvLine ['monitorEndDate'], $this->_incomingDateFormat);
        $discoveryDate    = (empty($csvLine ['discoveryDate'])) ? null : new Zend_Date($csvLine ['discoveryDate'], $this->_incomingDateFormat);
        $introductionDate = (empty($csvLine ['introductionDate'])) ? null : new Zend_Date($csvLine ['introductionDate'], $this->_incomingDateFormat);
        $adoptionDate     = (empty($csvLine ['adoptionDate'])) ? null : new Zend_Date($csvLine ['adoptionDate'], $this->_incomingDateFormat);

        $outputFormat = "yyyy-MM-dd HH:mm:ss";

        // Convert all the dates back to mysql dates
        $csvLine ['monitorStartDate'] = ($monitorStartDate === null) ? null : $monitorStartDate->toString($outputFormat);
        $csvLine ['monitorEndDate']   = ($monitorEndDate === null) ? null : $monitorEndDate->toString($outputFormat);
        $csvLine ['discoveryDate']    = ($discoveryDate === null) ? null : $discoveryDate->toString($outputFormat);
        $csvLine ['introductionDate'] = ($introductionDate === null) ? null : $introductionDate->toString($outputFormat);
        $csvLine ['adoptionDate']     = ($adoptionDate === null) ? null : $adoptionDate->toString($outputFormat);

        // Validate that certain fields are present
        if (empty($csvLine ['rmsModelId']))
        {
            return "Device does not have a model id";
        }
        if (empty($csvLine ['modelName']))
        {
            return "Device does not have a modelname";
        }
        if (empty($csvLine ['manufacturer']))
        {
            return "Device does not have a manufacturer";
        }
        if (empty($csvLine ['monitorStartDate']))
        {
            return "Device does not have a start date";
        }
        if (empty($csvLine ['monitorEndDate']))
        {
            return "Device does not have a end date";
        }

        /*
         * Device name sanitization
         */
        $manufacturer = $csvLine ['manufacturer'];
        $devicename   = $csvLine ['modelName'];

        // Remove the manufacturer name from the model name
        $devicename = str_ireplace($manufacturer . ' ', '', $devicename);

        // If the manufacturer is Hewlet-packard we also need to strip away HP
        if (strcasecmp($manufacturer, 'hewlett-packard') === 0)
        {
            $devicename = str_ireplace('hp ', '', $devicename);
        }

        // Correct the device name
        $devicename = ucwords(trim($devicename));

        // Convert hp into it's full name
        if ($manufacturer == "hp")
        {
            $manufacturer = "hewlett-packard";
        }

        $csvLine ['modelName']    = $devicename;
        $csvLine ['manufacturer'] = ucwords($manufacturer);

        // Check the meter columns
        $checkMetersValidation = $this->validateMetersForLine($csvLine);
        if ($checkMetersValidation !== true)
        {
            return $checkMetersValidation;
        }


        // If the discovery date is after the start date, use the discovery date
        if ($discoveryDate !== null && $discoveryDate->compare($monitorStartDate) === 1)
        {
            // Use Discovery Date
            $dateMonitoringStarted = new DateTime("@" . $discoveryDate->toString(Zend_Date::TIMESTAMP));
        }
        else
        {
            // Use monitor start date
            $dateMonitoringStarted = new DateTime("@" . $monitorStartDate->toString(Zend_Date::TIMESTAMP));
        }
        $dateMonitoringEnded = new DateTime("@" . $monitorEndDate->toString(Zend_Date::TIMESTAMP));

        // Figure out how long we've been monitoring this device
        $monitoringInterval = $dateMonitoringStarted->diff($dateMonitoringEnded);

        // Monitoring should not be inverted (means start date occured after end date)
        if ($monitoringInterval->invert || $monitoringInterval->days < $requiredDaysOfInformation)
        {
            return "Device was monitored for less than {$requiredDaysOfInformation} days.";
        }

        // Check to make sure our start date is not more than 5 years old
        if ($minimumDeviceIntroductionDate->compare($monitorStartDate) == 1)
        {
            return "Start date is greater than {$maximumAgeInYears} years old.";
        }

        return true;
    }

    /**
     * Validates the meters for a csv line
     *
     * @param array $csvLine
     *
     * @return boolean True if the meters are ok or returns an error message
     */
    public function validateMetersForLine ($csvLine)
    {
        /*
         * The start meter of any meter should never be greater than the end meter.
         */

        // If we are missing a black meter and are missing a color/life meter then we cannot proceed
        if (empty($csvLine ['startMeterBlack']) && (empty($csvLine ['startMeterLife']) || empty($csvLine ['startMeterColor'])))
        {
            return "Invalid black meter";
        }

        // Black meter
        if (($csvLine ['startMeterBlack'] > $csvLine ['endMeterBlack']) || ($csvLine ['startMeterBlack'] < 0 || $csvLine ['endMeterBlack'] < 0))
        {
            return "Invalid black meter";
        }

        // Life meter
        if (($csvLine ['startMeterLife'] > $csvLine ['endMeterLife']) || ($csvLine ['startMeterLife'] < 0 || $csvLine ['endMeterLife'] < 0))
        {
            return "Invalid life meter";
        }

        // Color meter
        if (($csvLine ['startMeterColor'] > $csvLine ['endMeterColor']) || ($csvLine ['startMeterColor'] < 0 || $csvLine ['endMeterColor'] < 0))
        {
            return "Invalid color meter";
        }

        // Print Black meter
        if (($csvLine ['startMeterPrintBlack'] > $csvLine ['endMeterPrintBlack']) || ($csvLine ['startMeterPrintBlack'] < 0 || $csvLine ['endMeterPrintBlack'] < 0))
        {
            return "Invalid print black meter";
        }

        // Print Color  meter
        if (($csvLine ['startMeterPrintColor'] > $csvLine ['endMeterPrintColor']) || ($csvLine ['startMeterPrintColor'] < 0 || $csvLine ['endMeterPrintColor'] < 0))
        {
            return "Invalid print color meter";
        }

        // Copy Black meter
        if (($csvLine ['startMeterCopyBlack'] > $csvLine ['endMeterCopyBlack']) || ($csvLine ['startMeterCopyBlack'] < 0 || $csvLine ['endMeterCopyBlack'] < 0))
        {
            return "Invalid copy black meter";
        }

        // Copy Color meter
        if (($csvLine ['startMeterCopyColor'] > $csvLine ['endMeterCopyColor']) || ($csvLine ['startMeterCopyColor'] < 0 || $csvLine ['endMeterCopyColor'] < 0))
        {
            return "Invalid copy color meter";
        }

        // Scan meter
        if (($csvLine ['startMeterScan'] > $csvLine ['endMeterScan']) || ($csvLine ['startMeterScan'] < 0 || $csvLine ['endMeterScan'] < 0))
        {
            return "Invalid scan meter";
        }

        // Fax meter
        if (($csvLine ['startMeterFax'] > $csvLine ['endMeterFax']) || ($csvLine ['startMeterFax'] < 0 || $csvLine ['endMeterFax'] < 0))
        {
            return "Invalid fax meter";
        }

        return true;
    }

    /**
     * Filters the data into desired formats
     *
     * @param array $csvData
     */
    public function processData ($csvData)
    {
        $this->_inputFilter->setData($csvData);

        foreach ($this->_fieldsPresent as $fieldname)
        {
            $value                = $this->_inputFilter->getEscaped($fieldname);
            $csvData [$fieldname] = (strlen($value) < 1 || strcasecmp($value, 'null') === 0) ? null : $value;
        }

        return $csvData;
    }

    /**
     * Parses a date into MySQL date format
     *
     * @param string $dateString
     *            The date to parse
     *
     * @return string The MySQL date
     */
    public function normalizeDate ($dateString)
    {
        $date       = new Zend_Date($dateString, $this->_incomingDateFormat);
        $dateString = $date->toString(self::ZEND_TO_MYSQL_DATEFORMAT);

        return $dateString;
    }

    /**
     * Getter for $_validCsvLines
     *
     * @return array
     */
    public function getValidCsvLines ()
    {
        return $this->_validCsvLines;
    }

    /**
     * Setter for $_validCsvLines
     *
     * @param array $_validCsvLines
     *                 The new value
     *
     * @return \Proposalgen_Service_Rms_Upload_Abstract
     */
    public function setValidCsvLines ($_validCsvLines)
    {
        $this->_validCsvLines = $_validCsvLines;

        return $this;
    }

    /**
     * Getter for $_csvLines
     *
     * @return array
     */
    public function getCsvLines ()
    {
        return $this->_csvLines;
    }

    /**
     * Setter for $_csvLines
     *
     * @param array $_csvLines
     *                 The new value
     *
     * @return \Proposalgen_Service_Rms_Upload_Abstract
     */
    public function setCsvLines ($_csvLines)
    {
        $this->_csvLines = $_csvLines;

        return $this;
    }

    /**
     * Getter for $_invalidCsvLines
     *
     * @return array
     */
    public function getInvalidCsvLines ()
    {
        return $this->_invalidCsvLines;
    }

    /**
     * Setter for $_invalidCsvLines
     *
     * @param array $_invalidCsvLines
     *                 The new value
     *
     * @return \Proposalgen_Service_Rms_Upload_Abstract
     */
    public function setInvalidCsvLines ($_invalidCsvLines)
    {
        $this->_invalidCsvLines = $_invalidCsvLines;

        return $this;
    }
}

