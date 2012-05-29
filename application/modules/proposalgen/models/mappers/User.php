<?php

class Proposalgen_Model_Mapper_User extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_Users";
    static $_instance;

    /**
     *
     * @return Tangent_Model_Mapper_Abstract
     */
    public static function getInstance ()
    {
        if (! isset(self::$_instance))
        {
            $className = get_class();
            self::$_instance = new $className();
        }
        return self::$_instance;
    }

    /**
     * Maps a database row object to an Application_Model
     *
     * @param Zend_Db_Table_Row $row            
     * @return The appropriate Application_Model
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object = new Proposalgen_Model_User();
            $object->setUserId($row->user_id)
                ->setDealerCompanyId($row->dealer_company_id)
                ->setFirstName($row->firstname)
                ->setLastName($row->lastname)
                ->setUserName($row->username)
                ->setPassword($row->password)
                ->setEmail($row->email)
                ->setTelephone($row->telephone)
                ->setIsActivated($row->is_activated)
                ->setUpdatePassword($row->update_password)
                ->setDateCreated($row->date_created)
                ->setTempPassword($row->temp_password)
                ->setPasswordResetRequested($row->password_reset_requested)
                ->setLastLogin($row->last_login)
                ->setEulaAccepted($row->eula_accepted)
                ->setUserEstimatedPageCoverageMono($row->user_estimated_page_coverage_mono)
                ->setUserEstimatedPageCoverageColor($row->user_estimated_page_coverage_color)
                ->setUserServiceCostPerPage($row->user_service_cost_per_page)
                ->setUserAdminChargePerPage($row->user_admin_charge_per_page)
                ->setUserPricingMargin($row->user_pricing_margin)
                ->setUserMonthlyLeasePayment($row->user_monthly_lease_payment)
                ->setUserDefaultPrinterCost($row->user_default_printer_cost)
                ->setUserLeasedBwPerPage($row->user_leased_bw_per_page)
                ->setUserLeasedColorPerPage($row->user_leased_color_per_page)
                ->setUserMpsBwPerPage($row->user_mps_bw_per_page)
                ->setUserMpsColorPerPage($row->user_mps_color_per_page)
                ->setUserKilowattsPerHour($row->user_kilowatts_per_hour)
                ->setPricingConfigId($row->pricing_config_id)
                ->setFailedLoginAttempts($row->failed_login_attempts)
                ->setLoginRestrictedUntilDate($row->login_restricted_until_date)
                ->setUserDefaultBWTonerCost($row->user_default_BW_toner_cost)
                ->setUserDefaultBWTonerYield($row->user_default_BW_toner_yield)
                ->setUserDefaultColorTonerCost($row->user_default_color_toner_cost)
                ->setUserDefaultColorTonerYield($row->user_default_color_toner_yield)
                ->setUserDefaultThreeColorTonerCost($row->user_default_three_color_toner_cost)
                ->setUserDefaultThreeColorTonerYield($row->user_default_three_color_toner_yield)
                ->setUserDefaultFourColorTonerCost($row->user_default_four_color_toner_cost)
                ->setUserDefaultFourColorTonerYield($row->user_default_four_color_toner_yield)
                ->setFullCompanyLogo($row->full_company_logo)
                ->setCompanyLogo($row->company_logo)
                ->setCompanyReportColor($row->company_report_color)
                ->setUserActualPageCoverageMono($row->user_actual_page_coverage_mono)
                ->setUserActualPageCoverageColor($row->user_actual_page_coverage_color);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a user row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param unknown_type $object            
     */
    public function save (Proposalgen_Model_User $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["user_id"] = $object->getUserId();
            $data ["dealer_company_id"] = $object->getDealerCompanyId();
            $data ["firstname"] = $object->getFirstName();
            $data ["lastname"] = $object->getLastName();
            $data ["username"] = $object->getUserName();
            $data ["password"] = $object->getPassword();
            $data ["email"] = $object->getEmail();
            $data ["telephone"] = $object->getTelephone();
            $data ["is_activated"] = $object->getIsActivated();
            $data ["update_password"] = $object->getUpdatePassword();
            $data ["date_created"] = $object->getDateCreated();
            $data ["temp_password"] = $object->getTempPassword();
            $data ["password_reset_requested"] = $object->getPasswordResetRequested();
            $data ["last_login"] = $object->getLastLogin();
            $data ["eula_accepted"] = $object->getEulaAccepted();
            $data ["user_estimated_page_coverage_mono"] = $object->getUserEstimatedPageCoverageMono();
            $data ["user_estimated_page_coverage_color"] = $object->getUserEstimatedPageCoverageColor();
            $data ["user_actual_page_coverage_mono"] = $object->getUserActualPageCoverageMono();
            $data ["user_actual_page_coverage_color"] = $object->getUserActualPageCoverageColor();
            $data ["user_service_cost_per_page"] = $object->getUserServiceCostPerPage();
            $data ["user_admin_charge_per_page"] = $object->getUserAdminChargePerPage();
            $data ["user_pricing_margin"] = $object->getUserPricingMargin();
            $data ["user_monthly_lease_payment"] = $object->getUserMonthlyLeasePayment();
            $data ["user_monthly_lease_payment"] = $object->getUserDefaultPrinterCost();
            $data ["user_default_printer_cost"] = $object->getUserLeasedBwPerPage();
            $data ["user_leased_color_per_page"] = $object->getUserLeasedColorPerPage();
            $data ["user_mps_bw_per_page"] = $object->getUserMpsBwPerPage();
            $data ["user_mps_color_per_page"] = $object->getUserMpsColorPerPage();
            $data ["user_kilowatts_per_hour"] = $object->getUserKilowattsPerHour();
            $data ["pricing_config_id"] = $object->getPricingConfigId();
            $data ["failed_login_attempts"] = $object->getFailedLoginAttempts();
            $data ["login_restricted_until_date"] = $object->getLoginRestrictedUntilDate();
            $data ["user_default_BW_toner_cost"] = $object->getUserDefaultBWTonerCost();
            $data ["user_default_BW_toner_yield"] = $object->getUserDefaultBWTonerYield();
            $data ["user_default_color_toner_cost"] = $object->getUserDefaultColorTonerCost();
            $data ["user_default_color_toner_yield"] = $object->getUserDefaultColorTonerYield();
            $data ["user_default_three_color_toner_cost"] = $object->getUserDefaultThreeColorTonerCost();
            $data ["user_default_three_color_toner_yield"] = $object->getUserDefaultThreeColorTonerYield();
            $data ["user_default_four_color_toner_cost"] = $object->getUserDefaultFourColorTonerCost();
            $data ["user_default_four_color_toner_yield"] = $object->getUserDefaultFourColorTonerYield();
            $data ["full_company_logo"] = $object->getFullCompanyLogo();
            $data ["company_logo"] = $object->getCompanyLogo();
            $data ["company_report_color"] = $object->getCompanyReportColor();
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }
}