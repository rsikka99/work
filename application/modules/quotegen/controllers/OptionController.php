
<?php

/**
 * Class Quotegen_OptionController
 */
class Quotegen_OptionController extends Tangent_Controller_Action
{

    public function init ()
    {
    }

    public function indexAction ()
    {
        // Get all current items in categories table
        $optionMapper = Quotegen_Model_Mapper_Option::getInstance();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($optionMapper,array('dealerId = ?' => Zend_Auth::getInstance()->getIdentity()->dealerId)));
        
        // Set current page
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        // Set max items per page
        $paginator->setItemCountPerPage(25);
        
        // Save entries to view paginatior
        $this->view->paginator = $paginator;
    }

    public function deleteAction ()
    {
        $optionId = $this->_getParam('id', false);
        
        if (! $optionId)
        {
            $this->_flashMessenger->addMessage(array (
                    'warning' => 'Please select an option to delete first.' 
            ));
            $this->redirector('index');
        }
        
        $optionMapper = Quotegen_Model_Mapper_Option::getInstance();
        $option = $optionMapper->find($optionId);
        
        if (! $option)
        {
            $this->_flashMessenger->addMessage(array (
                    'danger' => 'There was an error finding that option to delete.' 
            ));
            $this->redirector('index');
        }
        // If we are trying to access a option from another dealer, kick them back
        else if ($option->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array (
                                                     'danger' => 'You do not have permission to access this.'
                                               ));
            // Redirect
            $this->redirector('index');
        }
        
        $message = "Are you sure you want to delete {$option->name}?";
        $form = new Application_Form_Delete($message);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                // delete quote from database
                if ($form->isValid($values))
                {
                    Quotegen_Model_Mapper_OptionCategory::getInstance()->deleteByOptionId($option->id);
                    $optionMapper->delete($option);
                    
                    $this->_flashMessenger->addMessage(array (
                            'success' => "Option  {$this->view->escape ( $option->name )} was deleted successfully."
                    ));
                    $this->redirector('index');
                }
            }
            else // go back
            {
                $this->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    public function createAction ()
    {
        // Get master device id if passed in
        $page = $this->_getParam('page', false);
        $id = $this->_getParam('id', false);
        
        $form = new Quotegen_Form_Option();
        
        // If the form is on post insert data
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            // Get values from the form
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        $optionMapper = Quotegen_Model_Mapper_Option::getInstance();
                        
                        $option = new Quotegen_Model_Option();
                        $option->populate($values);
                        $option->dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
                        $optionId = $optionMapper->insert($option);
                        
                        // Create optionCategory with $optionId to save 
                        $optionCategory = new Quotegen_Model_OptionCategory();
                        $optionCategory->optionId = $optionId;
                        
                        foreach ( $values ['categories'] as $categoryId )
                        {
                            $optionCategory->categoryId = $categoryId;
                            Quotegen_Model_Mapper_OptionCategory::getInstance()->insert($optionCategory);
                        }
                        $this->_flashMessenger->addMessage(array (
                                                             'success' => "Option {$values['name']} Created"
                                                       ));
                        if ($page == "options")
                        {
                            // User has cancelled. Go back to the edit page
                            $this->redirector('options', 'devicesetup', 'quotegen', array (
                                    'id' => $id 
                            ));
                        }
                        else
                        {
                            // User has cancelled. Go back to the edit page
                            $this->redirector('index');
                        }
                    }
                    else // Values in form data aren't valid.
                    {
                        throw new InvalidArgumentException('Please correct the fields below');
                    }
                }
                catch ( Exception $e )
                {
                    $this->_flashMessenger->addMessage(array (
                            'danger' => $e->getMessage() 
                    ));
                }
            }
            else // Cancel was hit: redirect user
            {
                if ($page == "options")
                {
                    // User has cancelled. Go back to the edit page
                    $this->redirector('options', 'devicesetup', 'quotegen', array (
                            'id' => $id 
                    ));
                }
                else
                {
                    // User has cancelled. Go back to the edit page
                    $this->redirector('index');
                }
            }
        }
        
        $this->view->form = $form;
    }

    public function editAction ()
    {
        $optionId = $this->_getParam('id', false);
        
        // If not idea is set then back to index page
        if (! $optionId)
        {
            $this->_flashMessenger->addMessage(array (
                    'warning' => 'Please select an option first.' 
            ));
            // Redirect
            $this->redirector('index');
        }
        
        // Load the form for use
        $form = new Quotegen_Form_Option();
        
        // Find the option by id
        $optionMapper = Quotegen_Model_Mapper_Option::getInstance();
        $option = $optionMapper->find($optionId);

        // If we are trying to access a option from another dealer, kick them back
        if ($option && $option->dealerId != Zend_Auth::getInstance()->getIdentity()->dealerId)
        {
            $this->_flashMessenger->addMessage(array (
                                                     'danger' => 'You do not have permission to access this.'
                                               ));
            // Redirect
            $this->redirector('index');
        }


        $optionValues = $option->toArray();
        /* @var $category Quotegen_Model_Category */
        foreach ( $option->getCategories() as $category )
        {
            $optionValues ['categories'] [] = $category->id;
        }
        
        $form->populate($optionValues);
        $request = $this->getRequest();
        
        // If request is on post update the record
        if ($request->isPost())
        {
            
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        // Update quotesetting and message to comfirm
                        $option->populate($values);
                        
                        $optionCategoryMapper = Quotegen_Model_Mapper_OptionCategory::getInstance();
                        // Create a new category since we know the option id will stay the same at all times.
                        $optionCategory = new Quotegen_Model_OptionCategory();
                        $optionCategory->optionId = $option->id;
                        
                        $categoryIds [] = array ();
                        /* @var $category Quotegen_Model_Category */
                        foreach ( $option->getCategories() as $category )
                        {
                            
                            if (array_search((string)$category->id, $values ['categories']) === false)
                            {
                                $optionCategory->categoryId = $category->id;
                                $optionCategoryMapper->delete($optionCategory);
                            }
                            else
                            {
                                $categoryIds [] = $category->id;
                            }
                        }
                        
                        foreach ( $values ['categories'] as $categoryPostId )
                        {
                            if (array_search($categoryPostId, $categoryIds) === false)
                            {
                                $optionCategory->categoryId = $categoryPostId;
                                $optionCategoryMapper->insert($optionCategory);
                            }
                        }
                        $optionMapper->save($option, $optionId);
                        $this->_flashMessenger->addMessage(array (
                                'success' => "Category setting was updated sucessfully." 
                        ));
                        
                        $this->redirector('index');
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch ( InvalidArgumentException $e )
                {
                    $this->_flashMessenger->addMessage(array (
                            'danger' => $e->getMessage() 
                    ));
                }
            }
            else // Client hit cancel redicect
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->redirector('index');
            }
        }
        
        $this->view->form = $form;
    }

    public function viewAction ()
    {
        $this->view->option = Quotegen_Model_Mapper_Option::getInstance()->find($this->_getParam('id', false));
    }
}

