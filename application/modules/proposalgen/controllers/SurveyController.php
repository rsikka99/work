<?php

/**
 * Description of SurveyController:
 * This controller handles the survey/questionaire.  User should
 * be quided through a series of forms where they are asked to answer
 * questions about their existing fleet of printers.
 *
 * @author Chris Garrah
 */
class Proposalgen_SurveyController extends Zend_Controller_Action
{
    public $_form;
    public $_namespace = 'SurveyController';
    public $_session;
    private $_verificationURL;
    private $config;
    
    // general session to track which pages have been visited
    public $_pgenSession;
    public $_pgenNamespace = 'general';

    public function setSession ($set)
    {
        $this->_pgenSession = $set;
    }

    public function getSession ()
    {
        return $this->_pgenSession;
    }

    function init ()
    {
        $this->view->controller = "survey";
        $this->config = Zend_Registry::get('config');
        $this->initView();
        // $this->view->app = $this->config->app;
        $this->view->user = Zend_Auth::getInstance()->getIdentity();
        $this->user_id = Zend_Auth::getInstance()->getIdentity()->user_id;
        $this->view->privilege = Zend_Auth::getInstance()->getIdentity()->privileges;
        $this->view->MPSProgramName = $this->config->app->MPSProgramName;
        
        // set the namespace for storing which pages have been visited
        if (null === $this->_pgenSession)
        {
            $this->_pgenSession = new Zend_Session_Namespace($this->_pgenNamespace);
        } // end if
    
    } // end init
    
    /**
     * Checks to see if the user can view a particular sub form page.
     * If all
     * questions have been answered for
     * the subform, then the user may view the page.
     */
    public function canViewForm ($subform)
    {
        $formComplete = true;
        $db = Zend_Db_Table::getDefaultAdapter();
        $textAnswersTable = new Proposalgen_Model_DbTable_TextAnswers();
        $numericAnswersTable = new Proposalgen_Model_DbTable_NumericAnswers();
        $dateAnswersTable = new Proposalgen_Model_DbTable_DateAnswers();
        $session = new Zend_Session_Namespace('report');
        $reportId = $session->report_id;
        $subform = (strtolower($subform));
        $form = $this->getForm();
        
        // checking all the elements of the subform
        $allElements = $form->$subform->getElements();
        foreach ( $allElements as $element )
        {
            $id = $element->getAttrib('id');
            $type = $element->getAttrib('tmtw');
            if ($type == 'numeric')
            {
                $table = $numericAnswersTable;
                $dataType = 'numeric_answer';
            }
            else if ($type == 'date')
            {
                $table = $dateAnswersTable;
                $dataType = 'date_answer';
            }
            else
            {
                $table = $textAnswersTable;
                $dataType = 'textual_answer';
            }
            
            if ($reportId == null)
            {
                $formComplete = false;
            }
            
            if ($id && $reportId)
            {
                // text only elements have no IDs associated with them, so they
                // are skipped (ID = null).
                $db->beginTransaction();
                try
                {
                    $where = $table->getAdapter()->quoteInto('report_id = ' . $reportId . ' AND question_id = ?', $id, 'INTEGER');
                    $row = $table->fetchRow($where);
                    $db->commit();
                    if (is_null($row [$dataType]))
                    {
                        $formComplete = false;
                    }
                }
                catch ( Zend_Db_Exception $e )
                {
                    $db->rollback();
                
                }
                catch ( Exception $e )
                {
                    $db->rollback();
                
                }
            }
        }
        return $formComplete;
    }

