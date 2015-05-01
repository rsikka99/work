<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use MPSToolbox\Legacy\Forms\FormWithNavigation;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorManufacturerMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceGroupDeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceGroupModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel;

/**
 * Class QuotePageForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class QuotePageForm extends FormWithNavigation
{
    /**
     * This represent the current quote being in use
     *
     * @var QuoteModel
     */
    private $_quote;

    /**
     * @param null|QuoteModel $quote
     * @param null|array      $options
     * @param array|int       $formButtonMode
     * @param array           $buttons
     */
    public function __construct ($quote = null, $options = null, $formButtonMode = self::FORM_BUTTON_MODE_NAVIGATION, $buttons = [self::BUTTONS_ALL])
    {
        $this->_quote = $quote;
        parent::__construct($options, $formButtonMode, $buttons);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        // Validation variables
        $minQuantityPages = 0;
        $maxQuantityPages = 100000;

        /* @var $quoteDeviceGroup QuoteDeviceGroupModel */
        foreach ($this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {

            /* @var $quoteDeviceGroupDevice QuoteDeviceGroupDeviceModel */
            foreach ($quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice)
            {
                // quantity_monochrome_<quoteDeviceGroupId>_<quoteDeviceId> : MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceGroupDeviceModel->monochromePagesQuantity
                // quantity_monochrome_<quoteDeviceGroupId>_<quoteDeviceId> is used to store the amount of pages allocated per device
                $this->addElement('text_int', "quantity_monochrome_{$quoteDeviceGroupDevice->quoteDeviceGroupId}_{$quoteDeviceGroupDevice->quoteDeviceId}", [
                    'label'      => 'Quantity',
                    'required'   => true,
                    'value'      => $quoteDeviceGroupDevice->monochromePagesQuantity,
                    'validators' => [
                        'Int',
                        [
                            'validator' => 'Between',
                            'options'   => ['min' => $minQuantityPages, 'max' => $maxQuantityPages],
                        ],
                    ],
                ]);

                if ($quoteDeviceGroupDevice->getQuoteDevice()->isColorCapable())
                {
                    // quantity_color_<quoteDeviceGroupId>_<quoteDeviceId> : MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceGroupDeviceModel->colorPagesQuantity
                    // quantity_color_<quoteDeviceGroupId>_<quoteDeviceId> is used to store the amount of pages allocated per device
                    $this->addElement('text_int', "quantity_color_{$quoteDeviceGroupDevice->quoteDeviceGroupId}_{$quoteDeviceGroupDevice->quoteDeviceId}", [
                        'label'      => 'Quantity',
                        'required'   => true,
                        'value'      => $quoteDeviceGroupDevice->colorPagesQuantity,
                        'validators' => [
                            'Int',
                            [
                                'validator' => 'Between',
                                'options'   => ['min' => $minQuantityPages, 'max' => $maxQuantityPages],
                            ],
                        ],
                    ]);
                }
            }
        }

        // monochromePageMargin : MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel->monochromePageMargin
        // monochromePageMargin is used to determine margin on pages for the entire quote
        $this->addElement('text_float', 'monochromePageMargin', [
            'value'      => $this->_quote->monochromePageMargin,
            'required'   => true,
            'validators' => [
                'Float',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => -100, 'max' => 100, 'inclusive' => false],
                ],
            ],
        ]);

        // colorPageMargin : MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel->colorPageMargin
        // colorPageMargin is used to set a page margin for all of the color pages on the quote
        $this->addElement('text_float', 'colorPageMargin', [
            'value'      => $this->_quote->colorPageMargin,
            'required'   => true,
            'validators' => [
                'Float',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => -100, 'max' => 100, 'inclusive' => false],
                ],
            ],
        ]);

        // monochromeOverageMargin : MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel->monochromeOverageMargin
        // monochromeOverageMargin is used for the calcuation of overage rate per page for pages.
        $this->addElement('text_float', 'monochromeOverageMargin', [
            'value'      => $this->_quote->monochromeOverageMargin,
            'required'   => true,
            'validators' => [
                'Float',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => -100, 'max' => 100, 'inclusive' => false],
                ],
            ],
        ]);

        // colorOverageMargin : MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel->colorOverageMargin
        // colorOverageMargin is used for the calcuation of overage rate per page for pages.
        $this->addElement('text_float', 'colorOverageMargin', [
            'value'      => $this->_quote->colorOverageMargin,
            'required'   => true,
            'validators' => [
                'Float',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => -100, 'max' => 100, 'inclusive' => false],
                ],
            ],
        ]);

        // pageCoverageColor : MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel->pageCoverageColor
        // pageCoverageColor is used to set the page coverage amount in the quote
        $pageCoverageColor = $this->createElement('text_float', 'pageCoverageColor', [
            'label'      => 'Page Coverage Color:',
            'required'   => true,
            'value'      => $this->_quote->pageCoverageColor,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                'Float',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 0, 'max' => 100, 'inclusive' => false],
                ],
            ],
        ]);
        $this->addElement($pageCoverageColor);

        // pageCoverageMonochrome : MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel->pageCoverageMonochrome
        // pageCoverageMonochrome is used to set the page coverage amount in the quote
        $pageCoverageMonochrome = $this->createElement('text_float', 'pageCoverageMonochrome', [
            'label'      => 'Page Coverage Monochrome:',
            'required'   => true,
            'value'      => $this->_quote->pageCoverageMonochrome,
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                'Float',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 0, 'max' => 100, 'inclusive' => false],
                ],
            ],
        ]);
        $this->addElement($pageCoverageMonochrome);

        // adminCostPerPage : MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel->adminCostPerPage
        // adminCostPerPage is a flat CPP that is used to add an additional charge per page to recoup admin related fees
        $adminCostPerPage = $this->createElement('text_currency', 'adminCostPerPage', [
            'label'      => 'Admin Cost Per Page:',
            'value'      => $this->_quote->adminCostPerPage,
            'required'   => true,
            'validators' => [
                'Float',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => 0, 'max' => 5],
                ],
            ],
        ]);
        $this->addElement($adminCostPerPage);

        $tonerRankSets          = $this->_quote->getTonerRankSets();
        $dealerMonochromeVendor = $this->createElement('multiselect', 'dealerMonochromeRankSetArray',
            [
                'class' => 'tonerMultiselect',
                'value' => $tonerRankSets['dealerMonochromeRankSetArray'],
            ]);
        $dealerColorVendor      = $this->createElement('multiselect', 'dealerColorRankSetArray',
            [
                'class' => 'tonerMultiselect',
                'value' => $tonerRankSets['dealerColorRankSetArray'],
            ]);

        $this->addElement($dealerMonochromeVendor);
        $this->addElement($dealerColorVendor);

        $tonerVendors = TonerVendorManufacturerMapper::getInstance()->fetchAllForDealerDropdown();
        $dealerMonochromeVendor->setMultiOptions($tonerVendors);
        $dealerColorVendor->setMultiOptions($tonerVendors);

        QuoteNavigationForm::addFormActionsToForm(QuoteNavigationForm::BUTTONS_ALL, $this);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/quotegen/quote/pages-form.phtml']]]);
    }

    /**
     * @return null|QuoteModel
     */
    public function getQuote ()
    {
        return $this->_quote;
    }
}
