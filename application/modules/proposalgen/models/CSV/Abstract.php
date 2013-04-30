<?php

/**
 * Class Proposalgen_Model_CSV_Abstract
 */
class Proposalgen_Model_CSV_Abstract extends Tangent_Model_Abstract
{
    public $validHeaders;
    public $validHeaderCount;
    protected $headers;
    protected $headerCount;
    protected $validateCSV;
    protected $data;
    protected $errors = array();
    protected $badData;
    const FIELD_NOTREQUIRED      = 0;
    const FIELD_REQUIRED         = 1;
    const FIELD_REQUIRED_DEPENDS = 2;

    /**
     * Imports a save
     *
     * @param string $filename The CSV file
     *
     * @throws Exception
     */
    public function importCSV ($filename)
    {
        try
        {
            // Open the file
            if (($handle = fopen($filename, "r")) !== false)
            {
                // Prime read to fetch headings
                $row     = fgetcsv($handle);
                $headers = array();
                foreach ($row as $header)
                {
                    $headers [] = strtolower(trim($header));
                }
                $this->headers = $headers;

                if ($this->headers === false)
                {
                    throw new Exception("File did not have any data!");
                }
                else
                {
                    $this->headerCount = count($this->headers);
                    if ($this->validateHeaders())
                    {
                        // Fetch one row at a time
                        $rowNumber = 0;
                        while (($row = fgetcsv($handle, 1000, ",")) !== false)
                        {
                            $rowNumber++;
                            // In validate headers the headerCount got set.
                            // Rows should always have the same count
                            if ($this->headerCount === count($row))
                            {
                                $newRow = array();

                                for ($i = 0; $i < count($this->headers); $i++)
                                {
                                    $trimmedValue = trim($row [$i]);
                                    if (strlen($trimmedValue) > 0)
                                    {
                                        $newRow [$this->headers [$i]] = $trimmedValue;
                                    }
                                    else
                                    {
                                        $newRow [$this->headers [$i]] = null;
                                    }
                                }

                                $rowError = $this->checkRowForErrors($newRow);

                                if ($rowError === false)
                                {
                                    $this->data [] = $newRow;
                                }
                                else
                                {
                                    $this->badData [] = array(
                                        "Row $rowNumber Excluded: " . $rowError,
                                        $newRow
                                    );
                                }
                            }
                            else
                            {
                                $this->badData [] = array(
                                    $result = "Incorrect column count. (" . count($row) . " columns) Expected " . $this->headerCount,
                                    $row
                                );
                            }
                        }
                    }
                }
                fclose($handle);
            }
        }
        catch (Exception $e)
        {
            throw new Exception("Error importing CSV", 0, $e);
        }
    }

    /**
     * @return bool
     */
    protected function validateHeaders ()
    {
        $headingsAreValid = true;
        if (isset($this->validHeaders) && isset($this->headers))
        {
            // Normalize all the headers
            foreach ($this->headers as &$tempheader)
            {
                $tempheader = strtolower(trim($tempheader));
            }

            foreach ($this->validHeaders as $header => $requirement)
            {
                if (!in_array($header, $this->headers) && $requirement == self::FIELD_REQUIRED)
                {
                    $this->errors []  = "Missing a required header - $header";
                    $headingsAreValid = false;
                }
            }
        }

        return $headingsAreValid;
    }

    /**
     * @param $row
     *
     * @return bool
     */
    protected function checkRowForErrors ($row)
    {
        $result = false;
        if ($this->getValidateCSV())
        {
            // Do validation here
        }

        return $result;
    }

    /**
     * Validates the columns of a particular data line
     *
     * @param $row
     *
     * @return bool Whether or not the column count is correct
     */
    protected function validateColumnCount ($row)
    {
        $validColumnCount = true;
        if ($this->getvalidateCSV && isset($this->validHeaders))
        {
            if (count($row) !== $this->validHeaderCount)
            {
                $validColumnCount = false;
            }
        }

        return $validColumnCount;
    }

    /**
     *
     * @return array
     */
    public function getValidHeaders ()
    {
        if (!isset($this->validHeaders))
        {
            $this->validHeaders = null;
        }

        return $this->validHeaders;
    }

    /**
     *
     * @param array $validHeaders
     *
     * @return $this
     */
    public function setValidHeaders ($validHeaders)
    {
        $this->validHeaders = $validHeaders;

        return $this;
    }

    /**
     *
     * @return array
     */
    public function getHeaders ()
    {
        if (!isset($this->headers))
        {
            $this->headers = array();
        }

        return $this->headers;
    }

    /**
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders ($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getValidateCSV ()
    {
        if (!isset($this->validateCSV))
        {
            $this->validateCSV = true;
        }

        return $this->validateCSV;
    }

    /**
     * @param $validateCSV
     *
     * @return $this
     */
    public function setValidateCSV ($validateCSV)
    {
        $this->validateCSV = $validateCSV;

        return $this;
    }

    /**
     * @return array
     */
    public function getData ()
    {
        if (!isset($this->data))
        {
            $this->data = array();
        }

        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData ($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        if (!isset($this->badData))
        {
            $this->badData = array();
        }

        return $this->badData;
    }

    /**
     * @param $badData
     *
     * @return $this
     */
    public function setBadData ($badData)
    {
        $this->badData = $badData;

        return $this;
    }
}