    /**
     * Checks the database to see if all the form questions for each particular
     * sub form are complete
     * If they are, then the subform is added to the side bar menu.
     * If all forms
     * are complete, then the link
     * to the verification page is added as well.
     */
    public function getCompletedForms ()
    {
        $serverUrlHelper = new Zend_View_Helper_ServerUrl();
        $baseUrlHelper = new Zend_View_Helper_BaseUrl();
        $baseURL = "http://" . $serverUrlHelper->getHost() . $baseUrlHelper->getBaseUrl();
        $formComplete = true;
        $db = Zend_Db_Table::getDefaultAdapter();
        $textAnswersTable = new Proposalgen_Model_DbTable_TextAnswers();
        $numericAnswersTable = new Proposalgen_Model_DbTable_NumericAnswers();
        $dateAnswersTable = new Proposalgen_Model_DbTable_DateAnswers();
        $form = $this->getForm();
        $session = new Zend_Session_Namespace('report');
        $reportId = $session->report_id;
        $allForms = $this->getPotentialForms();
        $session->currentPage = $this->view->baseUrl() . '/company';
        
        if ($reportId)
        {
            // check report devices_modified flag and redirect to warning
            $report = Proposalgen_Model_Mapper_Report::getInstance()->find($reportId);
            
            if ($report->getDevicesModified())
            {
                // redirect to warning page
                $this->_redirect('/data/modificationwarning');
                break;
            }
        }
        
        // checking all the elements in all the subforms
        foreach ( $allForms as $subform )
        {
            $allElements = $form->$subform->getElements();
            
            foreach ( $allElements as $element )
            {
                $id = $element->getAttrib('id');
                $type = $element->getAttrib('tmtw');
                if ($type == 'numeric')
                {
                    $table = $numericAnswersTable;
                    $dataType = 'numeric_answer';
                }
                else if ($type == 'date')
                {
                    $table = $dateAnswersTable;
                    $dataType = 'date_answer';
                }
                else
                {
                    $table = $textAnswersTable;
                    $dataType = 'textual_answer';
                }
                
                $where = $table->getAdapter()->quoteInto('report_id = ' . $reportId . ' AND question_id = ?', $id, 'INTEGER');
                $db->beginTransaction();
                if ($reportId == null)
                {
                    $formComplete = false;
                }
                
                if ($id && $reportId)
                {
                    // text only elements have no IDs associated with them, so
                    // they are skipped (ID = null).
                    try
                    {
                        $row = $table->fetchRow($where);
                        $db->commit();
                        if (is_null($row [$dataType]))
                        {
                            $formComplete = false;
                            $session->verify = false;
                        }
                    
                    }
                    catch ( Zend_Db_Exception $e )
                    {
                        $db->rollback();
                    
                    }
                    catch ( Exception $e )
                    {
                        $db->rollback();
                    
                    }
                }
            }
            
            // if form is complete, add it to side menu.
            if ($formComplete)
            {
                $this->_pgenSession->surveyPages [$subform] = Array (
                        'url' => $baseURL . '/survey/' . $subform 
                );
                $session->currentPage = $this->view->url() . '/' . $subform;
            }
            else
            {
                $this->_pgenSession->surveyPages [$subform] = Array (
                        'url' => $baseURL . '/survey/' . $subform 
                );
                $session->currentPage = $this->view->url() . '/' . $subform;
                break;
            }
        }
        
        // if form is still complete after checking all the subforms, add the
        // verify page to the side menu as well as allow user to view
        // verification page.
        if ($formComplete)
        {
            $this->_pgenSession->surveyPages ['verify'] = array (
                    'url' => $this->view->url() . '/verify' 
            );
            $session->verify = true;
            $session->currentPage = $this->view->url() . '/verify';
        }
        $this->view->visitedPages = $this->_pgenSession->surveyPages;
    }

    public function getForm ()
    {
        if (null === $this->_form)
        {
            $this->_form = new Proposalgen_Form_Survey();
        }
        return $this->_form;
    }

    /**
     * Get the session namespace we're using
     *
     * @return Zend_Session_Namespace
     */
    public function getSessionNamespace ()
    {
        if (null === $this->_session)
        {
            $this->_session = new Zend_Session_Namespace($this->_namespace);
        }
        return $this->_session;
    }

    /**
     * Checks to see is the question has already been answered and the current
     * answer
     * should be overwritten or a new record should be added.
     */
    public function isAnswered ($reportID, $questionID, $table)
    {
        // The report id and question id form a unique identifier in each of the
        // answer tables
        // If there is already a record in the database with that unique
        // identifier, then the user
        // has already answered the question and is now changing the answer, so
        // the system needs to update
        // that record.
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try
        {
            $select = $table->select()
                ->where('report_id = ?', $reportID)
                ->where('question_id = ?', $questionID);
            $row = $table->fetchRow($select);
            $db->commit();
        
        }
        catch ( Zend_Db_Exception $e )
        {
            $db->rollback();
        
        }
        catch ( Exception $e )
        {
            $db->rollback();
        
        }
        
        if ($row == null)
        {
            return 0;
        }
        else
        {
            return 1;
        }
    }

    /**
     * Get a list of forms already stored in the session
     *
     * @return array
     */
    public function getStoredForms ()
    {
        $stored = array ();
        foreach ( $this->getSessionNamespace() as $key => $value )
        {
            $stored [] = $key;
        }
        return $stored;
    }

    /**
     * Get list of all subforms available
     *
     * @return array
     */
    public function getPotentialForms ()
    {
        return array_keys($this->getForm()->getSubForms());
    }

    /**
     * Get the next sub form to display
     *
     * @return False if not found, -1 if $currFormName is last item, or the next
     *         form
     */
    public function getNextSubFormFromCurrent ($currFormName)
    {
        $potentialForms = $this->getPotentialForms();
        $nextFormName = false;
        $grabNext = false;
        
        foreach ( $potentialForms as $name )
        {
            if ($grabNext == true)
            {
                $nextFormName = $this->getForm()->getSubForm($name);
                break;
            }
            if ($name == $currFormName)
            {
                $grabNext = true;
            }
        }
        
        if ($grabNext == true && $nextFormName == false)
        {
            // echo "reutrned 1";
            return - 1;
        }
        else
        {
            // echo "reutrned ".$nextFormName;
            return $nextFormName;
        }
    }

