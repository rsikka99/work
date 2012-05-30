<?php

/**
 * Class Application_Model_Survey_Setting
 */
class Proposalgen_Model_Survey_Setting extends Proposalgen_Model_DbModel_Survey_Setting
{

    public function getSettingsAsArray ()
    {
        $settings = array (
                "page_coverage_color" => $this->getPageCoverageColor(), 
                "page_coverage_mono" => $this->getPageCoverageMono() 
        );
        return $settings;
    }

    public function ApplyOverride ($settings)
    {
        $OverrideSettings = array ();
        if ($settings instanceof Proposalgen_Model_Survey_Setting)
        {
            $OverrideSettings = $settings->getSettingsAsArray();
        }
        else
        {
            if (is_array($settings))
            {
                $OverrideSettings = $settings;
            }
            else
            {
                throw new Exception("You must pass an array or instance of " . get_class($this));
            }
        }
        
        $newSettings = array ();
        foreach ( $OverrideSettings as $key => $setting )
        {
            if (! is_null($setting))
            {
                $newSettings [$key] = $setting;
            }
        }
        
        // A bit of a hack, taking advantage that we use the db column names to
        // identify
        // settings within a form. This way we can override settings with it
        $this->setOptionsFromDb($newSettings);
    }
}