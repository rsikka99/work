<?php

class Proposalgen_Model_CSV_Abstract extends Tangent_Model_Abstract
{
    public $validHeaders;
    public $validHeaderCount;
    protected $headers;
    protected $headerCount;
    protected $validateCSV;
    protected $data;
    protected $errors = array ();
    protected $baddata;
    const FIELD_NOTREQUIRED = 0;
    const FIELD_REQUIRED = 1;
    const FIELD_REQUIRED_DEPENDS = 2;

    public function importCSV ($filename)
    {
        try
        {
            // Open the file
            if (($handle = fopen($filename, "r")) !== FALSE)
            {
                // Prime read to fetch headings
                $row = fgetcsv($handle);
                $headers = array ();
                foreach ( $row as $header )
                {
                    $headers [] = strtolower(trim($header));
                }
                $this->headers = $headers;
                
                if ($this->headers === FALSE)
                {
                    throw new Exception("File did not have any data!");
                }
                else
                {
                    $this->headerCount = count($this->headers);
                    if ($this->validateHeaders())
                    {
                        // Fetch one row at a time
                        $rownumber = 0;
                        while ( ($row = fgetcsv($handle, 1000, ",")) !== FALSE )
                        {
                            $rownumber ++;
                            // In validate headers the headerCount got set.
                            // Rows should always have the same count
                            if ($this->headerCount === count($row))
                            {
                                for($i = 0; $i < count($this->headers); $i ++)
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
                                if ($rowError === FALSE)
                                {
                                    $this->data [] = $newRow;
                                }
                                else
                                {
                                    $this->baddata [] = array (
                                            "Row $rownumber Excluded: " . $rowError, 
                                            $newRow 
                                    );
                                }
                            }
                            else
                            {
                                $this->baddata [] = array (
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
        catch ( Exception $e )
        {
            throw new Exception("Error importing CSV", 0, $e);
        }
    }

    protected function validateHeaders ()
    {
        $headingsAreValid = true;
        if (isset($this->validHeaders) && isset($this->headers))
        {
            // Normalize all the headers
            foreach ( $this->headers as &$tempheader )
            {
                $tempheader = strtolower(trim($tempheader));
            }
            
            foreach ( $this->validHeaders as $header => $requirement )
            {
                if (! in_array($header, $this->headers) && $requirement == self::FIELD_REQUIRED)
                {
                    $this->errors [] = "Missing a required header - $header";
                    $headingsAreValid = false;
                }
            }
        }
        return $headingsAreValid;
    }

    protected function checkRowForErrors ($row)
    {
        $result = FALSE;
        if ($this->getValidateCSV())
        {
            // Do validation here
        }
        return $result;
    }

    /**
     * Validates the columns of a particular data line
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
     * @return the $validHeaders
     */
    public function getValidHeaders ()
    {
        if (! isset($this->validHeaders))
        {
            $this->validHeaders = null;
        }
        return $this->validHeaders;
    }

    /**
     *
     * @param $validHeaders field_type            
     */
    public function setValidHeaders ($validHeaders)
    {
        $this->validHeaders = $validHeaders;
        return $this;
    }

    /**
     *
     * @return the $headers
     */
    public function getHeaders ()
    {
        if (! isset($this->headers))
        {
            $this->headers = array ();
        }
        return $this->headers;
    }

    /**
     *
     * @param $headers field_type            
     */
    public function setHeaders ($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     *
     * @return the $validateCSV
     */
    public function getValidateCSV ()
    {
        if (! isset($this->validateCSV))
        {
            $this->validateCSV = true;
        }
        return $this->validateCSV;
    }

    /**
     *
     * @param $validateCSV field_type            
     */
    public function setValidateCSV ($validateCSV)
    {
        $this->validateCSV = $validateCSV;
        return $this;
    }

    /**
     *
     * @return the $data
     */
    public function getData ()
    {
        if (! isset($this->data))
        {
            $this->data = array ();
        }
        return $this->data;
    }

    /**
     *
     * @param $data field_type            
     */
    public function setData ($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     *
     * @return the $baddata
     */
    public function getBaddata ()
    {
        if (! isset($this->baddata))
        {
            $this->baddata = array ();
        }
        return $this->baddata;
    }

    /**
     *
     * @param $baddata field_type            
     */
    public function setBaddata ($baddata)
    {
        $this->baddata = $baddata;
        return $this;
    }
}