    /**
     * Get the previous subform to display
     *
     * @return Subform name|false
     */
    public function getPrevSubForm ($currFormName)
    {
        $currFormName = strtolower($currFormName);
        $potentialForms = $this->getPotentialForms();
        $prevFormName = false;
        
        foreach ( $potentialForms as $name )
        {
            if ($name == $currFormName)
            {
                return $prevFormName;
            }
            $prevFormName = $name;
        }
        return $prevFormName;
    }

    /**
     * Is the sub form valid?
     *
     * @param $subForm Zend_Form_SubForm           
     * @param $data array           
     * @return bool
     */
    public function subFormIsValid (Zend_Form_SubForm $subForm, array $data, $range)
    {
        $session = new Zend_Session_Namespace('report');
        $reportsTable = new Proposalgen_Model_DbTable_Reports();
        $reportData = $session->reportData;
        $db = Zend_Db_Table::getDefaultAdapter();
        $name = $subForm->getName();
        
        if ($subForm->isValid($data) && $range)
        {
            // creating a new entry in the report table
            if ($session->report_id == null)
            {
                $session->report_id = $reportsTable->insert($reportData);
            }
            $this->getSessionNamespace()->$name = $subForm->getValues(); // VERY
                                                                         // KEY
                                                                         // POINT!!!!!
                                                                         
            // getting the report id from the session.
            $reportID = $session->report_id;
            
            $reportTable = new Proposalgen_Model_DbTable_Reports();
            $textAnswersTable = new Proposalgen_Model_DbTable_TextAnswers();
            $numericAnswersTable = new Proposalgen_Model_DbTable_NumericAnswers();
            $dateAnswersTable = new Proposalgen_Model_DbTable_DateAnswers();
            
            $values = $subForm->getValues();
            $new_array = reset($values);
            $arrayValues = array_values($new_array);
            $arrayKeys = array_keys($new_array);
            
            $db->beginTransaction();
            try
            {
                // going through each element in the sub form to get both the
                // elements
                // question id and the datatype. The datatype is also used to
                // determine the
                // answer table to use.
                for($counter = 0; $counter < sizeof($new_array); $counter ++)
                {
                    $tmtw = $subForm->getElement($arrayKeys [$counter])->getAttrib('tmtw');
                    if ($tmtw)
                    {
                        if ($tmtw == 'numeric')
                        {
                            $table = $numericAnswersTable;
                            $dataType = 'numeric_answer';
                        }
                        else if ($tmtw == 'date')
                        {
                            $table = $dateAnswersTable;
                            $dataType = 'date_answer';
                        }
                        else
                        {
                            $table = $textAnswersTable;
                            $dataType = 'textual_answer';
                        }
                        
                        // building an array of data to save into the answer
                        // table.
                        $saveVal = $arrayValues [$counter];
                        $question_id = $subForm->getElement($arrayKeys [$counter])->getAttrib('id');
                        
                        if ($question_id == 17)
                        {
                            // check 17a selection
                            if ($arrayValues [$counter - 1] == "Times per month")
                            {
                                $saveVal = $arrayValues [3];
                            }
                            else
                            {
                                if ($arrayValues [$counter - 1] == "Daily")
                                {
                                    $saveVal = 22;
                                }
                                else if ($arrayValues [$counter - 1] == "Weekly")
                                {
                                    $saveVal = 4;
                                }
                            }
                        }
                        
                        $answerData = array (
                                'question_id' => $question_id, 
                                'report_id' => $reportID, 
                                $dataType => $saveVal 
                        );
                        
                        // adding a row to the answer table given the report id
                        // (saved in session) and the current question. If the
                        // question already has
                        // an answer, the answer is updated to the new value
                        // instead of inserting a new row.
                        $where = $table->getAdapter()->quoteInto('report_id = ' . $reportID . ' AND question_id = ?', $question_id, 'INTEGER');
                        if ($this->isAnswered($reportID, $question_id, $table))
                        {
                            $table->update($answerData, $where);
                        }
                        else
                        {
                            $table->insert($answerData);
                        }
                        
                        // adding the company name from the survey to the
                        // report.
                        if ($question_id == 4)
                        {
                            $reportData = array (
                                    'customer_company_name' => $arrayValues [$counter] 
                            );
                            $where = $reportTable->getAdapter()->quoteInto('report_id = ?', $reportID, 'INTEGER');
                            $reportTable->update($reportData, $where);
                        }
                    }
                }
                $db->commit();
            
            }
            catch ( Zend_Db_Exception $e )
            {
                $db->rollback();
            
            }
            catch ( Exception $e )
            {
                $db->rollback();
            
            }
            
            if ($subForm->getName() == 'hardware')
            {
                $session->verify = true;
            }
            
            if ($range)
            {
                return true;
            }
        }
        return false;
    }
    
