<?php

namespace MPSToolbox\Legacy\Forms;

use Zend_Form;

/**
 * Class DeleteConfirmationForm
 *
 * @package MPSToolbox\Legacy\Forms
 */
class DeleteConfirmationForm extends Zend_Form
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

        //setup cancel button
        $submit = $this->createElement('submit', 'cancel', array(
            'ignore' => true,
            'label'  => 'Cancel'
        ));
        //setup submit button
        $cancel = $this->createElement('submit', 'submit', array(
            'ignore' => true,
            'label'  => 'Delete'
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

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/delete-confirmation-form.phtml',
                    'message'    => $this->_formQuestion
                )
            )
        ));
    }
}
