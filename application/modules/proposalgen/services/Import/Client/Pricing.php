<?php

/**
 * Class Proposalgen_Service_Import_Client_Pricing
 */
class Proposalgen_Service_Import_Client_Pricing
{
    const CSV_HEADER_TOTALS                = 'T-O-T-A-L-S';
    const CSV_HEADER_CUSTOMER_NUMBER       = 'CUSTOMER #';
    const CSV_HEADER_CUSTOMER_NAME         = 'CUSTOMER NAME';
    const CSV_HEADER_PRODUCT_DESCRIPTION   = 'PRODUCT DESCRIPTION';
    const CSV_HEADER_DEALER_PRODUCT_CODE   = 'PRODUCT CODE';
    const CSV_HEADER_OEM_PRODUCT_CODE      = 'WHOLESALER PROD CODE';
    const CSV_HEADER_CUSTOMER_PRODUCT_CODE = 'CUST PRODUCT CODE';
    const CSV_HEADER_DATE_SHIPPED          = 'SHIPPED';
    const CSV_HEADER_ORDER_NUMBER          = 'ORDER NUMBER';
    const CSV_HEADER_DATE_ORDERED          = 'ORDERED';
    const CSV_HEADER_UNIT_MEASUREMENT      = 'UM';
    const CSV_HEADER_QUANTITY              = 'QUANTITY';
    const CSV_HEADER_UNIT_PRICE            = 'UNIT PRICE';
    const CSV_HEADER_EXTENDED_AMOUNT       = 'EXTENDED AMOUNT';
    const CSV_HEADER_CURRENCY              = 'Currency';
    const CSV_HEADER_PRODUCT_DEPARTMENT    = 'Depot Product Dept';
    const CSV_HEADER_DATE_RECONCILED       = 'Reconciled';

    public static $unitMultipliers = array(
        'EA' => 1
    );

    public $csvHeaders = array(
        self::CSV_HEADER_TOTALS,
        self::CSV_HEADER_CUSTOMER_NUMBER,
        self::CSV_HEADER_CUSTOMER_NAME,
        self::CSV_HEADER_PRODUCT_DESCRIPTION,
        self::CSV_HEADER_DEALER_PRODUCT_CODE,
        self::CSV_HEADER_OEM_PRODUCT_CODE,
        self::CSV_HEADER_CUSTOMER_PRODUCT_CODE,
        self::CSV_HEADER_DATE_SHIPPED,
        self::CSV_HEADER_ORDER_NUMBER,
        self::CSV_HEADER_DATE_ORDERED,
        self::CSV_HEADER_UNIT_MEASUREMENT,
        self::CSV_HEADER_QUANTITY,
        self::CSV_HEADER_UNIT_PRICE,
        self::CSV_HEADER_EXTENDED_AMOUNT,
        self::CSV_HEADER_CURRENCY,
        self::CSV_HEADER_PRODUCT_DEPARTMENT,
        self::CSV_HEADER_DATE_RECONCILED,
    );

    /**
     * Zend Filter that will filter and validate input data
     *
     * @var Zend_Filter_Input
     */
    protected $_inputFilter;

    /**
     * @var Proposalgen_Form_ImportClientCostCsv
     */
    protected $_form;

    /**
     * @var string
     */
    protected $_filename;

    /**
     * The headers that we receive from the import.
     *
     * @var array
     */
    public $importHeaders = array();

    /**
     * @var resource
     */
    public $importFile;

    public function __construct ()
    {
        $this->_inputFilter = new Zend_Filter_Input(
            array(
                '*' => array(
                    'StripTags',
                    'StringTrim',
                ),
                self::CSV_HEADER_DATE_RECONCILED       => array(
                    'StripTags',
                    'StringTrim',
                    array('PregReplace', '/-/', '/'),
                ),
            ),
            array(
                '*'                                 => array(
                    'allowEmpty' => true,
                ),
                self::CSV_HEADER_DATE_SHIPPED       => array(
                    'allowEmpty' => true,
                    new My_Validate_DateTime(My_Validate_DateTime::FORMAT_DATE_AMERICAN),
                ),
                self::CSV_HEADER_DATE_ORDERED       => array(
                    'allowEmpty' => true,
                    new My_Validate_DateTime(My_Validate_DateTime::FORMAT_DATE_AMERICAN),
                ),
                self::CSV_HEADER_DATE_RECONCILED       => array(
                    'allowEmpty' => true,
                    new My_Validate_DateTime(My_Validate_DateTime::FORMAT_DATE_AMERICAN),
                ),
                self::CSV_HEADER_UNIT_MEASUREMENT   => array(
                    array('InArray', array('EA')),
                ),
                self::CSV_HEADER_QUANTITY           => array(
                    'Int',
                    array('GreaterThan', 0),
                ),
                self::CSV_HEADER_UNIT_PRICE         => array(
                    'Float',
                ),
                self::CSV_HEADER_PRODUCT_DEPARTMENT => array(
                    array('InArray', array('65')),
                ),
            )
        );
    }

    /**
     * Gets an instance of the form
     *
     * @return Proposalgen_Form_ImportClientCostCsv
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new Proposalgen_Form_ImportClientCostCsv('.csv');
        }

        return $this->_form;
    }

    /**
     * @return array
     */
    public function validatedHeaders ()
    {
        // Find a matching array
        if (count(array_diff($this->importHeaders, $this->csvHeaders)) == 0)
        {
            return true;
        }
        else
        {
            echo "<pre>Var dump initiated at " . __LINE__ . " of:\n" . __FILE__ . "\n\n";
            var_dump(array_diff($this->importHeaders, $this->csvHeaders));
            die();
        }

        return false;
    }

    /**
     * @return resource
     */
    public function getValidFile ()
    {
        $this->_filename = $this->getForm()->getUploadedFilename();
        if ($this->_filename)
        {
            $this->importFile    = fopen($this->_filename, "r");
            $this->importHeaders = fgetcsv($this->importFile);

            return true;
        }

        return $this->_upload->getErrors();
    }

    /**
     * Closes files and unlink upload from the system
     */
    public function closeFiles ()
    {
        fclose($this->importFile);
        unlink($this->_filename);
    }

    /**
     * @param $data
     *
     * @return array|mixed|string
     */
    public function processValidation ($data)
    {
        try
        {
            $this->_inputFilter->setData($data);

            return $this->_inputFilter->isValid() ? $this->_inputFilter->getEscaped() : array("error" => array("invalid" => $this->_inputFilter->getInvalid()));
        }
        catch (Zend_Filter_Exception $e)
        {
            return "Zend Filter Exception caught.";
        }
    }

}