    // get formdata for a given subform from the session namespace
    // returns null if no form data is found
    public function getSubFormDataFromNameSpace ($form, $namespace)
    {
        $subformData = null;
        
        foreach ( $namespace as $currNameSpace )
        :
            foreach ( $currNameSpace as $currForm => $data )
            :
                if ($currForm == $form)
                {
                    // form data found
                    $subformData = $data;
                    break;
                }
            endforeach
            ;
        endforeach
        ;
        
        return $subformData;
    } // end getSubFormDataFromNameSpace
      
    // return true if form exists, false if it doesn't
    public function doesSubFormExist ($subFormName)
    {
        $exists = false;
        $potentialForms = $this->getPotentialForms();
        
        if (in_array($subFormName, $potentialForms))
        {
            $exists = true;
        }
        
        return $exists;
    }

    public function getCurrentSubForm ()
    {
        $request = $this->getRequest();
        
        if (! $request->isPost())
        {
            return false;
        } // end if
        
        foreach ( $this->getPotentialForms() as $name )
        {
            if ($data = $request->getPost($name, false))
            {
                if (is_array($data))
                {
                    return $this->getForm()->getSubForm($name);
                    break;
                } // end if
            } // end if
        } // end foreach
        
        return false;
    } // end getCurrentSubForm
    
    public function allowedToVisitForm ($subFormName)
    {
        $subFormName = strtolower($subFormName);
        
        if ($subFormName == "verify")
        {
            foreach ( $this->getPotentialForms() as $name )
            {
                $previousSubForm = $name;
            }
        }
        else
        {
            if (! in_array($subFormName, $this->getPotentialForms()))
            {
                // not a valid form name
                return false;
            }
            else
            {
                $previousSubForm = $this->getPrevSubForm($subFormName);
            }
        }
        
        if ($previousSubForm != false)
        {
            foreach ( $this->getStoredForms() as $name )
            {
                if ($previousSubForm == $name)
                {
                    // return true if the previous subform is stored already
                    return true;
                } // end
            } // end foreach
        }
        else
        {
            // return true if this is the first subform page
            return true;
        }
        
        // return false if the form exists, but we should not be viewing it
        return false;
    }

    /**
     * The index action is a start point for the survey.
     * This page simply
     * gives a description of what the survey is for, and allows them to
     * start a new survey, which will be part of a new report.
     *
     * @author Chris Garrah
     */
    public function indexAction ()
    {
        // restting the session that holds the visited pages each time a user
        // starts/or selects a new report.
        // Necessary because otherwise a user could get to the verification page
        // of one report, then start a new report
        // and the link to the verification page will still be visible.
        
        $this->_pgenSession->surveyPages = null;
        $this->getCompletedForms();
        
        // set up menu
        $menu = new Custom_Common();
        $menu_array = $menu->build_menu();
        
        if (count($menu_array) == 0)
        {
            $this->view->visitedPages = $this->_pgenSession->surveyPages;
        }
        else
        {
            $this->view->visitedPages = $menu_array;
        }
        $session = new Zend_Session_Namespace('report');
        
        $this->render('start');
        
        header('Location: ' . $session->currentPage);
    }

    /**
     * The company action is responsible for asking the user for information
     * about the
     * company
     *
     * @author Chris Garrah
     */
    public function companyAction ()
    {
        $this->view->controller = "survey";
        $this->_pgenSession->surveyPages = null;
        $this->getCompletedForms();
        $subform = $this->getForm()->getSubForm('company');
        $this->view->formTitle = "Company Information";
        
        if ($this->canViewForm($this->getPrevSubForm('General')))
        {
            $this->view->companyName = $this->getReportCompanyName();
        }
        
        if (! $this->allowedToVisitForm("Company"))
        {
            // INSERT CODE TO REDIRECT TO THE LAST VIEWABLE FORM
            // ******************************************************
            throw new Exception("YOU CANT VIEW THIS FORM!");
        } // end if
        
        if (! isset($this->_pgenSession->surveyPages ['company']))
        {
            $this->_pgenSession->surveyPages ['company'] = array (
                    'url' => $this->view->url() 
            );
        }
        
        $this->regenerateMenu(null, 'company');
        
        return $this->processForm('company', 'company', 'index');
    } // end company action
    
    /**
     * The general action is responsible for asking the first set of questions
     * to the user.
     * This page contains a form with general questions about
     * the user's existing printer fleet.
     *
     * @author Chris Garrah
     */
    public function generalAction ()
    {
        $this->view->controller = "survey";
        $this->view->formTitle = "General";
        $this->view->companyName = $this->getReportCompanyName();
        
        /*
         * if (!$this->allowedToVisitForm($this->view->formTitle)){ //INSERT
         * CODE TO REDIRECT TO THE LAST VIEWABLE FORM
         * //****************************************************** throw new
         * Exception("YOU CANT VIEW THIS FORM!"); } // end if
         */
        
        if (! $this->canViewForm($this->getPrevSubForm($this->view->formTitle)))
        {
            // INSERT CODE TO REDIRECT TO THE LAST VIEWABLE FORM
            throw new Exception("YOU CANT VIEW THIS FORM!");
        }
        
        if (! isset($this->_pgenSession->surveyPages ['general']))
        {
            $this->_pgenSession->surveyPages ['general'] = array (
                    'url' => $this->view->url() 
            );
        }
        
        $this->regenerateMenu('company', 'general');
        
        return $this->processForm('general', 'general', 'index');
    } // end general action
    
