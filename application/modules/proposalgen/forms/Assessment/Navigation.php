<?php

class Proposalgen_Form_Assessment_Navigation extends Twitter_Bootstrap_Form
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
        parent::__construct($options);

        $this->_buttonMode = $buttonMode;
        $this->_addClassNames("form-center-actions");
        Quotegen_Form_Quote_Navigation::addFormActionsToForm($buttonMode, $this);
    }

    /**
     * Adds form actions to a given form
     *
     * @param
     *            The mode for the buttons $buttonMode
     * @param
     *            The form to add the actions to $form
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
                                                       'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_INVERSE,
                                                       'label'      => 'Go Back',
                                                       'type'       => 'submit',
                                                       'class'      => 'pull-left',
                                                       'icon'       => 'arrow-left',
                                                       'whiteIcon'  => true
                                                  ));
            $addedButtons [] = 'goBack';
        }

        // Save Button
        if ($saveButton)
        {
            $form->addElement('button', 'save', array(
                                                     'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
                                                     'label'      => 'Save',
                                                     'type'       => 'submit',
                                                     'icon'       => 'ok',
                                                     'whiteIcon'  => true
                                                ));
            $addedButtons [] = 'save';
        }

        // Next (Save & Continue) Button
        if ($nextButton)
        {
            $form->addElement('button', 'saveAndContinue', array(
                                                                'buttonType'   => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
                                                                'label'        => 'Save & Continue',
                                                                'type'         => 'submit',
                                                                'class'        => 'pull-right',
                                                                'icon'         => 'arrow-right',
                                                                'whiteIcon'    => true,
                                                                'iconPosition' => Twitter_Bootstrap_Form_Element_Button::ICON_POSITION_RIGHT
                                                           ));
            $addedButtons [] = 'saveAndContinue';
        }

        // Add the buttons the the form actions
        $form->addDisplayGroup($addedButtons, 'actions', array(
                                                              'disableLoadDefaultDecorators' => true,
                                                              'decorators'                   => array(
                                                                  'Actions','FieldSize',
                                                                  'ViewHelper',
                                                                  'Addon',
                                                                  'ElementErrors',
                                                              )
                                                         ));
    }
}

?>
