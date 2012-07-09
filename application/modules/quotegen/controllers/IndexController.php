<?php

class Quotegen_IndexController extends Quotegen_Library_Controller_Quote
{

    public function indexAction ()
    {
        $request = $this->getRequest();
        $existingQuoteForm = new Quotegen_Form_SelectQuote();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            if (isset($values ['quoteId']))
            {
                // Existing Quote
                if ($existingQuoteForm->isValid($values))
                {
                    // Redirect to the build controller
                    $this->_helper->redirector('index', 'quote_devices', null, array('quoteId' => $values['quoteId']));
                }
                else
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => "There was an error selecting your quote. Please try again." 
                    ));
                }
            }
        }
        $this->view->existingQuoteForm = $existingQuoteForm;
    }
}