    /**
     * The finance action will present a form with questions regarding the
     * financial aspects of the users existing printer fleet
     *
     * @author Chris Garrah
     */
    public function financeAction ()
    {
        $this->view->controller = "survey";
        $this->view->formTitle = "Finance";
        $this->view->companyName = $this->getReportCompanyName();
        
        /*
         * if (!$this->allowedToVisitForm($this->view->formTitle)){ //INSERT
         * CODE TO REDIRECT TO THE LAST VIEWABLE FORM throw new Exception("YOU
         * CANT VIEW THIS FORM!"); }
         */
        
        if (! $this->canViewForm($this->getPrevSubForm($this->view->formTitle)))
        {
            // INSERT CODE TO REDIRECT TO THE LAST VIEWABLE FORM
            throw new Exception("YOU CANT VIEW THIS FORM!");
        }
        
        if (! isset($this->_pgenSession->surveyPages ['finance']))
        {
            $this->_pgenSession->surveyPages ['finance'] = array (
                    'url' => $this->view->url() 
            );
        }
        
        $this->regenerateMenu('general', 'finance');
        
        return $this->processForm('finance', 'finance', 'index');
    }

    /**
     * The purchasing action will present a form with questions regarding the
     * purchasing aspects of the users existing printer fleet
     *
     * @author Chris Garrah
     */
    public function purchasingAction ()
    {
        $this->view->controller = "survey";
        $this->view->formTitle = "Purchasing";
        $this->view->companyName = $this->getReportCompanyName();
        
        // if (!$this->allowedToVisitForm($this->view->formTitle)){
        // INSERT CODE TO REDIRECT TO THE LAST VIEWABLE FORM
        // throw new Exception("YOU CANT VIEW THIS FORM!");
        // }
        
        if (! $this->canViewForm($this->getPrevSubForm($this->view->formTitle)))
        {
            // INSERT CODE TO REDIRECT TO THE LAST VIEWABLE FORM
            throw new Exception("YOU CANT VIEW THIS FORM!");
        }
        
        if (! isset($this->_pgenSession->surveyPages ['purchasing']))
        {
            $this->_pgenSession->surveyPages ['purchasing'] = array (
                    'url' => $this->view->url() 
            );
        }
        
        $this->regenerateMenu('finance', 'purchasing');
        
        return $this->processForm('purchasing', 'purchasing', 'index');
    } // end purchasing action
    
    /**
     * The it action will present a form with questions regarding the
     * it support staff for their existing printer fleet
     *
     * @author Chris Garrah
     */
    public function itAction ()
    {
        $this->view->controller = "survey";
        $this->view->formTitle = "IT";
        $this->view->companyName = $this->getReportCompanyName();
        
        // if (!$this->allowedToVisitForm($this->view->formTitle)){
        // INSERT CODE TO REDIRECT TO THE LAST VIEWABLE FORM
        // throw new Exception("YOU CANT VIEW THIS FORM!");
        // }
        
        if (! $this->canViewForm($this->getPrevSubForm($this->view->formTitle)))
        {
            // INSERT CODE TO REDIRECT TO THE LAST VIEWABLE FORM
            throw new Exception("YOU CANT VIEW THIS FORM!");
        }
        
        if (! isset($this->_pgenSession->surveyPages ['it']))
        {
            $this->_pgenSession->surveyPages ['it'] = array (
                    'url' => $this->view->url() 
            );
        }
        
        $this->regenerateMenu('purchasing', 'it');
        
        return $this->processForm('it', 'it', 'index');
    } // end it action
    
    /**
     * The users action will present a form with the final set of questions in
     * the survery
     *
     * @author Chris Garrah
     */
    public function usersAction ()
    {
        $this->getDefaultPageCoverage();
        $this->view->controller = "survey";
        $this->view->formTitle = "Users";
        $this->view->companyName = $this->getReportCompanyName();
        // if (!$this->allowedToVisitForm($this->view->formTitle)){
        // INSERT CODE TO REDIRECT TO THE LAST VIEWABLE FORM
        // throw new Exception("YOU CANT VIEW THIS FORM!");
        // }
        
        if (! $this->canViewForm($this->getPrevSubForm($this->view->formTitle)))
        {
            // INSERT CODE TO REDIRECT TO THE LAST VIEWABLE FORM
            throw new Exception("YOU CANT VIEW THIS FORM!");
        }
        
        if (! isset($this->_pgenSession->surveyPages ['users']))
        {
            $this->_pgenSession->surveyPages ['users'] = array (
                    'url' => $this->view->url() 
            );
        }
        
        $this->regenerateMenu('it', 'users');
        
        return $this->processForm('users', 'users', 'index');
    } // end users action
    
