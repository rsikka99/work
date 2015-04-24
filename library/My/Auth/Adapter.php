<?php

/**
 * An custom auth adapter written to handle various aspects of login management.
 *
 * This class extends @see Zend_Auth_Adapter_DbTable to allow locking accounts as well as managing login attempts. So
 * far it manages login attempts and freezing the account if too many attempts are made befor a successful attempt.
 *
 * @author Lee Robert
 *
 */
class My_Auth_Adapter extends Zend_Auth_Adapter_DbTable
{

    /**
     * The column where lockout attempts are located
     *
     * @var String
     */
    protected $_loginAttemptsColumn = 'loginAttempts';

    /**
     * The column to get the date the account is frozen until.
     *
     * @var String
     */
    protected $_frozenColumn = 'frozenUntil';

    /**
     * The column to check to see if an account has been locked by an administrator
     *
     * @var String
     */
    protected $_lockedColumn = 'locked';

    /**
     * The amount of time in seconds that a user will be locked out once going over the maximum login attempts
     *
     * @var integer
     */
    protected $_lockoutTime = 300;

    /**
     * The maximum number of login attempts a user can make before we lock them out
     *
     * @var integer
     */
    protected $_maxLoginAttempts = 10;

    /**
     * A variable to keep track of the setup.
     * No need to run it twice
     *
     * @var boolean
     */
    protected $_ranAuthenticatedSetup = false;

    /**
     * (non-PHPdoc)
     *
     * @see Zend_Auth_Adapter_DbTable::_authenticateSetup()
     */
    protected function _authenticateSetup ()
    {
        if (!$this->_ranAuthenticatedSetup)
        {
            $this->_ranAuthenticatedSetup = true;
            parent::_authenticateSetup();
        }

    }

    /**
     * authenticate() - defined by Zend_Auth_Adapter_Interface.
     * This method is called to attempt an authentication.
     * Previous to this call, this adapter would have already been configured with all necessary information to
     * successfully connect to a database table and attempt to find a record matching the provided identity.
     *
     * @throws Zend_Auth_Adapter_Exception if answering the authentication query is impossible
     * @return Zend_Auth_Result
     */
    public function authenticate ()
    {
        $this->_authenticateSetup();

        /*
         * In order to use SHA 512 to hash our passwords we must fetch the password since it contains the salt used when
         * the password was created. This function will technically work with other hashing methods as long as the
         * format is consistent with php's crypt output.
         */

        // Load the password and salt
        $salt = $this->_zendDb->fetchOne("SELECT {$this->_credentialColumn} FROM {$this->_tableName} WHERE {$this->_identityColumn} = ?", $this->_identity);
        if (!$salt)
        {
            // return 'identity not found' error
            $this->_authenticateResultInfo ['code']        = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $this->_authenticateResultInfo ['messages'] [] = 'A record with the supplied identity could not be found.';

            return $this->_authenticateCreateAuthResult();
        }

        // Now we can encrypt the password the user gave us with our salt from the database before we try to match them.
        $password          = $this->_credential;
        $this->_credential = crypt($password, $salt);

        // Continue with the authentication process and return the result


        return parent::authenticate();
    }

