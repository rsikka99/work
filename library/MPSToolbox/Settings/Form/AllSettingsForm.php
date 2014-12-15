<?php

namespace MPSToolbox\Settings\Form;

use MPSToolbox\Legacy\Forms\FormWithNavigation;

/**
 * Class AllSettingsForm
 *
 * @package MPSToolbox\Settings\Form
 */
class AllSettingsForm extends FormWithNavigation
{

    /**
     * @var CurrentFleetSettingsForm
     */
    public $currentFleetSettingsForm;

    /**
     * @var ProposedFleetSettingsForm
     */
    public $proposedFleetSettingsForm;

    /**
     * @var GenericSettingsForm
     */
    public $genericSettingsForm;

    /**
     * @var QuoteSettingsForm
     */
    public $quoteSettingsForm;

    /**
     * @var OptimizationSettingsForm
     */
    public $optimizationSettingsForm;

    /**
     * @var array
     */
    protected $tonerVendorList;

    /**
     * @param null|array $options
     * @param int        $formButtonMode
     * @param array      $buttons
     */
    public function __construct ($options = null, $formButtonMode = FormWithNavigation::FORM_BUTTON_MODE_DIALOG, $buttons = array(FormWithNavigation::BUTTONS_ALL))
    {
        parent::__construct($options, $formButtonMode, $buttons);
    }

    /**
     * Initializes the form with all the elements required
     */
    public function init ()
    {
        $this->setMethod('post');

        $this->currentFleetSettingsForm  = new CurrentFleetSettingsForm(['tonerVendorList' => $this->getTonerVendorList()]);
        $this->proposedFleetSettingsForm = new ProposedFleetSettingsForm(['tonerVendorList' => $this->getTonerVendorList()]);
        $this->genericSettingsForm       = new GenericSettingsForm();
        $this->quoteSettingsForm         = new QuoteSettingsForm();
        $this->optimizationSettingsForm  = new OptimizationSettingsForm(['tonerVendorList' => $this->getTonerVendorList()]);

        $this->addSubForm($this->currentFleetSettingsForm, 'currentFleetSettingsForm');
        $this->addSubForm($this->proposedFleetSettingsForm, 'proposedFleetSettingsForm');
        $this->addSubForm($this->genericSettingsForm, 'genericSettingsForm');
        $this->addSubForm($this->quoteSettingsForm, 'quoteSettingsForm');
        $this->addSubForm($this->optimizationSettingsForm, 'optimizationSettingsForm');
    }

    /**
     * Overrides the parent to enforce a view script to render the form
     *
     * @return void|\Zend_Form
     */
    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/settings/client-settings-form.phtml']]]);
    }

    /**
     * Getter for toner vendor list
     *
     * @return array
     */
    public function getTonerVendorList ()
    {
        return $this->tonerVendorList;
    }

    /**
     * Setter for toner vendor list
     *
     * @param array $value
     *
     * @return $this
     */
    public function setTonerVendorList ($value)
    {
        $this->tonerVendorList = $value;

        return $this;
    }
}