    /**
     * THe verify action will display all of the information that the users
     * entered in their survey.
     * Users will have the option to go back
     * to a pervious form and change this information.
     *
     * @author Chris Garrah
     */
    public function verifyAction ()
    {
        // set the previous form to assign to the back button
        $this->view->previousPage = "users";
        $this->view->companyName = $this->getReportCompanyName();
        $this->view->formTitle = "Verification";
        
        // if (!$this->allowedToVisitForm('verify')){
        // INSERT CODE TO REDIRECT TO THE LAST VIEWABLE FORM
        // throw new Exception("YOU CANT VIEW THIS FORM!");
        // }
        
        $session = new Zend_Session_Namespace('report');
        if (! $this->canViewForm($this->getPrevSubForm($this->view->formTitle)))
        {
            // INSERT CODE TO REDIRECT TO THE LAST VIEWABLE FORM
            throw new Exception("YOU CANT VIEW THIS FORM!");
        }
        
        if (! isset($this->_pgenSession->surveyPages ['verify']))
        {
            $this->_pgenSession->surveyPages ['verify'] = array (
                    'url' => $this->view->url() 
            );
        }
        $report_id = $session->report_id;
        
        $this->regenerateMenu('users', 'verify');
        
        // set that the verification page has been visited, so link can
        // be added to the nav bar
        $this->_pgenSession->__set("verificationVisited", true);
        $this->view->verificationNavURL = $this->_verificationURL;
        
        // get the current report id.
        $session = new Zend_Session_Namespace('report');
        $questionTable = new Proposalgen_Model_DbTable_Questions();
        $textAnswersTable = new Proposalgen_Model_DbTable_TextAnswers();
        $numericAnswersTable = new Proposalgen_Model_DbTable_NumericAnswers();
        $dateAnswersTable = new Proposalgen_Model_DbTable_DateAnswers();
        $questionSetQuestionsTable = new Proposalgen_Model_DbTable_QuestionSetQuestions();
        $form = $this->getForm();
        $subforms = $form->getSubforms();
        
        foreach ( $subforms as $subform )
        {
            $values = $subform->getValues();
            $new_array = reset($values);
            $arrayValues = array_values($new_array);
            $arrayKeys = array_keys($new_array);
            
            for($counter = 0; $counter < sizeof($new_array); $counter ++)
            {
                $id = $subform->getElement($arrayKeys [$counter])->getAttrib('id');
                if ($id)
                {
                    if ($subform->getElement($arrayKeys [$counter])->getAttrib('tmtw') == 'numeric')
                    {
                        $table = $numericAnswersTable;
                        $dataType = 'numeric_answer';
                    }
                    else if ($subform->getElement($arrayKeys [$counter])->getAttrib('tmtw') == 'date')
                    {
                        $table = $dateAnswersTable;
                        $dataType = 'date_answer';
                    }
                    else
                    {
                        $table = $textAnswersTable;
                        $dataType = 'textual_answer';
                    }
                    
                    // remove a from id
                    $id = str_replace("a", "", $id);
                    
                    $where = $table->getAdapter()->quoteInto('question_id = ' . $id . ' AND report_id = ?', $report_id, 'INTEGER');
                    $answerRow = $table->fetchRow($where);
                    $value = $answerRow->$dataType;
                    
                    if ($subform->getName() == 'purchasing' && $id == 17)
                    {
                        // update 17a to equal 17
                        if ($value == "Daily")
                        {
                            $this->view->$arrayKeys [$counter] = "22";
                        }
                        else if ($value == "Weekly")
                        {
                            $this->view->$arrayKeys [$counter] = "4";
                        }
                        else
                        {
                            $this->view->$arrayKeys [$counter] = $value;
                        }
                    }
                    else
                    {
                        $this->view->$arrayKeys [$counter] = $value;
                    }
                }
            }
        }
        return $this->render('verification');
    }

