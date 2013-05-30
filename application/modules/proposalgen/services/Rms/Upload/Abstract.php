<?php
/**
 * Class Proposalgen_Service_Rms_Upload_Abstract
 */
abstract class Proposalgen_Service_Rms_Upload_Abstract
{
    /**
     * Input filter
     *
     * @var Zend_Filter_Input
     */
    protected $_inputFilter;

    /**
     * The number of lines to trim off the top of the csv
     *
     * @var int
     */
    protected $_linesToTrim = 0;

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
        'rmsModelId'             => false,
        'assetId'                => false,
        'monitorStartDate'       => true,
        'monitorEndDate'         => true,
        'adoptionDate'           => false,
        'cost'                   => false,
        'discoveryDate'          => false,
        'launchDate'             => false,
        'dutyCycle'              => false,
        'ipAddress'              => false,
        'isColor'                => false,
        'isCopier'               => false,
        'isScanner'              => false,
        'isFax'                  => false,
        'manufacturer'           => true,
        'modelName'              => true,
        'ppmBlack'               => false,
        'ppmColor'               => false,
        'serialNumber'           => false,
        'wattsOperating'         => false,
        'wattsIdle'              => false,
        'blackTonerSku'          => false,
        'blackTonerYield'        => false,
        'blackTonerCost'         => false,
        'cyanTonerSku'           => false,
        'cyanTonerYield'         => false,
        'cyanTonerCost'          => false,
        'magentaTonerSku'        => false,
        'magentaTonerYield'      => false,
        'magentaTonerCost'       => false,
        'yellowTonerSku'         => false,
        'yellowTonerYield'       => false,
        'yellowTonerCost'        => false,
        'threeColorTonerSku'     => false,
        'threeColorTonerYield'   => false,
        'threeColorTonerCost'    => false,
        'fourColorTonerSku'      => false,
        'fourColorTonerYield'    => false,
        'fourColorTonerCost'     => false,
        'startMeterBlack'        => true,
        'endMeterBlack'          => true,
        'startMeterColor'        => false,
        'endMeterColor'          => false,
        'startMeterLife'         => false,
        'endMeterLife'           => false,
        'startMeterPrintBlack'   => false,
        'endMeterPrintBlack'     => false,
        'startMeterPrintColor'   => false,
        'endMeterPrintColor'     => false,
        'startMeterCopyBlack'    => false,
        'endMeterCopyBlack'      => false,
        'startMeterCopyColor'    => false,
        'endMeterCopyColor'      => false,
        'startMeterScan'         => false,
        'endMeterScan'           => false,
        'startMeterFax'          => false,
        'endMeterFax'            => false,
        'reportsTonerLevels'     => false,
        'tonerLevelBlack'        => false,
        'tonerLevelCyan'         => false,
        'tonerLevelMagenta'      => false,
        'tonerLevelYellow'       => false,
        'pageCoverageMonochrome' => false,
        'pageCoverageCyan'       => false,
        'pageCoverageMagenta'    => false,
        'pageCoverageYellow'     => false,
        'isManaged'            => false,
        'rmsDeviceId'          => false,
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
     * @var Proposalgen_Service_Rms_Upload_Line[]
     */
    public $csvLines = array();

    /**
     * Valid csv lines
     *
     * @var Proposalgen_Service_Rms_Upload_Line[]
     */
    public $validCsvLines = array();

    /**
     * Invalid csv lines
     *
     * @var Proposalgen_Service_Rms_Upload_Line[]
     */
    public $invalidCsvLines = array();

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
            'rmsModelId'       => array(
                'StringTrim',
                'Digits'
            ),
            'isManaged'        => array(
                'StringTrim',
                array(
                    'filter'  => 'Boolean',
                    'options' => array(
                        'type' => Zend_Filter_Boolean::ALL
                    ),
                ),
                'Int',
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

    /**
     * Processes the csv file and inserts the data into the database
     *
     * @param     $filename
     * @param int $maxLineCount
     *
     * @return bool|string
     */
    public function processCsvFile ($filename, $maxLineCount = 1000)
    {
        if (file_exists($filename))
        {
            // Reset arrays
            $this->csvLines        = array();
            $this->validCsvLines   = array();
            $this->invalidCsvLines = array();
            $this->_csvHeaders     = array();

            $fileLines = file($filename, FILE_IGNORE_NEW_LINES);
            $lineCount = count($fileLines) - 1 - $this->_linesToTrim;

            if ($lineCount < 1)
            {
                return "File was empty";
            }

            if ($lineCount > $maxLineCount)
            {
                return "The uploaded file contains {$lineCount} printers. The maximum number of printers supported in a single report is {$maxLineCount}. Please modify your file and try again.";
            }

            /*
             * Trim the top of the file
             */
            if ($this->_linesToTrim > 0)
            {
                $fileLines = array_splice($fileLines, $this->_linesToTrim);
            }

            /*
             * Pop off the first line since they are headers
             */
            $this->_csvHeaders = array();
            foreach (str_getcsv(array_shift($fileLines)) as $header)
            {
                $lowerCaseHeader = strtolower($header);
                /*
                 * We'll build the array of headers ourselves and ensure that they are normalized.
                 * We could map column names here for different reports
                */
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

            /*
             * Now we validate our lines and assign them in the areas we deem necessary
             */
            if ($this->validateHeaders($this->_fieldsPresent))
            {
                $csvLineNumber = $this->_linesToTrim + 2;
                // Get normalized data
                foreach ($fileLines as $line)
                {
                    // Turn the line into an assoc array for us
                    $csvLine = array_combine($this->_mappedHeaders, str_getcsv($line));

                    // Only validate lines that were combined properly (meaning they weren't empty and had the same column count as the headers)
                    if ($csvLine !== false)
                    {
                        $csvLine["csvLineNumber"] = $csvLineNumber;

                        /*
                         * Process our data and massage it into our upload line
                         */
                        $uploadLine = new Proposalgen_Service_Rms_Upload_Line($this->processData($csvLine));

                        $validationMessage = $uploadLine->isValid($this->_incomingDateFormat);

                        if ($validationMessage === true)
                        {
                            $this->validCsvLines [] = $uploadLine;
                            $this->csvLines []      = $uploadLine;
                        }
                        else
                        {
                            $uploadLine->validationErrorMessage = $validationMessage;
                            $this->csvLines []                  = $uploadLine;
                            $this->invalidCsvLines []           = $uploadLine;
                        }
                    }
                    $csvLineNumber++;
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
        foreach ($this->_requiredHeaders as $fieldName => $isRequired)
        {
            // If the header is required and doesn't exist in the array, return false
            if ($isRequired && !in_array($fieldName, $csvHeaders))
            {
                return false;
            }
        }

        return true;
    }


    /**
     * Filters the data into desired formats
     *
     * @param array $csvData
     *
     * @return array
     */
    public function processData ($csvData)
    {
        $this->_inputFilter->setData($csvData);

        foreach ($this->_fieldsPresent as $fieldName)
        {
            $value                = $this->_inputFilter->getEscaped($fieldName);
            $csvData [$fieldName] = (strlen($value) < 1 || strcasecmp($value, 'null') === 0) ? null : $value;
        }

        return $csvData;
    }
}