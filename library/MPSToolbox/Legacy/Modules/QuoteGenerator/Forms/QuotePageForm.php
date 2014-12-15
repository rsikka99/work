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
    public function __construct ($quote = null, $options = null, $formButtonMode = self::FORM_BUTTON_MODE_NAVIGATION, $buttons = array(self::BUTTONS_ALL))
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
                $this->addElement('text', "quantity_monochrome_{$quoteDeviceGroupDevice->quoteDeviceGroupId}_{$quoteDeviceGroupDevice->quoteDeviceId}", array(
                    'label'      => 'Quantity',
                    'required'   => true,
                    'value'      => $quoteDeviceGroupDevice->monochromePagesQuantity,
                    'validators' => array(
                        'Int',
                        array(
                            'validator' => 'Between',
                            'options'   => array('min' => $minQuantityPages, 'max' => $maxQuantityPages)
                        )
                    )
                ));

                if ($quoteDeviceGroupDevice->getQuoteDevice()->isColorCapable())
                {
                    // quantity_color_<quoteDeviceGroupId>_<quoteDeviceId> : MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceGroupDeviceModel->colorPagesQuantity
                    // quantity_color_<quoteDeviceGroupId>_<quoteDeviceId> is used to store the amount of pages allocated per device
                    $this->addElement('text', "quantity_color_{$quoteDeviceGroupDevice->quoteDeviceGroupId}_{$quoteDeviceGroupDevice->quoteDeviceId}", array(
                        'label'      => 'Quantity',
                        'required'   => true,
                        'value'      => $quoteDeviceGroupDevice->colorPagesQuantity,
                        'validators' => array(
                            'Int',
                            array(
                                'validator' => 'Between',
                                'options'   => array('min' => $minQuantityPages, 'max' => $maxQuantityPages)
                            )
                        )
                    ));
                }
            }
        }

        // monochromePageMargin : MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel->monochromePageMargin
        // monochromePageMargin is used to determine margin on pages for the entire quote
        $this->addElement('text', 'monochromePageMargin', array(
            'value'      => $this->_quote->monochromePageMargin,
            'required'   => true,
            'validators' => array(
                'Float',
                array(
                    'validator' => 'Between',
                    'options'   => array(
                        'min'       => -100,
                        'max'       => 100,
                        'inclusive' => false
                    )
                )
            )
        ));

        // colorPageMargin : MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel->colorPageMargin
        // colorPageMargin is used to set a page margin for all of the color pages on the quote
        $this->addElement('text', 'colorPageMargin', array(
            'value'      => $this->_quote->colorPageMargin,
            'required'   => true,
            'validators' => array(
                'Float',
                array(
                    'validator' => 'Between',
                    'options'   => array(
                        'min'       => -100,
                        'max'       => 100,
                        'inclusive' => false
                    )
                )
            )
        ));

        // monochromeOverageMargin : MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel->monochromeOverageMargin
        // monochromeOverageMargin is used for the calcuation of overage rate per page for pages.
        $this->addElement('text', 'monochromeOverageMargin', array(
            'value'      => $this->_quote->monochromeOverageMargin,
            'required'   => true,
            'validators' => array(
                'Float',
                array(
                    'validator' => 'Between',
                    'options'   => array(
                        'min'       => -100,
                        'max'       => 100,
                        'inclusive' => false
                    )
                )
            )
        ));

        // colorOverageMargin : MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel->colorOverageMargin
        // colorOverageMargin is used for the calcuation of overage rate per page for pages.
        $this->addElement('text', 'colorOverageMargin', array(
            'value'      => $this->_quote->colorOverageMargin,
            'required'   => true,
            'validators' => array(
                'Float',
                array(
                    'validator' => 'Between',
                    'options'   => array(
                        'min'       => -100,
                        'max'       => 100,
                        'inclusive' => false
                    )
                )
            )
        ));

        // pageCoverageColor : MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel->pageCoverageColor
        // pageCoverageColor is used to set the page coverage amount in the quote
        $pageCoverageColor = $this->createElement('text', 'pageCoverageColor', array(
            'label'      => 'Page Coverage Color:',
            'required'   => true,
            'value'      => $this->_quote->pageCoverageColor,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array(
                array(
                    'validator' => 'Between',
                    'options'   => array(
                        'min'       => 0,
                        'max'       => 100,
                        'inclusive' => false
                    )
                ),
                'Float'
            )
        ));
        $this->addElement($pageCoverageColor);

        // pageCoverageMonochrome : MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel->pageCoverageMonochrome
        // pageCoverageMonochrome is used to set the page coverage amount in the quote
        $pageCoverageMonochrome = $this->createElement('text', 'pageCoverageMonochrome', array(
            'label'      => 'Page Coverage Monochrome:',
            'required'   => true,
            'value'      => $this->_quote->pageCoverageMonochrome,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array(
                'Float',
                array(
                    'validator' => 'Between',
                    'options'   => array(
                        'min'       => 0,
                        'max'       => 100,
                        'inclusive' => false
                    )
                ),
            )
        ));
        $this->addElement($pageCoverageMonochrome);

        // adminCostPerPage : MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel->adminCostPerPage
        // adminCostPerPage is a flat CPP that is used to add an additional charge per page to recoup admin related fees
        $adminCostPerPage = $this->createElement('text', 'adminCostPerPage', array(
            'label'      => 'Admin Cost Per Page:',
            'value'      => $this->_quote->adminCostPerPage,
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array(
                'Float',
                array(
                    'validator' => 'Between',
                    'options'   => array('min' => 0, 'max' => 5)
                ),
            )
        ));
        $this->addElement($adminCostPerPage);

        $tonerRankSets          = $this->_quote->getTonerRankSets();
        $dealerMonochromeVendor = $this->createElement('multiselect', 'dealerMonochromeRankSetArray',
            array(
                'class' => 'tonerMultiselect',
                'value' => $tonerRankSets['dealerMonochromeRankSetArray'],
            ));
        $dealerColorVendor      = $this->createElement('multiselect', 'dealerColorRankSetArray',
            array(
                'class' => 'tonerMultiselect',
                'value' => $tonerRankSets['dealerColorRankSetArray'],
            ));

        $this->addElement($dealerMonochromeVendor);
        $this->addElement($dealerColorVendor);

        $tonerVendors = TonerVendorManufacturerMapper::getInstance()->fetchAllForDealerDropdown();
        $dealerMonochromeVendor->setMultiOptions($tonerVendors);
        $dealerColorVendor->setMultiOptions($tonerVendors);

        QuoteNavigationForm::addFormActionsToForm(QuoteNavigationForm::BUTTONS_ALL, $this);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/quotegen/quote/pages-form.phtml'
                )
            )
        ));
    }

    /**
     * @return null|QuoteModel
     */
    public function getQuote ()
    {
        return $this->_quote;
    }
}