    /**
     * This function is called to prepare each action.
     * It will display
     * the correct subform, and fill out the links on each page. We display the
     * verification page once all subforms in the survey have been validated.
     *
     * @author Chris Garrah
     */
    private function processForm ($subFormName, $controllerName, $viewScript)
    {
        $financeArray = array (
                11, 
                12, 
                13, 
                14, 
                15 
        );
        
        $session = new Zend_Session_Namespace('report');
        $reportsTable = new Proposalgen_Model_DbTable_Reports();
        $db = Zend_Db_Table::getDefaultAdapter();
        $prevForm = $this->getPrevSubForm($subFormName);
        $request = $this->getRequest();
        $textAnswersTable = new Proposalgen_Model_DbTable_TextAnswers();
        $numericAnswersTable = new Proposalgen_Model_DbTable_NumericAnswers();
        $dateAnswersTable = new Proposalgen_Model_DbTable_DateAnswers();
        
        $form = $this->getForm();
        $subForm = $form->getSubForm($subFormName);
        
        if (! $request->isPost())
        {
            // No post, form requested from URL, display the desired form
            
            // IF DATA for this form is found, display it
            // $data = $this->getSubFormDataFromNameSpace($subFormName,
            // $this->getSessionNamespace());
            $values = $subForm->getValues();
            $values = reset($values);
            
            // setting up an array of data to save into the database
            if ($session->report_id)
            {
                $arrayValues = array_values($values);
                $arrayKeys = array_keys($values);
                $reportId = $session->report_id;
                
                // going through each element in the sub form to get both the
                // elements
                // question id and the datatype. The datatype is also used to
                // determine the
                // answer table to use.
                
                for($counter = 0; $counter < sizeof($arrayKeys); $counter ++)
                {
                    $questionId = $subForm->getElement($arrayKeys [$counter])->getAttrib('id');
                    
                    if ($subForm->getElement($arrayKeys [$counter])->getAttrib('tmtw') == 'numeric')
                    {
                        $table = $numericAnswersTable;
                        $dataType = 'numeric_answer';
                    
                    }
                    else if ($subForm->getElement($arrayKeys [$counter])->getAttrib('tmtw') == 'date')
                    {
                        $table = $dateAnswersTable;
                        $dataType = 'date_answer';
                    
                    }
                    else
                    {
                        $table = $textAnswersTable;
                        $dataType = 'textual_answer';
                    
                    }
                    
                    // fetching the answer text from the database.
                    $where = $table->getAdapter()->quoteInto('report_id = ' . $reportId . ' AND question_id = ?', $subForm->getElement($arrayKeys [$counter])
                        ->getAttrib('id'), 'INTEGER');
                    $db->beginTransaction();
                    try
                    {
                        $row = $table->fetchRow($where);
                        $db->commit();
                    }
                    catch ( Zend_Db_Exception $e )
                    {
                        $db->rollback();
                    
                    }
                    catch ( Exception $e )
                    {
                        $db->rollback();
                    
                    }
                    
                    if ($row [$dataType] != null)
                    {
                        // filling in the form element with the appropriate data
                        // from the answer table.
                        // if the form element is associated with a currency
                        // value, then the output is set to currency format
                        if (in_array($subForm->getElement($arrayKeys [$counter])->getAttrib('id'), $financeArray) && $subForm->getElement($arrayKeys [$counter])->getAttrib('id') != "11a" && $subForm->getElement($arrayKeys [$counter])->getAttrib('id') != "12a")
                        {
                            $arrayValues [$counter] = money_format('%i', $row [$dataType]);
                        }
                        else
                        {
                            $arrayValues [$counter] = ($row [$dataType]);
                        }
                    
                    }
                
                }
                // building the array of data to save to the database this data
                // array is later used to populate the form elements.
                $data = array_combine($arrayKeys, $arrayValues);
            
            }
            else
            {
                $data = null;
            
            }
            
            $subForm->setAction($session->url . '/survey/' . $controllerName)->setMethod('post');
            
            $form = $this->getForm()->prepareSubForm($subForm);
            
            // set the url for the back button
            $backbtn = $form->getElement('back_button');
            if ($backbtn != null)
            {
                if ($subFormName == 'company')
                {
                    $form->getElement('back_button')->setAttrib('ONCLICK', 'window.location=\'' . $session->url . '\';');
                }
                else
                {
                    $backbtn->setAttrib('ONCLICK', 'window.location=\'' . $this->view->baseUrl() . '/survey/' . $prevForm . '\';');
                }
            }
            
            if ($data != false)
            {
                $form->populate($data);
            }
            
            $this->view->form = $form;
            $this->getForm()
                ->getSubForm($subFormName)
                ->setDecorators(array (
                    'PrepareElements',  // DO not remove PrepareElements
                    array (
                            'ViewScript', 
                            array (
                                    'viewScript' => 'forms/' . $subFormName . 'Form.phtml' 
                            ) 
                    ) 
            ));
            return $this->render($viewScript);
        
        }
        else
        {
            $range = true;
            
            // Form was posted, we must save the posted data, and show the next
            // form
            $submittedSubForm = $this->getCurrentSubForm($subFormName);
            
            // if its the general page, then need to check the goal questions
            // for duplicates.
            if ($subFormName == 'general')
            {
                $formData = $this->_request->getPost();
                $form = new Proposalgen_Form_Survey();
                $range = $form->set_validation($formData);
            }
            
            // if subform is valid, data will be saved in session namespace,
            // otherwise redisplay
            if ($this->subFormIsValid($submittedSubForm, $this->getRequest()
                ->getPost(), $range))
            {
                if ($subFormName == "users")
                {
                    $session->verify = true;
                }
                
                $nextSubForm = $this->getNextSubFormFromCurrent($subFormName);
                
                if ($session->verify && $nextSubForm === - 1)
                {
                    // ENTIRE FORM IS VALID, SO SHOW THE VERFIFICATION SCREEN
                    // NOTE: do not move to verification screen unless we are
                    // currently showing the last form.
                    $this->_redirect('survey/verify');
                
                }
                else
                {
                    // subform valid, entire form not yet valid, so direct to
                    // the next subform
                    if ($nextSubForm === - 1 || $nextSubForm == false)
                    {
                        $var = $session->verify;
                        throw new Exception("Form Error: Could not find the next form to display.");
                    
                    }
                    else
                    {
                        $this->_redirect('survey/' . $nextSubForm->getName());
                    
                    }
                }
            
            }
            else
            { // PUT IF STATEMENT HERE TO CHECK IF ENTIRE FORM IS VALID!!
              // subform is not valid, redisplay the invalid form
              
                $submittedSubForm->setAction($session->url . '/survey/' . $subFormName)->setMethod('post');
                $this->view->form = $this->getForm()->prepareSubForm($submittedSubForm);
                // set the url for the back button
                
                $backbtn = $this->view->form->getElement('back_button');
                if ($backbtn != null)
                {
                    if ($subFormName == 'company')
                    {
                        $backbtn->setAttrib('ONCLICK', 'window.location=\'' . $session->url . '\';');
                    }
                    else
                    {
                        $backbtn->setAttrib('ONCLICK', 'window.location=\'' . $this->view->baseUrl() . '/survey/' . $prevForm . '\';');
                    }
                }
                
                $this->getForm()
                    ->getSubForm($subFormName)
                    ->setDecorators(array (
                        'PrepareElements',  // DO not remove PrepareElements
                        array (
                                'ViewScript', 
                                array (
                                        'viewScript' => 'forms/' . $subFormName . 'Form.phtml' 
                                ) 
                        ) 
                ));
                return $this->render($viewScript);
            
            } // end else
        
        } // end else
    
    } // end function processForm
    
