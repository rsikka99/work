<?php
class Preferences_ProposalController extends Tangent_Controller_Action
{
    /**
     * This is where the user can edit their proposal preferences
     */
    public function indexAction ()
    {
        $this->view->title = 'Manage My Settings';
        $db                = Zend_Db_Table::getDefaultAdapter();
        $message           = '';

        // Get system overrides
        $userId         = Zend_Auth::getInstance()->getIdentity()->id;
        $reportSettings = Proposalgen_Model_Mapper_Report_Setting::getInstance()->fetchUserReportSetting($userId);
        $surveySettings = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->fetchUserSurveySetting($userId);
        $pricingConfigs = Proposalgen_Model_Mapper_PricingConfig::getInstance()->fetchAll();
        $form           = new Proposalgen_Form_Settings_User();
        // Add all the pricing configs
        /* @var $pricingConfig Proposalgen_Model_PricingConfig */
        foreach ($pricingConfigs as $pricingConfig)
        {
            $form->getElement('assessmentPricingConfigId')->addMultiOption($pricingConfig->pricingConfigId, ($pricingConfig->pricingConfigId !== 1) ? $pricingConfig->configName : "");
        }


        // Populate the values in the form.
        $form->populate($surveySettings->toArray());
        $form->populate($reportSettings->toArray());

        // Get the system defaults and unset the id's.  Merge the two system settings and set the dropdowns.
        $systemReportSettings      = Proposalgen_Model_Mapper_Report_Setting::getInstance()->find(1);
        $systemSurveySetting       = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->find(1);
        $systemReportSettingsArray = $systemReportSettings->toArray();
        $systemSurveySettingArray  = $systemSurveySetting->toArray();
        unset($systemReportSettingsArray ['id']);
        unset($systemSurveySettingArray ['id']);
        $defaultSettings = array_merge($systemReportSettingsArray, $systemSurveySettingArray);

        if ($defaultSettings ["assessmentPricingConfigId"] !== Proposalgen_Model_PricingConfig::NONE)
        {
            $defaultSettings ["assessmentPricingConfigId"] = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find($defaultSettings ["assessmentPricingConfigId"])->configName;
        }
        else
        {
            $defaultSettings ["assessmentPricingConfigId"] = "";
        }


        if ($this->_request->isPost())
        {
            // get form values
            $formData = $this->_request->getPost();

            if ($form->isValid($formData))
            {
                if (isset($formData ['save_settings']))
                {
                    $db->beginTransaction();
                    try
                    {
                        // Make all empty values = null
                        foreach ($formData as &$value)
                        {
                            if (strlen($value) === 0)
                            {
                                $value = new Zend_Db_Expr("NULL");
                            }
                        }

                        // Save page coverage settings (survey settings)
                        $surveySettings->populate($formData);
                        Proposalgen_Model_Mapper_Survey_Setting::getInstance()->save($surveySettings, $surveySettings->id);

                        // Save report settings (all other)
                        $reportSettings->populate($formData);
                        Proposalgen_Model_Mapper_Report_Setting::getInstance()->save($reportSettings, $reportSettings->id);

                        $this->_helper->flashMessenger(array("success" => "Your settings have been updated."));
                        $db->commit();
                    }
                    catch (Zend_Db_Exception $e)
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array("error" => "An error occurred while saving your settings.{$e->getMessage()}"));
                    }
                    catch (Exception $e)
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array("error" => "An error occurred while saving your settings."));
                    }
                }
            }
            else
            {
                $this->_helper->flashMessenger(array("error" => "Please review the errors below."));
                $form->populate($formData);
            }
        }


        // add form to page
        $form->setDecorators(array(
                                  array(
                                      'ViewScript',
                                      array(
                                          'viewScript' => 'forms/settings/user.phtml',
                                          'dealerData' => $defaultSettings,
//                                          'dealerName' => $dealerName,
                                          'message'    => $message
                                      )
                                  )
                             ));
        $this->view->settingsForm = $form;
    }

    /**
     * This is where the admin can edit the system proposal preferences
     */
    public function systemAction ()
    {
        $this->view->title = 'Manage Settings';
        $db                = Zend_Db_Table::getDefaultAdapter();

        // Row #1 of Report Settings has all the system defaults
        $systemReportSettings = Proposalgen_Model_Mapper_Report_Setting::getInstance()->find(1);
        $systemSurveySettings = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->find(1);
        $form                 = new Proposalgen_Form_Settings_SystemAdmin();

        if ($this->_request->isPost())
        {
            // Get the data that has been posted
            $formData = $this->_request->getPost();

            if ($form->isValid($formData))
            {
                if (isset($formData ['save_settings']))
                {
                    $db->beginTransaction();
                    try
                    {
                        // Make all empty values = null
                        foreach ($formData as &$value)
                        {
                            if (strlen($value) === 0)
                            {
                                $value = null;
                            }
                        }
                        // Save page coverage settings (survey settings)
                        $systemSurveySettings->populate($formData);
                        Proposalgen_Model_Mapper_Survey_Setting::getInstance()->save($systemSurveySettings, $systemSurveySettings->id);
                        // Save report settings (all other)
                        $systemReportSettings->populate($formData);
                        Proposalgen_Model_Mapper_Report_Setting::getInstance()->save($systemReportSettings, $systemReportSettings->id);

                        $this->_helper->flashMessenger(array(
                                                            "success" => "Your settings have been updated."
                                                       ));
                        $db->commit();
                    }
                    catch (Zend_Db_Exception $e)
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array("error" => "An error occured while saving your settings."));
                    }
                    catch (Exception $e)
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array("error" => "An error occured while saving your settings."));
                    }
                }
            }
            else
            {
                $this->_helper->flashMessenger(array(
                                                    "error" => "Please review the errors below."
                                               ));
                $form->populate($formData);
            }
        }

        // Add form to page
        $form->setDecorators(array(array('ViewScript', array('viewScript' => 'forms/settings/systemadmin.phtml'))));
        // Populate the form wif data
        $form->populate($systemReportSettings->toArray());
        $form->populate($systemSurveySettings->toArray());
        $this->view->settingsForm = $form;
    }
}