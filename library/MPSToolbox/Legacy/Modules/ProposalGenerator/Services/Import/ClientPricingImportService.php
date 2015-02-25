<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Forms\ImportClientCostCsvForm;
use My_Validate_DateTime;
use Zend_Filter_Exception;
use Zend_Filter_Input;

/**
 * Class ClientPricingImportService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import
 */
class ClientPricingImportService
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

    public static $unitMultipliers = [
        'EA' => 1,
    ];

    public $csvHeaders = [
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
    ];

    /**
     * Zend Filter that will filter and validate input data
     *
     * @var Zend_Filter_Input
     */
    protected $_inputFilter;

    /**
     * @var ImportClientCostCsvForm
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
    public $importHeaders = [];

    /**
     * @var resource
     */
    public $importFile;

    public function __construct ()
    {
        $this->_inputFilter = new Zend_Filter_Input(
            [
                '*'                              => [
                    'StripTags',
                    'StringTrim',
                ],
                self::CSV_HEADER_DATE_RECONCILED => [
                    'StripTags',
                    'StringTrim',
                    ['PregReplace', '/-/', '/'],
                ],
            ],
            [
                '*'                                 => [
                    'allowEmpty' => true,
                ],
                self::CSV_HEADER_DATE_SHIPPED       => [
                    'allowEmpty' => true,
                    new My_Validate_DateTime(My_Validate_DateTime::FORMAT_DATE_AMERICAN),
                ],
                self::CSV_HEADER_DATE_ORDERED       => [
                    'allowEmpty' => true,
                    new My_Validate_DateTime(My_Validate_DateTime::FORMAT_DATE_AMERICAN),
                ],
                self::CSV_HEADER_DATE_RECONCILED    => [
                    'allowEmpty' => true,
                    new My_Validate_DateTime(My_Validate_DateTime::FORMAT_DATE_AMERICAN),
                ],
                self::CSV_HEADER_UNIT_MEASUREMENT   => [
                    ['InArray', ['EA']],
                ],
                self::CSV_HEADER_QUANTITY           => [
                    'Int',
                    ['GreaterThan', 0],
                ],
                self::CSV_HEADER_UNIT_PRICE         => [
                    'Float',
                ],
                self::CSV_HEADER_PRODUCT_DEPARTMENT => [
                    ['InArray', ['65']],
                ],
            ]
        );
    }

    /**
     * Gets an instance of the form
     *
     * @return ImportClientCostCsvForm
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new ImportClientCostCsvForm('.csv');
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

            return $this->_inputFilter->isValid() ? $this->_inputFilter->getEscaped() : ["error" => ["invalid" => $this->_inputFilter->getInvalid()]];
        }
        catch (Zend_Filter_Exception $e)
        {
            return "Zend Filter Exception caught.";
        }
    }

}