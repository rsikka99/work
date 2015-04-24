<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMapper;
use Zend_Filter_Boolean;
use Zend_Filter_Input;
use Zend_Filter_StringTrim;

/**
 * Class AbstractRmsUploadService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload
 */
abstract class AbstractRmsUploadService
{
    /**
     * The csv delimiter
     *
     * @var string
     */
    protected $csv_delimiter = null;

    /**
     * The csv value enclosure
     *
     * @var string
     */
    protected $csv_enclosure = null;

    /**
     * The csv escape format
     *
     * @var string
     */
    protected $csv_escape = null;

    /**
     * Input filter
     *
     * @var Zend_Filter_Input
     */
    protected $_inputFilter;

    /**
     * The number of lines to trim off the top of the CSV
     *
     * @var int
     */
    protected $_linesToTrim = 0;

    /**
     * How to read the date coming in from the CSV
     *
     * @var string
     */
    protected $_incomingDateFormat = "MM/dd/yyyy HH:ii:ss";

    /**
     * The fields that must be present within the CSV format
     *
     * @var array
     */
    protected $_requiredHeaders = [
        'rmsVendorName'          => false,
        'rmsReportVersion'       => false,
        'rmsModelId'             => false,
        'assetId'                => false,
        'monitorStartDate'       => true,
        'monitorEndDate'         => true,
        'adoptionDate'           => false,
        'cost'                   => false,
        'discoveryDate'          => false,
        'launchDate'             => false,
        'ipAddress'              => false,
        'isColor'                => false,
        'isCopier'               => false,
        'isFax'                  => false,
        'isA3'                   => false,
        'isDuplex'               => false,
        'manufacturer'           => true,
        'rawDeviceName'          => false,
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
        'startMeterPrintA3Black' => false,
        'endMeterPrintA3Black'   => false,
        'startMeterPrintA3Color' => false,
        'endMeterPrintA3Color'   => false,
        'reportsTonerLevels'     => false,
        'tonerLevelBlack'        => false,
        'tonerLevelCyan'         => false,
        'tonerLevelMagenta'      => false,
        'tonerLevelYellow'       => false,
        'pageCoverageMonochrome' => false,
        'pageCoverageColor'      => false,
        'pageCoverageCyan'       => false,
        'pageCoverageMagenta'    => false,
        'pageCoverageYellow'     => false,
        'isManaged'              => false,
        'managementProgram'      => false,
        'rmsDeviceId'            => false,
        'location'               => false,
    ];

    /**
     * A list of fields that are present in the CSV file
     *
     * @var array
     */
    protected $_fieldsPresent = [];

    /**
     * Column mapping for CSV -> Upload Row.
     * This lets us accept different column names for a report and map the values to these.
     *
     * @var array
     */
    protected $_columnMapping = [];

    /**
     * Valid CSV lines
     *
     * @var UploadLineModel[]
     */
    public $csvLines = [];

    /**
     * Valid CSV lines
     *
     * @var UploadLineModel[]
     */
    public $validCsvLines = [];

    /**
     * Invalid CSV lines
     *
     * @var UploadLineModel[]
     */
    public $invalidCsvLines = [];

    /**
     * Raw CSV header
     *
     * @var array
     */
    protected $_csvHeaders = [];

    /**
     * Mapped CSV header
     *
     * @var array
     */
    protected $_mappedHeaders = [];

