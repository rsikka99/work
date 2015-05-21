<?php
use Tangent\Controller\Action;

/**
 * Class Default_InfoController
 */
class Default_InfoController extends Action
{

    /**
     * Display the company End User License Agreement (EULA)
     */
    public function eulaAction ()
    {
        if ($_SERVER['QUERY_STRING']=='agree') {
            $auth   = Zend_Auth::getInstance();
            $identity = $auth->getIdentity();
            $userMapper = \MPSToolbox\Legacy\Mappers\UserMapper::getInstance();
            $user = $userMapper->find($identity->id);
            if ($user) {
                $identity->eulaAccepted = $user->eulaAccepted = date('Y-m-d H:i:s');
                $userMapper->save($user);
                $auth->getStorage()->write($identity);
                $this->_helper->viewRenderer->setNoRender(true);
                $this->_helper->layout->disableLayout();
                header('Location: /');
                return;
            }
        }
        $this->_pageTitle = ['End User License Agreement'];
        $file             = APPLICATION_PATH . "/../data/info/eula.txt";
        $text             = 'Not Available';

        if (file_exists($file))
        {
            $text = str_replace("�", "'", file_get_contents($file));
        }

        $this->view->text = $text;
    }

    /**
     * Displays the program version and meta information
     */
    public function aboutAction ()
    {
        $this->_pageTitle = ['About ' . $this->view->App()->title];
        // These things could be moved into a view helper....
        $this->view->buildinfo       = $this->getBuildInfo()->build;
        $this->view->changelog       = $this->getChangelog();
        $this->view->databaseVersion = $this->getDatabaseVersion();
    }

    /**
     * Gets the database version from the database metadata table
     *
     * @return string Returns the string 'Not available' when it cannot fetch the version from the database.
     */
    public function getDatabaseVersion ()
    {
        $databaseVersion = "Not Available";
        try
        {
            $db     = Zend_Db_Table::getDefaultAdapter();
            $result = $db->fetchRow("SELECT * FROM database_metadata WHERE meta_key='dbversion' LIMIT 1;");
            if ($result)
            {
                $databaseVersion = $result ["meta_value"];
            }
        }
        catch (Exception $e)
        {
        }

        return $databaseVersion;
    }

    /**
     * Gets the build information as a Zend_Config_Ini object
     *
     * @return Zend_Config_Ini The config object, or FALSE if the file was malformed/non existant.
     */
    public function getBuildInfo ()
    {
        $configPath = APPLICATION_PATH . '/configs/buildinfo.ini';
        $config     = false;
        if (file_exists($configPath))
        {
            try
            {
                $config = new Zend_Config_Ini($configPath, 'production');
            }
            catch (Exception $e)
            {
            }
        }

        /*
         * We could check to see if we have a git repository, and display related information if that's the case. This
         * could be used to show things during development and testing.
         */

        return $config;
    }

    /**
     * Gets the change log information for the project
     *
     * @return string
     */
    public function getChangelog ()
    {
        // We could fetch change logs on a module basis if we wanted to...
        $file = APPLICATION_PATH . "/configs/changelog.md";
        $text = 'Not Available';

        if (file_exists($file))
        {
            $text = file_get_contents($file);
        }

        return $text;
    }
}

