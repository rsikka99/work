<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use MPSToolbox\Legacy\Forms\FormWithNavigation;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\LeasingSchemaMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel;
use Zend_Auth;

/**
 * Class QuoteProfitabilityForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class QuoteProfitabilityForm extends FormWithNavigation
{
    protected $_leasingSchemaId;
    /**
     * This represents the current quote being worked on
     *
     * @var QuoteModel
     */
    protected $_quote;

    /**
     * @param null|QuoteModel $quote
     * @param null|int        $leasingSchemaId
     * @param null|array      $options
     * @param int             $formButtonMode
     * @param array           $buttons
     */
    public function __construct ($quote = null, $leasingSchemaId = null, $options = null, $formButtonMode = self::FORM_BUTTON_MODE_NAVIGATION, $buttons = [self::BUTTONS_ALL])
    {
        $this->_leasingSchemaId = $leasingSchemaId;
        $this->_quote           = $quote;
        parent::__construct($options, $formButtonMode, $buttons);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        if ($this->_quote->isLeased())
        {
            $leasingSchemas  = [];
            $leasingSchemaId = null;

            foreach (LeasingSchemaMapper::getInstance()->getSchemasForDealer(Zend_Auth::getInstance()->getIdentity()->dealerId) as $leasingSchema)
            {
                // Use this to grab the first id in the leasing schema dropdown
                if (!$leasingSchemaId)
                {
                    $leasingSchemaId = $leasingSchema->id;
                }
                $leasingSchemas [$leasingSchema->id] = $leasingSchema->name;
            }

            /**
             * If the quote has a leasing schema term already selected we should grab the values from the schema it's from
             */
            if ($this->getQuote()->getLeasingSchemaTerm() && $this->getQuote()->getLeasingSchemaTerm()->getLeasingSchema()->id && $this->getQuote()->getLeasingSchemaTerm()->getLeasingSchema()->dealerId == Zend_Auth::getInstance()->getIdentity()->dealerId)
            {
                $leasingSchemaId = $this->getQuote()->getLeasingSchemaTerm()->getLeasingSchema()->id;
            }

            /**
             * Finally if we've manually specified a new schema, we should grab those values since they will be used for validation
             */
            if ($this->_leasingSchemaId)
            {
                $leasingSchemaId = $this->_leasingSchemaId;
            }

            $this->addElement('select', 'leasingSchemaId', [
                'label'        => 'Leasing Schema:',
                'multiOptions' => $leasingSchemas,
                'required'     => true,
                'value'        => $leasingSchemaId,
            ]);

            $leasingSchema      = LeasingSchemaMapper::getInstance()->find($leasingSchemaId);
            $leasingSchemaTerms = [];
            $firstId            = null;

            if ($leasingSchema && $leasingSchemas)
            {
                foreach ($leasingSchema->getTerms() as $leasingSchemaTerm)
                {
                    $leasingSchemaTerms [$leasingSchemaTerm->id] = number_format($leasingSchemaTerm->months) . " months";
                    if ($firstId == null)
                    {
                        $firstId = $leasingSchemaTerm->id;
                    }
                }
            }

            /**
             * Figure out which term to set as a default
             */
            if (!$this->getQuote()->getLeasingSchemaTerm() && $firstId != null)
            {
                $termId = $firstId;
            }
            else
            {
                $termId = $this->getQuote()->getLeasingSchemaTerm()->id;
            }

            $this->addElement('select', 'leasingSchemaTermId', [
                'label'        => 'Lease Term:',
                'multiOptions' => $leasingSchemaTerms,
                'required'     => true,
                'value'        => $termId,
            ]);
        }

        /**
         * ----------------------------------------------------------------------
         * Form elements for devices
         * ----------------------------------------------------------------------
         */
        foreach ($this->getQuote()->getQuoteDevices() as $quoteDevice)
        {
            if ($quoteDevice->calculateTotalQuantity() > 0)
            {
                /**
                 * Package Markup
                 */
                $this->addElement('text', "packageMarkup_{$quoteDevice->id}", [
                    'label'      => 'Markup',
                    'required'   => true,
                    'value'      => $quoteDevice->packageMarkup,
                    'validators' => [
                        'Float',
                        [
                            'validator' => 'Between',
                            'options'   => ['min' => 0, 'max' => 99999],
                        ],
                    ],
                ]);

                /**
                 * Margin
                 */
                $this->addElement('text', "margin_{$quoteDevice->id}", [
                    'label'      => 'Margin',
                    'required'   => true,
                    'value'      => $quoteDevice->margin,
                    'validators' => [
                        'Float',
                        [
                            'validator' => 'Between',
                            'options'   => ['min' => -100, 'max' => 100, 'inclusive' => false],
                        ],
                    ],
                ]);

                /**
                 * Elements for leased quotes only
                 */
                if ($this->_quote->isLeased())
                {
                    /**
                     * Residual
                     */
                    $this->addElement('text', "residual_{$quoteDevice->id}", [
                        'label'      => 'Residual',
                        'required'   => true,
                        'value'      => $quoteDevice->buyoutValue,
                        'validators' => [
                            'Float',
                            [
                                'validator' => 'Between',
                                'options'   => ['min' => 0, 'max' => 30000, 'inclusive' => true],
                            ],
                        ],
                    ]);
                }
            }
        }
    }

    /**
     * Overrides the loadDefaultDecorators and allows us to use a view script to render the form elements.
     *
     * @see Zend_Form::loadDefaultDecorators()
     */
    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/quotegen/quote/profitability-form.phtml']], 'Form']);
    }

    /**
     * Gets the quote
     *
     * @return QuoteModel
     */
    public function getQuote ()
    {
        return $this->_quote;
    }

    /**
     * Sets the quote
     *
     * @param QuoteModel $_quote
     *
     * @return $this
     */
    public function setQuote ($_quote)
    {
        $this->_quote = $_quote;

        return $this;
    }
}