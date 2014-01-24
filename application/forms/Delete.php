<?php

/**
 * Class Application_Form_Delete
 */
class Application_Form_Delete extends Twitter_Bootstrap_Form_Horizontal
{
    protected $_formQuestion;

    /**
     * Creates a deletion form
     *
     * @param string     $message The message to present to the users
     * @param null|array $options
     *
     */
    public function __construct ($message = "Are you sure you want to delete?", $options = null)
    {
        $this->_formQuestion = $message;
        parent::__construct($options);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        $this->setAttrib('class', 'form-horizontal');

        $note = new My_Form_Element_Paragraph('question');
        $note->setValue($this->_formQuestion);

        $this->addElement($note);

        //setup cancel button
        $submit = $this->createElement('submit', 'cancel', array(
            'ignore' => true,
            'label'  => 'Cancel'
        ));
        //setup submit button
        $cancel = $this->createElement('submit', 'submit', array(
            'ignore'     => true,
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'label'      => 'Delete'
        ));

        $this->addDisplayGroup(array(
            $submit,
            $cancel
        ), 'actions', array(
            'disableLoadDefaultDecorators' => true,
            'decorators'                   => array(
                'Actions'
            ),
            'class'                        => 'form-actions-center'
        ));

    }
}
