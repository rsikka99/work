<?php

namespace MPSToolbox\Legacy\Modules\Admin\Services;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload\UploadLineModel;
use Zend_Filter_Input;

/**
 * Class OnboardingAbstractService
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Services
 */
class OnboardingAbstractService
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
     * A list of fields that are present in the CSV file
     *
     * @var array
     */
    protected $_fieldsPresent = [];

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
     * Column mapping for CSV -> Upload Row.
     * This lets us accept different column names for a report and map the values to these.
     *
     * @var array
     */
    protected $_columnMapping = [];

    /**
     * The fields that must be present within the CSV format
     *
     * @var array
     */
    protected $_requiredHeaders = [];


    public function processFile ($filename, $dealerId)
    {
        $csvLines = $this->getCsvContents($filename);

        return $csvLines;
    }

    /**
     * Gets the contents of a csv file
     *
     * @param string $filename
     *
     * @return array|bool|string
     */
    public function getCsvContents ($filename)
    {
        if (file_exists($filename))
        {
            // Reset arrays
            $this->csvLines        = [];
            $this->validCsvLines   = [];
            $this->invalidCsvLines = [];
            $this->_csvHeaders     = [];

            $fileLines = file($filename, FILE_IGNORE_NEW_LINES);
            $lineCount = count($fileLines) - 1 - $this->_linesToTrim;

            if ($lineCount < 1)
            {
                return "File was empty";
            }

            /*
             * Trim the top of the file
             */
            if ($this->_linesToTrim > 0)
            {
                $fileLines = array_splice($fileLines, $this->_linesToTrim);
            }

            /*
             * Pop off the first line since they are headers and map them accordingly
             */
            $this->_csvHeaders = [];


            $lowercaseColumnMapping = [];
            foreach ($this->_columnMapping as $csvColumnName => $systemColumnName)
            {
                $lowercaseColumnMapping[$csvColumnName] = strtolower($csvColumnName);
            }


            foreach (str_getcsv(array_shift($fileLines), $this->csv_delimiter, $this->csv_delimiter, $this->csv_escape) as $header)
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
                if (($arrayKey = array_search($lowerCaseHeader, $lowercaseColumnMapping)) !== false)
                {
                    /*
                     * Add to the fields present array so that we know that the column actually exists as not all
                     * columns are required to be present.
                     */
                    $this->_fieldsPresent [] = $this->_columnMapping [$arrayKey];
                    $this->_mappedHeaders [] = $this->_columnMapping [$arrayKey];
                }
                else
                {
                    $this->_mappedHeaders [] = trim($header);
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
                        $this->csvLines [] = $csvLine;

                        // TODO lrobert: Add validation logic in
                        if (true)
                        {
                            $this->validCsvLines [] = $csvLine;
                        }
                        else
                        {
                            $this->invalidCsvLines [] = $csvLine;
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

        return $this->validCsvLines;
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

