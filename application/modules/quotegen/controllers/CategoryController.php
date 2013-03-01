
<?php

class Quotegen_CategoryController extends Tangent_Controller_Action
{

    public function init ()
    {
    }
    
    // Shows all the current categories in a table
    public function indexAction ()
    {
        // Get all current items in categories table
        $categoryMapper = new Quotegen_Model_Mapper_Category();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($categoryMapper));
        
        // Set current page
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        // Set max items per page
        $paginator->setItemCountPerPage(25);
        
        // Save entries to view paginatior
        $this->view->paginator = $paginator;
    }

    public function deleteAction ()
    {
        $categoryId = $this->_getParam('id', false);
        
        if (! $categoryId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a category to delete first.' 
            ));
            $this->redirector('index');
        }
        
        // Get a Quotegen_Model_Category object from the id that was passed.
        $category = Quotegen_Model_Mapper_Category::getInstance()->find($categoryId);
        
        if (! $category)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the category to delete.' 
            ));
            $this->redirector('index');
        }
        
        $message = "Are you sure you want to delete {$category->name}?";
        $form = new Application_Form_Delete($message);
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Delete the entry from the lookup table by the category id.
                        Quotegen_Model_Mapper_OptionCategory::getInstance()->deleteByCategoryId($categoryId);
                        // Delete the entry from the category table.
                        Quotegen_Model_Mapper_Category::getInstance()->delete($category);
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "Category  {$this->view->escape ( $category->name )} was deleted successfully."
                        ));
                        
                        // Redirect the user back to index action of this controller.
                        $this->redirector('index');
                    }
                }
                catch ( Exception $e )
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => 'There was an error deleting this category.  Please try again.' 
                    ));
                }
            }
            else
            {
                // Redirect the user back to index action of this controller.
                $this->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    public function createAction ()
    {
        $form = new Quotegen_Form_Category();
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            // When user is posting data, get the values that have been posted.
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        // Attempt to save the category to the database.
                        $category = new Quotegen_Model_Category();
                        $category->populate($values);
                        Quotegen_Model_Mapper_Category::getInstance()->insert($category);
                        
                        // Redirect client back to index
                        $this->redirector('index');
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => 'Please correct the errors below.' 
                        ));
                    }
                }
                catch ( Exception $e )
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => 'There was an error creating this category.  Please try again..' 
                    ));
                }
            }
            else // If user has selected cancel send user back to the index pages of this Controller
            {
                $this->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    public function editAction ()
    {
        $categoryId = $this->_getParam('id', false);
        
        if (! $categoryId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a category first.' 
            ));
            $this->redirector('index');
        }
        
        // Find client and pass form object
        $form = new Quotegen_Form_Category();
        $category = Quotegen_Model_Mapper_Category::getInstance()->find($categoryId);
        $form->populate($category->toArray());
        
        $request = $this->getRequest();
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
                        $category->populate($values);
                        $category->id = $categoryId;
                        Quotegen_Model_Mapper_Category::getInstance()->save($category, $categoryId);
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "Category was updated sucessfully." 
                        ));
                        
                        $this->redirector('index');
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => 'Please correct the errors below' 
                        ));
                    }
                }
                catch ( Exception $e )
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => 'There was an error updating this category.  Please try again' 
                    ));
                }
            }
            else
            {
                // User has cancelled - redirect
                $this->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    public function viewAction ()
    {
        $categoryId = $this->_getParam('id', false);
        
        // Get the client
        $mapper = new Quotegen_Model_Mapper_Category();
        $category = $mapper->find($categoryId);
        $this->view->category = $category;
    }
}

