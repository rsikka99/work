<?php

/**
 * Class Proposalgen_Model_User
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_User extends Tangent_Model_Abstract
{
    static $CurrentUser;
    static $CurrentUserId = 1;
    static $MasterUser;
    static $MasterUserId = 1;
    
    // Database Fields
    protected $UserId;
    protected $DealerCompanyId;
    protected $FirstName;
    protected $LastName;
    protected $UserName;
    protected $Password;
    protected $Email;
    protected $Telephone;
    protected $IsActivated;
    protected $UpdatePassword;
    protected $DateCreated;
    protected $TempPassword;
    protected $PasswordResetRequested;
    protected $LastLogin;
    protected $EulaAccepted;
    protected $UserEstimatedPageCoverageMono;
    protected $UserEstimatedPageCoverageColor;
    protected $UserServiceCostPerPage;
    protected $UserAdminChargePerPage;
    protected $UserPricingMargin;
    protected $UserMonthlyLeasePayment;
    protected $UserDefaultPrinterCost;
    protected $UserLeasedBwPerPage;
    protected $UserLeasedColorPerPage;
    protected $UserMpsBwPerPage;
    protected $UserMpsColorPerPage;
    protected $UserKilowattsPerHour;
    protected $PricingConfigId;
    protected $FailedLoginAttempts;
    protected $LoginRestrictedUntilDate;
    protected $UserDefaultBWTonerCost;
    protected $UserDefaultBWTonerYield;
    protected $UserDefaultColorTonerCost;
    protected $UserDefaultColorTonerYield;
    protected $UserDefaultThreeColorTonerCost;
    protected $UserDefaultThreeColorTonerYield;
    protected $UserDefaultFourColorTonerCost;
    protected $UserDefaultFourColorTonerYield;
    protected $FullCompanyLogo;
    protected $CompanyLogo;
    protected $CompanyReportColor;
    
    protected $UserActualPageCoverageMono;
    protected $UserActualPageCoverageColor;
    
    protected $ReportSettings;

    /**
     * @return Proposalgen_Model_User $CurrentUser
     */
    public static function getCurrentUser ()
    {
        if (! isset(Proposalgen_Model_User::$CurrentUser))
        {
            Proposalgen_Model_User::$CurrentUser = Proposalgen_Model_Mapper_User::getInstance()->find(self::getCurrentUserId());
        }
        return Proposalgen_Model_User::$CurrentUser;
    }

    /**
     * @param Proposalgen_Model_User $CurrentUser
     */
    public static function setCurrentUser ($CurrentUser)
    {
        Proposalgen_Model_User::$CurrentUser = $CurrentUser;
    }

    /**
     * @return Integer $CurrentUserId
     */
    public static function getCurrentUserId ()
    {
        if (! isset(Proposalgen_Model_User::$CurrentUserId))
        {
            Proposalgen_Model_User::$CurrentUserId = null;
        }
        return Proposalgen_Model_User::$CurrentUserId;
    }

    /**
     * @param Integer $CurrentUserId
     */
    public static function setCurrentUserId ($CurrentUserId)
    {
        Proposalgen_Model_User::$CurrentUserId = $CurrentUserId;
    }

    /**
     * @return Proposalgen_Model_User $MasterUser
     */
    public static function getMasterUser ()
    {
        if (! isset(Proposalgen_Model_User::$MasterUser))
        {
            Proposalgen_Model_User::$MasterUser = Proposalgen_Model_Mapper_User::getInstance()->find(self::getMasterUserId());
        }
        return Proposalgen_Model_User::$MasterUser;
    }

    /**
     * @param Proposalgen_Model_User $MasterUser
     */
    public static function setMasterUser ($MasterUser)
    {
        Proposalgen_Model_User::$MasterUser = $MasterUser;
    }

    /**
     * @return Integer $MasterUserId
     */
    public static function getMasterUserId ()
    {
        if (! isset(Proposalgen_Model_User::$MasterUserId))
        {
            
            Proposalgen_Model_User::$MasterUserId = null;
        }
        return Proposalgen_Model_User::$MasterUserId;
    }

    /**
     * @param Integer $MasterUserId
     */
    public static function setMasterUserId ($MasterUserId)
    {
        Proposalgen_Model_User::$MasterUserId = $MasterUserId;
        return $this;
    }

    /**
     * @return the $UserId
     */
    public function getUserId ()
    {
        if (! isset($this->UserId))
        {
            
            $this->UserId = null;
        }
        return $this->UserId;
    }

    /**
     * @param field_type $UserId
     */
    public function setUserId ($UserId)
    {
        $this->UserId = $UserId;
        return $this;
    }

    /**
     * @return the $DealerCompanyId
     */
    public function getDealerCompanyId ()
    {
        if (! isset($this->DealerCompanyId))
        {
            
            $this->DealerCompanyId = null;
        }
        return $this->DealerCompanyId;
    }

    /**
     * @param field_type $DealerCompanyId
     */
    public function setDealerCompanyId ($DealerCompanyId)
    {
        $this->DealerCompanyId = $DealerCompanyId;
        return $this;
    }

    /**
     * @return the $FirstName
     */
    public function getFirstName ()
    {
        if (! isset($this->FirstName))
        {
            
            $this->FirstName = null;
        }
        return $this->FirstName;
    }

    /**
     * @param field_type $FirstName
     */
    public function setFirstName ($FirstName)
    {
        $this->FirstName = $FirstName;
        return $this;
    }

    /**
     * @return the $LastName
     */
    public function getLastName ()
    {
        if (! isset($this->LastName))
        {
            
            $this->LastName = null;
        }
        return $this->LastName;
    }

    /**
     * @param field_type $LastName
     */
    public function setLastName ($LastName)
    {
        $this->LastName = $LastName;
        return $this;
    }

    /**
     * @return the $UserName
     */
    public function getUserName ()
    {
        if (! isset($this->UserName))
        {
            
            $this->UserName = null;
        }
        return $this->UserName;
    }

    /**
     * @param field_type $UserName
     */
    public function setUserName ($UserName)
    {
        $this->UserName = $UserName;
        return $this;
    }

    /**
     * @return the $Password
     */
    public function getPassword ()
    {
        if (! isset($this->Password))
        {
            
            $this->Password = null;
        }
        return $this->Password;
    }

    /**
     * @param field_type $Password
     */
    public function setPassword ($Password)
    {
        $this->Password = $Password;
        return $this;
    }

    /**
     * @return the $Email
     */
    public function getEmail ()
    {
        if (! isset($this->Email))
        {
            
            $this->Email = null;
        }
        return $this->Email;
    }

    /**
     * @param field_type $Email
     */
    public function setEmail ($Email)
    {
        $this->Email = $Email;
        return $this;
    }

    /**
     * @return the $Telephone
     */
    public function getTelephone ()
    {
        if (! isset($this->Telephone))
        {
            
            $this->Telephone = null;
        }
        return $this->Telephone;
    }

    /**
     * @param field_type $Telephone
     */
    public function setTelephone ($Telephone)
    {
        $this->Telephone = $Telephone;
        return $this;
    }

    /**
     * @return the $IsActivated
     */
    public function getIsActivated ()
    {
        if (! isset($this->IsActivated))
        {
            
            $this->IsActivated = null;
        }
        return $this->IsActivated;
    }

    /**
     * @param field_type $IsActivated
     */
    public function setIsActivated ($IsActivated)
    {
        $this->IsActivated = $IsActivated;
        return $this;
    }

    /**
     * @return the $UpdatePassword
     */
    public function getUpdatePassword ()
    {
        if (! isset($this->UpdatePassword))
        {
            
            $this->UpdatePassword = null;
        }
        return $this->UpdatePassword;
    }

    /**
     * @param field_type $UpdatePassword
     */
    public function setUpdatePassword ($UpdatePassword)
    {
        $this->UpdatePassword = $UpdatePassword;
        return $this;
    }

    /**
     * @return the $DateCreated
     */
    public function getDateCreated ()
    {
        if (! isset($this->DateCreated))
        {
            
            $this->DateCreated = null;
        }
        return $this->DateCreated;
    }

    /**
     * @param field_type $DateCreated
     */
    public function setDateCreated ($DateCreated)
    {
        $this->DateCreated = $DateCreated;
        return $this;
    }

    /**
     * @return the $TempPassword
     */
    public function getTempPassword ()
    {
        if (! isset($this->TempPassword))
        {
            
            $this->TempPassword = null;
        }
        return $this->TempPassword;
    }

    /**
     * @param field_type $TempPassword
     */
    public function setTempPassword ($TempPassword)
    {
        $this->TempPassword = $TempPassword;
        return $this;
    }

    /**
     * @return the $PasswordResetRequested
     */
    public function getPasswordResetRequested ()
    {
        if (! isset($this->PasswordResetRequested))
        {
            
            $this->PasswordResetRequested = null;
        }
        return $this->PasswordResetRequested;
    }

    /**
     * @param field_type $PasswordResetRequested
     */
    public function setPasswordResetRequested ($PasswordResetRequested)
    {
        $this->PasswordResetRequested = $PasswordResetRequested;
        return $this;
    }

    /**
     * @return the $LastLogin
     */
    public function getLastLogin ()
    {
        if (! isset($this->LastLogin))
        {
            
            $this->LastLogin = null;
        }
        return $this->LastLogin;
    }

    /**
     * @param field_type $LastLogin
     */
    public function setLastLogin ($LastLogin)
    {
        $this->LastLogin = $LastLogin;
        return $this;
    }

    /**
     * @return the $EulaAccepted
     */
    public function getEulaAccepted ()
    {
        if (! isset($this->EulaAccepted))
        {
            
            $this->EulaAccepted = null;
        }
        return $this->EulaAccepted;
    }

    /**
     * @param field_type $EulaAccepted
     */
    public function setEulaAccepted ($EulaAccepted)
    {
        $this->EulaAccepted = $EulaAccepted;
        return $this;
    }

    /**
     * @return the $UserEstimatedPageCoverageMono
     */
    public function getUserEstimatedPageCoverageMono ()
    {
        if (! isset($this->UserEstimatedPageCoverageMono))
        {
            
            $this->UserEstimatedPageCoverageMono = null;
        }
        return $this->UserEstimatedPageCoverageMono;
    }

    /**
     * @param field_type $UserEstimatedPageCoverageMono
     */
    public function setUserEstimatedPageCoverageMono ($UserEstimatedPageCoverageMono)
    {
        $this->UserEstimatedPageCoverageMono = $UserEstimatedPageCoverageMono;
        return $this;
    }

    /**
     * @return the $UserEstimatedPageCoverageColor
     */
    public function getUserEstimatedPageCoverageColor ()
    {
        if (! isset($this->UserEstimatedPageCoverageColor))
        {
            
            $this->UserEstimatedPageCoverageColor = null;
        }
        return $this->UserEstimatedPageCoverageColor;
    }

    /**
     * @param field_type $UserEstimatedPageCoverageColor
     */
    public function setUserEstimatedPageCoverageColor ($UserEstimatedPageCoverageColor)
    {
        $this->UserEstimatedPageCoverageColor = $UserEstimatedPageCoverageColor;
        return $this;
    }

    /**
     * @return the $UserAdminChargePerPage
     */
    public function getUserAdminChargePerPage ()
    {
        if (! isset($this->UserAdminChargePerPage))
        {
            
            $this->UserAdminChargePerPage = null;
        }
        return $this->UserAdminChargePerPage;
    }

    /**
     * @param field_type $UserAdminChargePerPage
     */
    public function setUserAdminChargePerPage ($UserAdminChargePerPage)
    {
        $this->UserAdminChargePerPage = $UserAdminChargePerPage;
        return $this;
    }

    /**
     * @return the $UserPricingMargin
     */
    public function getUserPricingMargin ()
    {
        if (! isset($this->UserPricingMargin))
        {
            
            $this->UserPricingMargin = null;
        }
        return $this->UserPricingMargin;
    }

    /**
     * @param field_type $UserPricingMargin
     */
    public function setUserPricingMargin ($UserPricingMargin)
    {
        $this->UserPricingMargin = $UserPricingMargin;
        return $this;
    }

    /**
     * @return the $UserMonthlyLeasePayment
     */
    public function getUserMonthlyLeasePayment ()
    {
        if (! isset($this->UserMonthlyLeasePayment))
        {
            
            $this->UserMonthlyLeasePayment = null;
        }
        return $this->UserMonthlyLeasePayment;
    }

    /**
     * @param field_type $UserMonthlyLeasePayment
     */
    public function setUserMonthlyLeasePayment ($UserMonthlyLeasePayment)
    {
        $this->UserMonthlyLeasePayment = $UserMonthlyLeasePayment;
        return $this;
    }

    /**
     * @return the $UserDefaultPrinterCost
     */
    public function getUserDefaultPrinterCost ()
    {
        if (! isset($this->UserDefaultPrinterCost))
        {
            
            $this->UserDefaultPrinterCost = null;
        }
        return $this->UserDefaultPrinterCost;
    }

    /**
     * @param field_type $UserDefaultPrinterCost
     */
    public function setUserDefaultPrinterCost ($UserDefaultPrinterCost)
    {
        $this->UserDefaultPrinterCost = $UserDefaultPrinterCost;
        return $this;
    }

    /**
     * @return the $UserLeasedBwPerPage
     */
    public function getUserLeasedBwPerPage ()
    {
        if (! isset($this->UserLeasedBwPerPage))
        {
            
            $this->UserLeasedBwPerPage = null;
        }
        return $this->UserLeasedBwPerPage;
    }

    /**
     * @param field_type $UserLeasedBwPerPage
     */
    public function setUserLeasedBwPerPage ($UserLeasedBwPerPage)
    {
        $this->UserLeasedBwPerPage = $UserLeasedBwPerPage;
        return $this;
    }

    /**
     * @return the $UserLeasedColorPerPage
     */
    public function getUserLeasedColorPerPage ()
    {
        if (! isset($this->UserLeasedColorPerPage))
        {
            
            $this->UserLeasedColorPerPage = null;
        }
        return $this->UserLeasedColorPerPage;
    }

    /**
     * @param field_type $UserLeasedColorPerPage
     */
    public function setUserLeasedColorPerPage ($UserLeasedColorPerPage)
    {
        $this->UserLeasedColorPerPage = $UserLeasedColorPerPage;
        return $this;
    }

    /**
     * @return the $UserMpsBwPerPage
     */
    public function getUserMpsBwPerPage ()
    {
        if (! isset($this->UserMpsBwPerPage))
        {
            
            $this->UserMpsBwPerPage = null;
        }
        return $this->UserMpsBwPerPage;
    }

    /**
     * @param field_type $UserMpsBwPerPage
     */
    public function setUserMpsBwPerPage ($UserMpsBwPerPage)
    {
        $this->UserMpsBwPerPage = $UserMpsBwPerPage;
        return $this;
    }

    /**
     * @return the $UserMpsColorPerPage
     */
    public function getUserMpsColorPerPage ()
    {
        if (! isset($this->UserMpsColorPerPage))
        {
            
            $this->UserMpsColorPerPage = null;
        }
        return $this->UserMpsColorPerPage;
    }

    /**
     * @param field_type $UserMpsColorPerPage
     */
    public function setUserMpsColorPerPage ($UserMpsColorPerPage)
    {
        $this->UserMpsColorPerPage = $UserMpsColorPerPage;
        return $this;
    }

    /**
     * @return the $UserKilowattsPerHour
     */
    public function getUserKilowattsPerHour ()
    {
        if (! isset($this->UserKilowattsPerHour))
        {
            
            $this->UserKilowattsPerHour = null;
        }
        return $this->UserKilowattsPerHour;
    }

    /**
     * @param field_type $UserKilowattsPerHour
     */
    public function setUserKilowattsPerHour ($UserKilowattsPerHour)
    {
        $this->UserKilowattsPerHour = $UserKilowattsPerHour;
        return $this;
    }

    /**
     * @return the $PricingConfigId
     */
    public function getPricingConfigId ()
    {
        if (! isset($this->PricingConfigId))
        {
            
            $this->PricingConfigId = null;
        }
        return $this->PricingConfigId;
    }

    /**
     * @param field_type $PricingConfigId
     */
    public function setPricingConfigId ($PricingConfigId)
    {
        $this->PricingConfigId = $PricingConfigId;
        return $this;
    }

    /**
     * @return the $FailedLoginAttempts
     */
    public function getFailedLoginAttempts ()
    {
        if (! isset($this->FailedLoginAttempts))
        {
            
            $this->FailedLoginAttempts = null;
        }
        return $this->FailedLoginAttempts;
    }

    /**
     * @param field_type $FailedLoginAttempts
     */
    public function setFailedLoginAttempts ($FailedLoginAttempts)
    {
        $this->FailedLoginAttempts = $FailedLoginAttempts;
        return $this;
    }

    /**
     * @return the $LoginRestrictedUntilDate
     */
    public function getLoginRestrictedUntilDate ()
    {
        if (! isset($this->LoginRestrictedUntilDate))
        {
            
            $this->LoginRestrictedUntilDate = null;
        }
        return $this->LoginRestrictedUntilDate;
    }

    /**
     * @param field_type $LoginRestrictedUntilDate
     */
    public function setLoginRestrictedUntilDate ($LoginRestrictedUntilDate)
    {
        $this->LoginRestrictedUntilDate = $LoginRestrictedUntilDate;
        return $this;
    }

    /**
     * @return the $UserDefaultBWTonerCost
     */
    public function getUserDefaultBWTonerCost ()
    {
        if (! isset($this->UserDefaultBWTonerCost))
        {
            
            $this->UserDefaultBWTonerCost = null;
        }
        return $this->UserDefaultBWTonerCost;
    }

    /**
     * @param field_type $UserDefaultBWTonerCost
     */
    public function setUserDefaultBWTonerCost ($UserDefaultBWTonerCost)
    {
        $this->UserDefaultBWTonerCost = $UserDefaultBWTonerCost;
        return $this;
    }

    /**
     * @return the $UserDefaultBWTonerYield
     */
    public function getUserDefaultBWTonerYield ()
    {
        if (! isset($this->UserDefaultBWTonerYield))
        {
            
            $this->UserDefaultBWTonerYield = null;
        }
        return $this->UserDefaultBWTonerYield;
    }

    /**
     * @param field_type $UserDefaultBWTonerYield
     */
    public function setUserDefaultBWTonerYield ($UserDefaultBWTonerYield)
    {
        $this->UserDefaultBWTonerYield = $UserDefaultBWTonerYield;
        return $this;
    }

    /**
     * @return the $UserDefaultColorTonerCost
     */
    public function getUserDefaultColorTonerCost ()
    {
        if (! isset($this->UserDefaultColorTonerCost))
        {
            
            $this->UserDefaultColorTonerCost = null;
        }
        return $this->UserDefaultColorTonerCost;
    }

    /**
     * @param field_type $UserDefaultColorTonerCost
     */
    public function setUserDefaultColorTonerCost ($UserDefaultColorTonerCost)
    {
        $this->UserDefaultColorTonerCost = $UserDefaultColorTonerCost;
        return $this;
    }

    /**
     * @return the $UserDefaultColorTonerYield
     */
    public function getUserDefaultColorTonerYield ()
    {
        if (! isset($this->UserDefaultColorTonerYield))
        {
            
            $this->UserDefaultColorTonerYield = null;
        }
        return $this->UserDefaultColorTonerYield;
    }

    /**
     * @param field_type $UserDefaultColorTonerYield
     */
    public function setUserDefaultColorTonerYield ($UserDefaultColorTonerYield)
    {
        $this->UserDefaultColorTonerYield = $UserDefaultColorTonerYield;
        return $this;
    }

    /**
     * @return the $UserDefaultThreeColorTonerCost
     */
    public function getUserDefaultThreeColorTonerCost ()
    {
        if (! isset($this->UserDefaultThreeColorTonerCost))
        {
            
            $this->UserDefaultThreeColorTonerCost = null;
        }
        return $this->UserDefaultThreeColorTonerCost;
    }

    /**
     * @param field_type $UserDefaultThreeColorTonerCost
     */
    public function setUserDefaultThreeColorTonerCost ($UserDefaultThreeColorTonerCost)
    {
        $this->UserDefaultThreeColorTonerCost = $UserDefaultThreeColorTonerCost;
        return $this;
    }

    /**
     * @return the $UserDefaultThreeColorTonerYield
     */
    public function getUserDefaultThreeColorTonerYield ()
    {
        if (! isset($this->UserDefaultThreeColorTonerYield))
        {
            
            $this->UserDefaultThreeColorTonerYield = null;
        }
        return $this->UserDefaultThreeColorTonerYield;
    }

    /**
     * @param field_type $UserDefaultThreeColorTonerYield
     */
    public function setUserDefaultThreeColorTonerYield ($UserDefaultThreeColorTonerYield)
    {
        $this->UserDefaultThreeColorTonerYield = $UserDefaultThreeColorTonerYield;
        return $this;
    }

    /**
     * @return the $UserDefaultFourColorTonerCost
     */
    public function getUserDefaultFourColorTonerCost ()
    {
        if (! isset($this->UserDefaultFourColorTonerCost))
        {
            
            $this->UserDefaultFourColorTonerCost = null;
        }
        return $this->UserDefaultFourColorTonerCost;
    }

    /**
     * @param field_type $UserDefaultFourColorTonerCost
     */
    public function setUserDefaultFourColorTonerCost ($UserDefaultFourColorTonerCost)
    {
        $this->UserDefaultFourColorTonerCost = $UserDefaultFourColorTonerCost;
        return $this;
    }

    /**
     * @return the $UserDefaultFourColorTonerYield
     */
    public function getUserDefaultFourColorTonerYield ()
    {
        if (! isset($this->UserDefaultFourColorTonerYield))
        {
            
            $this->UserDefaultFourColorTonerYield = null;
        }
        return $this->UserDefaultFourColorTonerYield;
    }

    /**
     * @param field_type $UserDefaultFourColorTonerYield
     */
    public function setUserDefaultFourColorTonerYield ($UserDefaultFourColorTonerYield)
    {
        $this->UserDefaultFourColorTonerYield = $UserDefaultFourColorTonerYield;
        return $this;
    }

    /**
     * @return the $FullCompanyLogo
     */
    public function getFullCompanyLogo ()
    {
        if (! isset($this->FullCompanyLogo))
        {
            
            $this->FullCompanyLogo = null;
        }
        return $this->FullCompanyLogo;
    }

    /**
     * @param field_type $FullCompanyLogo
     */
    public function setFullCompanyLogo ($FullCompanyLogo)
    {
        $this->FullCompanyLogo = $FullCompanyLogo;
        return $this;
    }

    /**
     * @return the $CompanyLogo
     */
    public function getCompanyLogo ()
    {
        if (! isset($this->CompanyLogo))
        {
            
            $this->CompanyLogo = null;
        }
        return $this->CompanyLogo;
    }

    /**
     * @param field_type $CompanyLogo
     */
    public function setCompanyLogo ($CompanyLogo)
    {
        $this->CompanyLogo = $CompanyLogo;
        return $this;
    }

    /**
     * @return the $CompanyReportColor
     */
    public function getCompanyReportColor ()
    {
        if (! isset($this->CompanyReportColor))
        {
            
            $this->CompanyReportColor = null;
        }
        return $this->CompanyReportColor;
    }

    /**
     * @param field_type $CompanyReportColor
     */
    public function setCompanyReportColor ($CompanyReportColor)
    {
        $this->CompanyReportColor = $CompanyReportColor;
        return $this;
    }

    /**
     * @return the $UserServiceCostPerPage
     */
    public function getUserServiceCostPerPage ()
    {
        if (! isset($this->UserServiceCostPerPage))
        {
            
            $this->UserServiceCostPerPage = null;
        }
        return $this->UserServiceCostPerPage;
    }

    /**
     * @param field_type $UserServiceCostPerPage
     */
    public function setUserServiceCostPerPage ($UserServiceCostPerPage)
    {
        $this->UserServiceCostPerPage = $UserServiceCostPerPage;
        return $this;
    }

    /**
     * @return the $UserActualPageCoverageMono
     */
    public function getUserActualPageCoverageMono ()
    {
        if (! isset($this->UserActualPageCoverageMono))
        {
            
            $this->UserActualPageCoverageMono = null;
        }
        return $this->UserActualPageCoverageMono;
    }

    /**
     * @return the $UserActualPageCoverageColor
     */
    public function getUserActualPageCoverageColor ()
    {
        if (! isset($this->UserActualPageCoverageColor))
        {
            
            $this->UserActualPageCoverageColor = null;
        }
        return $this->UserActualPageCoverageColor;
    }

    /**
     * @param field_type $UserActualPageCoverageMono
     */
    public function setUserActualPageCoverageMono ($UserActualPageCoverageMono)
    {
        $this->UserActualPageCoverageMono = $UserActualPageCoverageMono;
        return $this;
    }

    /**
     * @param field_type $UserActualPageCoverageColor
     */
    public function setUserActualPageCoverageColor ($UserActualPageCoverageColor)
    {
        $this->UserActualPageCoverageColor = $UserActualPageCoverageColor;
        return $this;
    }

    /**
     * @return the $ReportSettings
     */
    public function getReportSettings ($getOverrides = true)
    {
        if (! isset($this->ReportSettings))
        {
            if ($this->getUserId() == Proposalgen_Model_User::getCurrentUserId())
            {
                $settings = Proposalgen_Model_DealerCompany::getCurrentUserCompany()->getReportSettings();
            }
            else
            {
                $settings = Proposalgen_Model_Mapper_DealerCompany::getInstance()->find($this->getDealerCompanyId())
                    ->getReportSettings();
            }
            
            $usersettings = array (
                    "estimated_page_coverage_mono" => $this->getUserEstimatedPageCoverageMono(),
                    "estimated_page_coverage_color" => $this->getUserEstimatedPageCoverageColor(),
                    "actual_page_coverage_mono" => $this->getUserActualPageCoverageMono(),
                    "actual_page_coverage_color" => $this->getUserActualPageCoverageColor(),
                    "service_cost_per_page" => $this->getUserServiceCostPerPage(),
                    "admin_charge_per_page" => $this->getUserAdminChargePerPage(),
                    "pricing_margin" => $this->getUserPricingMargin(),
                    "monthly_lease_payment" => $this->getUserMonthlyLeasePayment(),
                    "default_printer_cost" => $this->getUserDefaultPrinterCost(),
                    "leased_bw_per_page" => $this->getUserLeasedBwPerPage(),
                    "leased_color_per_page" => $this->getUserLeasedColorPerPage(),
                    "mps_bw_per_page" => $this->getUserMpsBwPerPage(),
                    "mps_color_per_page" => $this->getUserMpsColorPerPage(),
                    "kilowatts_per_hour" => $this->getUserKilowattsPerHour(),
                    "pricing_config_id" => $this->getPricingConfigId(),
                    "pricing_margin" => $this->getUserPricingMargin()
            );
            
            if ($getOverrides)
            {
                if ($usersettings ["pricing_config_id"] === 1)
                {
                    $usersettings ["pricing_config_id"] = null;
                }
                foreach ( $usersettings as $setting => $value )
                {
                    if (! empty($value))
                    {
                        $settings [$setting] = $value;
                    }
                }
            }
            else
            {
                $settings = $usersettings;
            }
            $this->ReportSettings = $settings;
        }
        return $this->ReportSettings;
    }

    /**
     * @param field_type $ReportSettings
     */
    public function setReportSettings ($ReportSettings)
    {
        $this->ReportSettings = $ReportSettings;
        return $this;
    }

}