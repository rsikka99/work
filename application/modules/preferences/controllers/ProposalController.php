<?php
/**
 * Class Preferences_ProposalController
 */
class Preferences_ProposalController extends Tangent_Controller_Action
{
    public function indexAction () { /** Do nothing */ }

    public function dealerAction ()
    {
        // Initialize and get the form
        $dealer = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);

        $combinedSettings                    = array_merge($dealer->getDealerSettings()->getAssessmentSettings()->toArray(), $dealer->getDealerSettings()->getSurveySettings()->toArray());
        $assessmentId                        = $dealer->getDealerSettings()->getAssessmentSettings()->toArray()['id'];
        $surveyId                            = $dealer->getDealerSettings()->getSurveySettings()->toArray()['id'];
        $combinedSettings['reportSettingId'] = $assessmentId;
        $combinedSettings['surveySettingId'] = $surveyId;
        $reportSettingFormService            = new Preferences_Service_ReportSetting($combinedSettings);
        $form                                = $reportSettingFormService->getForm();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values  = $request->getPost();
            $success = $reportSettingFormService->update($values);

            if ($success)
            {
                $this->_flashMessenger->addMessage(array('success' => 'Report settings updated successfully'));
            }
            else
            {
                $this->_flashMessenger->addMessage(array('danger' => 'Error saving report settings. Please correct the highlighted errors blow.'));
            }
        }

        $this->view->form = $form;
    }

    /**
     * This is where the admin can edit the system proposal preferences
     */
    public function systemAction ()
    {
        // Initialize and get the form
        $reportSettingFormService = new Preferences_Service_ReportSetting();
        $form                     = $reportSettingFormService->getForm();

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values  = $request->getPost();
            $success = $reportSettingFormService->update($values);

            if ($success)
            {
                $this->_flashMessenger->addMessage(array('success' => 'Report settings updated successfully'));
            }
            else
            {
                $this->_flashMessenger->addMessage(array('danger' => 'Error saving report settings. Please correct the highlighted errors blow.'));
            }
        }

        $this->view->form = $form;
    }

    public function userAction ()
    {
        // Initialize and get the form

        // Dealer
        $dealer                 = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);
        $combinedDealerSettings = array_merge($dealer->getDealerSettings()->getAssessmentSettings()->toArray(), $dealer->getDealerSettings()->getSurveySettings()->toArray());

        // User
        $user                                    = Application_Model_Mapper_User::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->id);
        $combinedUserSettings                    = array_merge($user->getUserSettings()->getAssessmentSettings()->toArray(), $user->getUserSettings()->getSurveySettings()->toArray(), $user->getUserSettings()->getAssessmentSettings()->getTonerRankSets());
        $assessmentId                            = $user->getUserSettings()->getAssessmentSettings()->toArray()['id'];
        $surveyId                                = $user->getUserSettings()->getSurveySettings()->toArray()['id'];
        $combinedUserSettings['reportSettingId'] = $assessmentId;
        $combinedUserSettings['surveySettingId'] = $surveyId;
        $reportSettingFormService                = new Preferences_Service_ReportSetting($combinedUserSettings);

        $form = $reportSettingFormService->getFormWithDefaults($combinedDealerSettings);

        $request = $this->getRequest();

        if ($request->isPost())
        {
            $values  = $request->getPost();
            $success = $reportSettingFormService->update($values);

            if ($success)
            {
                $this->_flashMessenger->addMessage(array('success' => 'Report settings updated successfully'));
            }
            else
            {
                $this->_flashMessenger->addMessage(array('danger' => 'Error saving report settings. Please correct the highlighted errors blow.'));
            }
        }

        $this->view->form = $form;
    }
}