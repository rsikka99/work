
<?php

class Quotegen_OptionController extends Zend_Controller_Action
{

    public function init ()
    {
    }

    public function indexAction ()
    {
        // Get all current items in categories table
        $optionMapper = Quotegen_Model_Mapper_Option::getInstance();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($optionMapper));
        
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
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select an option to delete first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $optionMapper = Quotegen_Model_Mapper_Option::getInstance();
        $option = $optionMapper->find($optionId);
        
        if (! $option)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error finding that option to delete.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $message = "Are you sure you want to delete {$option->getName()}?";
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
                    Quotegen_Model_Mapper_OptionCategory::getInstance()->deleteByOptionId($option->getId());
                    $optionMapper->delete($option);
                    
                    $this->_helper->flashMessenger(array (
                            'success' => "Option  {$this->view->escape ( $option->getName() )} was deleted successfully." 
                    ));
                    $this->_helper->redirector('index');
                }
            }
            else // go back
            {
                $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    public function createAction ()
    {
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
                        // Persist data to database
                        $categoryMapper = new Quotegen_Model_Mapper_Category();
                        $category = new Quotegen_Model_Category();
                        $category->populate($values);
                        
                        $categoryMapper->insert($category);
                        
                        // Redirect client back to index
                        $this->_helper->redirector('index');
                    }
                    else // Values in form data aren't valid.
                    {
                        throw new InvalidArgumentException('Please correct the fields below');
                    }
                }
                catch ( Exception $e )
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => $e->getMessage() 
                    ));
                }
            }
            else // Cancel was hit: redirect user
            {
                $this->_helper->redirector('index');
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
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select an option first.' 
            ));
            // Redirect
            $this->_helper->redirector('index');
        }
        
        // Load the form for use
        $form = new Quotegen_Form_Option();
        
        // Find the option by id
        $optionMapper = Quotegen_Model_Mapper_Option::getInstance();
        $option = $optionMapper->find($optionId);
        $optionValues = $option->toArray();
        /* @var $category Quotegen_Model_Category */
        foreach ( $option->getCategories() as $category )
        {
            $optionValues ['categories'] [] = $category->getId();
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
                        $optionCategory->setOptionId($option->getId());
                        
                        $categoryIds [] = array ();
                        /* @var $category Quotegen_Model_Category */
                        foreach ( $option->getCategories() as $category )
                        {
                            
                            if (array_search((string)$category->getId(), $values ['categories']) === false)
                            {
                                $optionCategory->setCategoryId($category->getId());
                                $optionCategoryMapper->delete($optionCategory);
                            }
                            else
                            {
                                $categoryIds [] = $category->getId();
                            }
                        }
                        
                        foreach ( $values ['categories'] as $categoryPostId )
                        {
                            if (array_search($categoryPostId, $categoryIds) === false)
                            {
                                $optionCategory->setCategoryId($categoryPostId);
                                $optionCategoryMapper->insert($optionCategory);
                            }
                        }
                        $optionMapper->save($option, $optionId);
                        $this->_helper->flashMessenger(array (
                                'success' => "Category setting was updated sucessfully." 
                        ));
                        
                        $this->_helper->redirector('index');
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch ( InvalidArgumentException $e )
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => $e->getMessage() 
                    ));
                }
            }
            else // Client hit cancel redicect
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }
        
        $this->view->form = $form;
    }

    public function viewAction ()
    {
    }
}