    /**
     * _authenticateValidateResult() - This method attempts to validate that the record in the resultset is indeed a
     * record that matched the identity provided to this adapter.
     *
     * @param array $resultIdentity
     *
     * @return Zend_Auth_Result
     */
    protected function _authenticateValidateResult ($resultIdentity)
    {
        /*
         * We override the function and check to see if an account is locked. If the account is locked, tell the user,
         * too bad, so sad.
         */

        if ($resultIdentity [$this->_lockedColumn])
        {
            // Since this is not a security risk we don't increment the login attempts
            $this->_authenticateResultInfo ['code']        = Zend_Auth_Result::FAILURE_UNCATEGORIZED;
            $this->_authenticateResultInfo ['messages'] [] = 'Account is locked.';

            return $this->_authenticateCreateAuthResult();
        }

        $loginAttempts = $resultIdentity [$this->_loginAttemptsColumn];
        $frozenDate    = new DateTime($resultIdentity [$this->_frozenColumn]);
        $currentDate   = new DateTime();

        /*
         * If we're over our max attempts, we lock them out of their account and reset the login attempts count so this
         * doesn't get triggered on future attempts
         */
        if ($loginAttempts > $this->_maxLoginAttempts)
        {
            $newLockedDate = date('Y-m-d H:i:s', time() + $this->_lockoutTime);
            $this->_zendDb->update($this->_tableName, [
                $this->_loginAttemptsColumn => 0,
                $this->_frozenColumn        => $newLockedDate,
            ], [
                "{$this->_identityColumn} = ?" => $this->_identity,
            ]);
            $frozenDate = new DateTime($newLockedDate);
        }

        /*
         * If they are locked out, display a nice friendly message
         */
        $diff = $currentDate->diff($frozenDate);
        if (!$diff->invert && ($diff->s > 0 || $diff->i > 0 || $diff->h > 0 || $diff->days > 0))
        {
            // Fetch a nice time interval description to help the user know when he/she can try again
            $lockedOutTime                                 = $this->getTimeDifference($diff);
            $this->_authenticateResultInfo ['code']        = Zend_Auth_Result::FAILURE_UNCATEGORIZED;
            $this->_authenticateResultInfo ['messages'] [] = "Your account is frozen for {$lockedOutTime} because of too many unsuccessful login attempts.";

            return $this->_authenticateCreateAuthResult();
        }

        // Get the parents result
        $result = parent::_authenticateValidateResult($resultIdentity);

        /*
         * If we were successful with matching the password, we clear their login attempts and allow them to proceed. If
         * they were unsuccessful we increment the login attempts column
         */
        if ($result->getCode() === Zend_Auth_Result::SUCCESS)
        {

            if ($loginAttempts > 0)
            {
                $this->_zendDb->update($this->_tableName, [
                    $this->_loginAttemptsColumn => 0,
                ], [
                    "{$this->_identityColumn} = ?" => $this->_identity,
                ]);
            }
        }
        else
        {
            // The user has made a bad attempt so we increment our counter
            $loginAttempts++;
            $this->_zendDb->update($this->_tableName, [
                $this->_loginAttemptsColumn => $loginAttempts,
            ], [
                "{$this->_identityColumn} = ?" => $this->_identity,
            ]);

        }

        return $result;
    }

    /**
     * Gets the locked out time difference as a nice string
     *
     * @param DateInterval $diff
     *            A date interval object
     *
     * @return string A string containing the amount of time until the account is unlocked. EG 1 year, or 55 days, or 23
     *         minutes
     */
    function getTimeDifference (DateInterval $diff)
    {
        if ($diff->y > 0)
        {
            $string = "{$diff->y} year";
            if ($diff->y > 1)
            {
                $string .= 's';
            }
        }
        else if ($diff->m > 0)
        {
            $string = "{$diff->m} month";
            if ($diff->m > 1)
            {
                $string .= 's';
            }
        }
        else if ($diff->d > 0)
        {
            $string = "{$diff->d} day";
            if ($diff->d > 1)
            {
                $string .= 's';
            }
        }
        else if ($diff->h > 0)
        {
            $string = "{$diff->h} hour";
            if ($diff->h > 1)
            {
                $string .= 's';
            }
        }
        else if ($diff->i > 0)
        {
            $string = "{$diff->i} minute";
            if ($diff->i > 1)
            {
                $string .= 's';
            }
        }
        else
        {
            $string = "{$diff->s} second";
            if ($diff->i !== 1)
            {
                $string .= 's';
            }
        }

        // If the date is in the past we should put that in the string 
        if ($diff->invert)
        {
            $string .= " ago";
        }

        return $string;
    }
}