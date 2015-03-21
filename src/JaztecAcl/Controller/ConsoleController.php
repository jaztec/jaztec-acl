<?php

namespace JaztecAcl\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;

class ConsoleController extends AbstractActionController
{
    /** @var \JaztecAcl\Service\InstallationService */
    protected $installationService;

    /**
     * Will return a blank page by default.
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * Will check and update the entire database structure.
     * 
     * @throws \Zend\Console\Exception\RuntimeException
     * @return string Program output.
     */
    public function updateDatabaseAction()
    {
        // Check if a console is used.
        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new \Zend\Console\Exception\RuntimeException('You can only access this function through a console');
        }
        // Gather the variables.
        /* @var $mode string */
        if ((bool) $request->getParam('help', false) || $request->getParam('h', false)) {
            $mode = 'help';
        } else {
            $mode = $request->getParam('clean-install', false) ? 'clean-install' : false;
            if (!$mode) {
                $mode = $request->getParam('update', false) ? 'update' : false;
            }
            if (!$mode) {
                $mode = 'help';
            }
        }
        /* @var $verbose boolean */
        $verbose = (bool) $request->getParam('verbose', false) || $request->getParam('v', false);

        /* @var $result string */
        $result = '';
        if ($verbose) {
            print_r('Processing mode: ');
            print_r($mode . "\n");
        }
        // Test the mode.
        switch ($mode) {
            case 'clean-install':
                $email = $request->getParam('email', null);
                if (!$email) {
                    throw new \Zend\Console\Exception\RuntimeException('No e-mail parameter was received.');
                }
                $result = $this->getService()->installDatabase($email, $verbose);
                break;
            case 'update':
                $result = $this->getService()->updateDatabase($verbose);
                break;
            case 'help':
                $result = $this->getHelpOutput();
                break;
        }
        return $result;
    }

    /**
     * Get the help output for console usage of the JaztecAcl 'acl database'
     * console command.
     * 
     * @return string The help output string.
     */
    protected function getHelpOutput()
    {
        /* @var $result string */
        $result = "\n";
        $result .= "Copyright 2014 by Jasper van Herpt <jasper.v.herpt@gmail.com>\n";
        $result .= "\n";
        $result .= "Usage:\t\tacl database [clean-install|update] [--email=] [--help|-h] [--verbose|-v]\n";
        $result .= "Examples:\tacl database clean-install --email=john.doe@example.com --verbose\n";
        $result .= "\t\tacl database update --verbose\n";
        $result .= "\t\tacl database --help\n";
        $result .= "\n";
        $result .= "clean-install: Installs the database schema to the configurated database connection\n";
        $result .= "\t--e-mail\t(required):\tAny e-mail address\n";
        $result .= "\t--verbose|-v\t(optional):\tOutput progress\n";
        $result .= "\n";
        $result .= "update: Update the class data to the existing database schema.\n";
        $result .= "\t--verbose|-v\t(optional):\tOutput progress\n";
        $result .= "\n";
        return $result;
    }

    /**
     * @return \JaztecAcl\Service\InstallationService
     */
    public function getService()
    {
        if (empty($this->installationService)) {
            $this->installationService = $this->getServiceLocator()->get('jaztec_acl_installation_service');
        }
        return $this->installationService;
    }
}