    /**
     * Setting the defualt value of the page coverage in the survey to be the
     * users personal setting.
     * If no user setting exists, then the company's setting is used. If no
     * dealer setting exists, then
     * the default is the system admins values.
     *
     * @author Mike Christie
     */
    public function getDefaultPageCoverage ()
    {
        $session = new Zend_Session_Namespace('report');
        $userTable = new Proposalgen_Model_DbTable_Users();
        $dealerTable = new Proposalgen_Model_DbTable_DealerCompany();
        $where = $userTable->getAdapter()->quoteInto('user_id = ?', $session->userid);
        $row = $userTable->fetchRow($where);
        
        $session->pageCoverageBW = $row->user_estimated_page_coverage_mono;
        $session->pageCoverageColor = $row->user_estimated_page_coverage_color;
        if (! $session->pageCoverageBW)
        {
            $row = $dealerTable->fetchRow('dealer_company_id =' . $session->dealerid);
            $session->pageCoverageBW = $row->dc_estimated_page_coverage_mono;
        }
        if (! $session->pageCoverageBW)
        {
            $row = $dealerTable->fetchRow('company_name = "MASTER"');
            $session->pageCoverageBW = $row->dc_estimated_page_coverage_mono;
        }
        if (! $session->pageCoverageColor)
        {
            $row = $dealerTable->fetchRow('dealer_company_id =' . $session->dealerid);
            $session->pageCoverageColor = $row->dc_estimated_page_coverage_color;
        }
        if (! $session->pageCoverageColor)
        {
            $row = $dealerTable->fetchRow('company_name = "MASTER"');
            $session->pageCoverageColor = $row->dc_estimated_page_coverage_color;
        }
    
    }

    /**
     * This function gets the name of the company the report was prepared for
     */
    public function getReportCompanyName ()
    {
        $session = new Zend_Session_Namespace('report');
        $report_id = $session->report_id;
        $questionTable = new Proposalgen_Model_DbTable_TextAnswers();
        $where = $questionTable->getAdapter()->quoteInto('report_id = ? AND question_id = 4', $report_id, 'INTEGER');
        $row = $questionTable->fetchRow($where);
        
        if ($row ['textual_answer'])
        {
            return $row ['textual_answer'];
        }
        else
        {
            return null;
        }
    }

    /**
     * Regenerates the menu as well as sets the current stage to where we are if
     * we are coming from the previous stage
     * This allows us to get back to the page we are on.
     * EG:
     * $this->regenerateMenu('hardware', 'verify');
     *
     * @param $previousstage string           
     * @param $newstage string           
     */
    public function regenerateMenu ($previousstage, $newstage)
    {
        $session = new Zend_Session_Namespace('report');
        if (isset($session->report_id))
        {
            
            $report = Proposalgen_Model_Mapper_Report::getInstance()->find($session->report_id);
            
            if (is_null($report->getReportStage()) || $report->getReportStage() === $previousstage)
            {
                
                $report->setReportStage($newstage);
                $test = Proposalgen_Model_Mapper_Report::getInstance()->save($report);
            }
            $menu = new Custom_Report_Menu($report);
            $this->view->reportMenu = $menu;
        
        }
    }

} // end class ProfileController
