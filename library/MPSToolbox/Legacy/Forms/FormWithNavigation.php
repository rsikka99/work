<?php

namespace MPSToolbox\Legacy\Forms;

use Zend_Form;
use Zend_Form_Element;

/**
 * Class FormWithNavigation
 *
 * @package MPSToolbox\Legacy\Forms
 */
class FormWithNavigation extends Zend_Form
{
    const FORM_BUTTON_MODE_DIALOG     = 1;
    const FORM_BUTTON_MODE_NAVIGATION = 2;

    const BUTTONS_ALL           = 1;
    const BUTTONS_SAVE          = 2;
    const BUTTONS_SAVE_CONTINUE = 3;
    const BUTTONS_BACK          = 4;
    const BUTTONS_CANCEL        = 5;

    static $validFormModes = array(
        self::FORM_BUTTON_MODE_DIALOG     => true,
        self::FORM_BUTTON_MODE_NAVIGATION => true,
    );

    /**
     * The type of buttons that will show up on this form
     *
     * @var int
     */
    protected $formButtonMode;

    /**
     * A list of buttons to show
     *
     * @var array
     */
    protected $buttons = array();

    /**
     *An array of button elements
     *
     * @var Zend_Form_Element[]
     */
    protected $formActionButtonElements;

    /**
     * @param null|array $options        Original Zend_Form options array
     * @param int        $formButtonMode The type of navigation we're going to use
     * @param array      $buttons        The buttons that will be present on the form
     */
    public function __construct ($options = null, $formButtonMode = self::FORM_BUTTON_MODE_DIALOG, $buttons = array(self::BUTTONS_ALL))
    {
        parent::__construct($options);

        if (isset(self::$validFormModes[$formButtonMode]) && self::$validFormModes[$formButtonMode])
        {
            $this->formButtonMode = $formButtonMode;
        }
        else
        {
            throw new \InvalidArgumentException('Invalid form button mode');
        }

        $this->buttons = $buttons;


        switch ($this->formButtonMode)
        {
            case self::FORM_BUTTON_MODE_DIALOG:
                $this->addDialogButtons($this->buttons);
                break;
            case self::FORM_BUTTON_MODE_NAVIGATION:
                $this->addNavigationButtons($this->buttons);
                break;
            default:
                throw new \InvalidArgumentException('Invalid form mode.');
        }

        $this->addDisplayGroup($this->formActionButtonElements, 'form-actions', array(
            'decorators' => array(
                array('ViewScript', array(
                    'viewScript' => 'forms/form-actions.phtml'
                )),
            ),
        ));
    }

    /**
     * Adds buttons for a navigation type of form
     *
     * @param $buttons
     */
    protected function addNavigationButtons ($buttons)
    {
        foreach ($buttons as $button)
        {
            switch ($button)
            {
                case self::BUTTONS_ALL:
                    $this->addPreviousButton(true);
                    $this->addSaveButton(true);
                    $this->addNextButton(true);
                    break;
                case self::BUTTONS_BACK:
                    $this->addPreviousButton(true);
                    break;
                case self::BUTTONS_SAVE:
                    $this->addSaveButton(true);
                    break;
                case self::BUTTONS_SAVE_CONTINUE:
                    $this->addNextButton(true);
                    break;
                default:
                    throw new \InvalidArgumentException('Invalid button for navigation form mode.');
            }
        }
    }

    /**
     * Adds buttons for a dialog type form
     *
     * @param $buttons
     */
    protected function addDialogButtons ($buttons)
    {
        foreach ($buttons as $button)
        {
            switch ($button)
            {
                case self::BUTTONS_ALL:
                    $this->addCancelButton();
                    $this->addSaveButton();
                    break;
                case self::BUTTONS_CANCEL:
                    $this->addCancelButton();
                    break;
                case self::BUTTONS_SAVE:
                    $this->addSaveButton();
                    break;
                default:
                    throw new \InvalidArgumentException('Invalid button for dialog form mode');
            }
        }
    }

    /**
     * Adds a save button to the form
     *
     * @param bool $withIcon
     *
     * @throws \Zend_Form_Exception
     */
    protected function addSaveButton ($withIcon = false)
    {
        $this->formActionButtonElements[] = $this->createElement('submit', 'save', array(
            'label'  => ($withIcon) ? '<i class="fa fa-fw fa-check"></i> Save' : 'Save',
            'ignore' => true,
        ));
    }

    /**
     * Adds a cancel button
     *
     * @param bool $withIcon
     *
     * @throws \Zend_Form_Exception
     */
    protected function addCancelButton ($withIcon = false)
    {
        $this->formActionButtonElements[] = $this->createElement('submit', 'cancel', array(
            'label'          => ($withIcon) ? '<i class="fa fa-fw fa-remove"></i> Cancel' : 'Cancel',
            'ignore'         => true,
            'formnovalidate' => true,
        ));
    }

    /**
     * Adds a next button
     *
     * @param bool $withIcon
     *
     * @throws \Zend_Form_Exception
     */
    protected function addNextButton ($withIcon = false)
    {
        $this->formActionButtonElements[] = $this->createElement('submit', 'saveAndContinue', array(
            'label'  => ($withIcon) ? 'Save & Continue <i class="fa fa-fw fa-arrow-right"></i>' : 'Save & Continue',
            'ignore' => true,
        ));
    }

    /**
     * Adds a previous button
     *
     * @param bool $withIcon
     *
     * @throws \Zend_Form_Exception
     */
    protected function addPreviousButton ($withIcon = false)
    {
        $this->formActionButtonElements[] = $this->createElement('submit', 'goBack', array(
            'label'          => ($withIcon) ? '<i class="fa fa-fw fa-arrow-left"></i> Go Back' : 'Go Back',
            'ignore'         => true,
            'formnovalidate' => true,
        ));
    }

    /**
     * Gets the form button mode
     *
     * @return int
     */
    public function getFormButtonMode ()
    {
        return $this->formButtonMode;
    }
}