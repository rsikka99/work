<?php

abstract class Tangent_PHPUnit_Framework_ZendFormTestCase extends Tangent_PHPUnit_Framework_TestCase
{


    /**
     * Gets an instance of a Zend_Form so we can test the validation
     *
     * @return Zend_Form
     */
    abstract public function getForm ();

    /**
     * Gets data that should pass the form validation (In PHPUnit dataProvider format)
     *
     * @return mixed
     */
    abstract public function getGoodData ();

    /**
     * Gets data that should fail the form validation (In PHPUnit dataProvider format)
     *
     * @return mixed
     */
    abstract public function getBadData ();

    /**
     * @dataProvider getGoodData
     *               Tests whether the form accepts valid data
     *
     * @param array $data
     *
     * @throws Exception
     * @throws Zend_Form_Exception
     */
    public function testFormAcceptsValidData ($data)
    {
        $form = $this->getForm();

        if (!$form instanceof Zend_Form)
        {
            debug_print_backtrace(0,1);
        }

        $this->assertTrue($form->isValid($data), $this->getExpandedMessages($form));
    }

    /**
     * @dataProvider getBadData
     *               Tests if the form errors on invalid data
     *
     * @param array $data
     *
     * @throws Exception
     * @throws Zend_Form_Exception
     */
    public function testFormRejectsBadData ($data)
    {
        $form = $this->getForm();

        if (!$form instanceof Zend_Form)
        {
            debug_print_backtrace(0,1);
        }

        $this->assertFalse($form->isValid($data), $this->getExpandedMessages($form));
    }

    /**
     * @param Zend_Form $form
     *
     * @return String[] $formMessages
     */
    public function getExpandedMessages ($form)
    {
        $errorMessages = $form->getMessages();
        $formMessages  = [];

        foreach ($errorMessages as $elementName => $messages)
        {
            foreach ($messages as $messageId => $messageText)
            {
                $formMessages[] = "Element: '$elementName' Error: '$messageText'";
            }
        }

        return implode(PHP_EOL, $formMessages);
    }
}
