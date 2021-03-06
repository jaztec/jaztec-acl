<?php

/**
 * Credits to Evan Coury for writing this standard bootstrap.
 */

namespace JaztecAclTest;

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use RuntimeException;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

class Bootstrap
{

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected static $serviceManager;

    /**
     * @var array
     */
    protected static $config;
    protected static $bootstrap;

    /**
     * Setup the bootstrap.
     *
     * @return void
     */
    public static function init()
    {
        // Load a config file.
        if (is_readable(__DIR__ . '/TestConfig.php')) {
            $testConfig = include __DIR__ . '/TestConfig.php';
        } else {
            $testConfig = include __DIR__ . '/TestConfig.php.dist';
        }

        $zf2ModulePaths = array();

        if (isset($testConfig['module_listener_options']['module_paths'])) {
            $modulePaths = $testConfig['module_listener_options']['module_paths'];
            foreach ($modulePaths as $modulePath) {
                if (($path = static::findParentPath($modulePath))) {
                    $zf2ModulePaths[] = $path;
                }
            }
        }

        $zf2ModulePaths = implode(PATH_SEPARATOR, $zf2ModulePaths) . PATH_SEPARATOR;
        $zf2ModulePaths .= getenv('ZF2_MODULES_TEST_PATHS') ? : (defined('ZF2_MODULES_TEST_PATHS') ? ZF2_MODULES_TEST_PATHS : '');

        static::initAutoloader();

        $baseConfig = array(
            'module_listener_options' => array(
                'module_paths' => explode(PATH_SEPARATOR, $zf2ModulePaths),
            ),
        );

        $config = ArrayUtils::merge($baseConfig, $testConfig);

        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();

        static::$serviceManager = $serviceManager;
        static::$config = $config;
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager
     */
    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    /**
     * @return array
     */
    public static function getConfig()
    {
        return static::$config;
    }

    /**
     * @throws RuntimeException
     * @return void
     */
    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        if (is_readable($vendorPath . '/autoload.php')) {
            $loader = include $vendorPath . '/autoload.php';
        } else {
            $zf2Path = getenv('ZF2_PATH') ? : (defined('ZF2_PATH') ? ZF2_PATH : (is_dir($vendorPath . '/ZF2/library') ? $vendorPath . '/ZF2/library' : false));

            if (!$zf2Path) {
                throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
            }

            include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
        }

        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true,
                'namespaces'      => array(
                    __NAMESPACE__ => __DIR__ . '/' . __NAMESPACE__,
                ),
            ),
        ));
    }

    /**
     * @param  string $path
     * @return string
     */
    protected static function findParentPath($path)
    {
        $dir         = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir         = dirname($dir);
            if ($previousDir === $dir)
                return false;
            $previousDir = $dir;
        }

        return $dir . '/' . $path;
    }

    /**
     * Reset the database and provide dummy filling.
     */
    public static function setUpAclDatabase() {
        // Schema Tool to process our entities
        /* @var \Doctrine\ORM\EntityManager $em */
        $em = static::getServiceManager()->get('doctrine.entitymanager.orm_default');
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $em->getMetaDataFactory()->getAllMetaData();

        // Drop all classes and re-build them for each test case
        $tool->dropSchema($classes);
        $tool->createSchema($classes);

        // Get the setup from the configuration.
        /* @var array $config */
        $config = static::getServiceManager()->get('Config');
        /* @var array $setUp */
        $setUp = $config['TestSuite']['setUp'];

        // SetUp roles.
        /* $var array $roleSetIp */
        $roleSetUp = $setUp['roles'];
        /* $var \JaztecAcl\Entity\Acl\Role[] $roles */
        $roles = array();

        foreach($roleSetUp as $setUpConfig) {
            $role = new \JaztecAcl\Entity\Acl\Role($setUpConfig['name']);
            $role->setSort($setUpConfig['sort']);

            if (array_key_exists('parent', $setUpConfig)) {
                foreach($roles as $cached) {
                    /* @var \JaztecAcl\Entity\Acl\Role $cached */
                    if ($cached->getName() == $setUpConfig['parent']) {
                        $role->setParent($cached);
                    }
                }
            }
            $em->persist($role);
            $roles[] = $role;
        }
        $em->flush();
    }
}

Bootstrap::init();
