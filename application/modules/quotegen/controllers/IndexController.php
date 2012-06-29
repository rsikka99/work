<?php

class Quotegen_IndexController extends Quotegen_Library_Controller_Quote
{

    public function indexAction ()
    {
        $request = $this->getRequest();
        $existingQuoteForm = new Quotegen_Form_SelectQuote();
        $newQuoteForm = new Quotegen_Form_Quote();
        //$newQuoteForm->setAttrib('class', 'form-vertical');
        
        $newClientForm = new Quotegen_Form_Client();
        //$newClientForm->setAttrib('class', 'form-vertical');
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            if (isset($values ['quoteId']))
            {
                // Existing Quote
                if ($existingQuoteForm->isValid($values))
                {
                    $this->resetQuoteSession($values ['quoteId']);
                    
                    // Redirect to the build controller
                    $this->_helper->redirector('index', 'build');
                }
                else
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => "There was an error selecting your quote. Please try again." 
                    ));
                }
            }
            else if (isset($values ['name']))
            {
                // New Client Form
                $this->_helper->flashMessenger(array (
                        'info' => "Creating a client from the main page is not supported yet!"
                ));
            }
            else
            {
                // New Quote
                if ($newQuoteForm->isValid($values))
                {
                    $currentDate = date('Y-m-d H:i:s');
                    $quote = new Quotegen_Model_Quote($values);
                    
                    $quote->setUserId(Zend_Auth::getInstance()->getIdentity()->id);
                    $quote->setDateCreated($currentDate);
                    $quote->setDateModified($currentDate);
                    $quote->setQuoteDate($currentDate);
                    
                    $quoteId = Quotegen_Model_Mapper_Quote::getInstance()->insert($quote);
                    
                    $this->resetQuoteSession($quoteId);
                    
                    // Redirect to the build controller
                    $this->_helper->redirector('index', 'build');
                }
                else
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => "There was an error creating your quote. Please try again." 
                    ));
                }
            }
        }
        $this->view->existingQuoteForm = $existingQuoteForm;
        $this->view->newQuoteForm = $newQuoteForm;
        $this->view->newClientForm = $newClientForm;
    }
}