    /**
     * The constructor for this object.
     * Sets up filters and validators for our data.
     */
    public function __construct ()
    {
        $filters            = [
            'rmsModelId'     => [
                'StringTrim',
            ],
            'isManaged'      => [
                'StringTrim',
                [
                    'filter'  => 'Callback',
                    'options' => [
                        'callback' => function ($value)
                        {
                            return (strcasecmp($value, "Managed") === 0);
                        },
                    ],
                ],
                'Int',
            ],
            'isColor'        => [
                'StringTrim',
                [
                    'filter'  => 'Boolean',
                    'options' => [
                        'type' => Zend_Filter_Boolean::ALL
                    ]
                ],
                'Int'
            ],
            'isCopier'       => [
                'StringTrim',
                [
                    'filter'  => 'Boolean',
                    'options' => [
                        'type' => Zend_Filter_Boolean::ALL
                    ]
                ],
                'Int'
            ],
            'isFax'          => [
                'StringTrim',
                [
                    'filter'  => 'Boolean',
                    'options' => [
                        'type' => Zend_Filter_Boolean::ALL
                    ]
                ],
                'Int'
            ],
            'isA3'           => [
                'StringTrim',
                [
                    'filter'  => 'Boolean',
                    'options' => [
                        'type' => Zend_Filter_Boolean::ALL
                    ]
                ],
                'Int'
            ],
            'isDuplex'       => [
                'StringTrim',
                [
                    'filter'  => 'Boolean',
                    'options' => [
                        'type' => Zend_Filter_Boolean::ALL
                    ]
                ],
                'Int'
            ],
            'ppmBlack'       => [
                'StringTrim',
                'Int'
            ],
            'ppmColor'       => [
                'StringTrim',
                'Int'
            ],
            'wattsOperating' => [
                'StringTrim',
                'Int'
            ],
            'wattsIdle'      => [
                'StringTrim',
                'Int'
            ]
        ];
        $this->_inputFilter = new Zend_Filter_Input($filters, array());

        // If we haven't set a filter for the data, use this one
        $this->_inputFilter->setDefaultEscapeFilter(new Zend_Filter_StringTrim());
    }

    /**
     * Processes the CSV file and inserts the data into the database
     *
     * @param     $filename
     * @param int $maxLineCount
     *
     * @return bool|string
     */
    public function processCsvFile ($filename, $maxLineCount = DeviceInstanceMapper::MAX_DEVICE_INSTANCES)
    {
        if (file_exists($filename))
        {
            // Reset arrays
            $this->csvLines        = [];
            $this->validCsvLines   = [];
            $this->invalidCsvLines = [];
            $this->_csvHeaders     = [];

            if (preg_match('#\.(xls|xlsx)$#',$filename)) {
                $obj = \PHPExcel_IOFactory::load($filename);
                $sheet = $obj->getSheet(0);
                $maxCol = $sheet->getHighestDataColumn();
                $maxRow = $sheet->getHighestDataRow();
                for($row = 1; $row <= $maxRow; ++$row) {
                    // Convert the row to an array...
                    $cellsArray = $sheet->rangeToArray('A'.$row.':'.$maxCol.$row,'', true);
                    // ... and write to the file
                    $writeDelimiter = false;
                    // Build the line
                    $line = '';
                    foreach ($cellsArray[0] as $element) {
                        if (is_bool($element)) $element = $element?'TRUE':'FALSE';
                        // Escape enclosures
                        $element = str_replace('"', '"' . '"', $element);

                        // Add delimiter
                        if ($writeDelimiter) {
                            $line .= ',';
                        } else {
                            $writeDelimiter = true;
                        }

                        // Add enclosed string
                        $line .= '"' . $element . '"';
                    }
                    $fileLines[] = $line;
                }
            } else {
                $fileLines = file($filename, FILE_IGNORE_NEW_LINES);
            }
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
            $this->_csvHeaders = [];

            $headerLine = str_getcsv(array_shift($fileLines), $this->csv_delimiter, $this->csv_delimiter, $this->csv_escape);
            foreach ($headerLine as $header)
            {
                $lowerCaseHeader = strtolower(trim($header));
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
            $validHeadersMessage = $this->validateHeaders($this->_fieldsPresent);
            if ($validHeadersMessage === true)
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
                        $uploadLine = new UploadLineModel($this->processData($csvLine));

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
                return $validHeadersMessage;
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
        $requiredHeadersMissing = [];
        // Loop through our standard headers
        foreach ($this->_requiredHeaders as $fieldName => $isRequired)
        {
            // If the header is required and doesn't exist in the array, return false
            if ($isRequired && !in_array($fieldName, $csvHeaders))
            {
                $requiredHeadersMissing[] = $fieldName;
            }
        }

        if (count($requiredHeadersMissing) > 0)
        {
            $vendorHeadings = [];
            foreach ($requiredHeadersMissing as $header)
            {
                $vendorHeadings[] = array_search($header, $this->_columnMapping);
            }

            return "File is missing required these headers: [" . implode(',', $vendorHeadings) . "]. Are you sure you selected the right RMS Vendor?";
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