<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Forms;

use InvalidArgumentException;
use Zend_Form;

/**
 * Class HardwareOptimizationNavigationForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Forms
 */
class HardwareOptimizationNavigationForm extends Zend_Form
{
    const BUTTONS_ALL       = 1;
    const BUTTONS_SAVE_NEXT = 2;
    const BUTTONS_BACK_NEXT = 3;
    const BUTTONS_BACK_SAVE = 4;
    const BUTTONS_BACK      = 5;
    const BUTTONS_SAVE      = 6;
    const BUTTONS_NEXT      = 7;

    /**
     * The button mode
     *
     * @var int
     */
    private $_buttonMode;

    /**
     * Constructor for navigation form
     *
     * @param int  $buttonMode
     *            What buttons to show on this form. Use the constants provided.
     *            ALL = Back, Save, Save And Continue.
     *            TWO_BUTTONS = Back, Save And Continue.
     *            ONLY_BACK = Back.
     *            ONLY_NEXT = Save And Continue.
     * @param null $options
     *            See Zend_Form::__construct()
     *
     * @throws InvalidArgumentException Thrown when you pass an incorrect button mode
     */
    public function __construct ($buttonMode = self::BUTTONS_ALL, $options = null)
    {
        $this->setMethod('POST');
        parent::__construct($options);

        $this->_buttonMode = $buttonMode;
        HardwareOptimizationNavigationForm::addFormActionsToForm($buttonMode, $this);
    }

    /**
     * Adds form actions to a given form
     *
     * @param int       $buttonMode The mode for the buttons
     * @param Zend_Form $form       The form to add the actions to
     *
     * @throws InvalidArgumentException This is thrown when you specify an invalid button mode
     */
    public static function addFormActionsToForm ($buttonMode, $form)
    {
        // Validate the button mode
        if (!in_array($buttonMode, array(
            self::BUTTONS_ALL,
            self::BUTTONS_SAVE_NEXT,
            self::BUTTONS_BACK_NEXT,
            self::BUTTONS_BACK_SAVE,
            self::BUTTONS_BACK,
            self::BUTTONS_SAVE,
            self::BUTTONS_NEXT
        ))
        )
        {
            throw new InvalidArgumentException('Invalid Button Mode!');
        }

        $goBackButton = false;
        $nextButton   = false;
        $saveButton   = false;
        $addedButtons = array();

        switch ($buttonMode)
        {
            case self::BUTTONS_ALL :
                $goBackButton = true;
                $saveButton   = true;
                $nextButton   = true;
                break;
            case self::BUTTONS_SAVE_NEXT :
                $saveButton = true;
                $nextButton = true;
                break;
            case self::BUTTONS_BACK_NEXT :
                $goBackButton = true;
                $nextButton   = true;
                break;
            case self::BUTTONS_BACK_SAVE :
                $goBackButton = true;
                $saveButton   = true;
                break;
            case self::BUTTONS_BACK :
                $goBackButton = true;
                break;
            case self::BUTTONS_SAVE :
                $saveButton = true;
                break;
            case self::BUTTONS_NEXT :
                $nextButton = true;
                break;
        }

        // Go Back
        if ($goBackButton)
        {
            $form->addElement('button', 'goBack', array(
                'label' => '<i class="fa fa-fw fa-arrow-left"></i>  Go Back',
                'type'  => 'submit',
                'class' => 'btn btn-default',
            ));
            $addedButtons [] = 'goBack';
        }

        // Save Button
        if ($saveButton)
        {
            $form->addElement('button', 'save', array(
                'label' => '<i class="fa fa-fw fa-check"></i> Save',
                'type'  => 'submit',
                'class' => 'btn btn-success',
            ));
            $addedButtons [] = 'save';
        }

        // Next (Save & Continue) Button
        if ($nextButton)
        {
            $form->addElement('button', 'saveAndContinue', array(
                'label' => 'Save & Continue <i class="fa fa-fw fa-arrow-right"></i>',
                'type'  => 'submit',
                'class' => 'btn btn-primary',
            ));
            $addedButtons [] = 'saveAndContinue';
        }

        // Add the buttons the the form actions
        $form->addDisplayGroup($addedButtons, 'actions', array(
            'disableLoadDefaultDecorators' => true,
            'decorators'                   => array(
                'Actions'
            )
        ));
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/hardwareoptimization/navigation-form.phtml'
                )
            )
        ));
    }
}