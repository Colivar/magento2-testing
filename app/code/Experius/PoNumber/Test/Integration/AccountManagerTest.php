<?php
/**
 * Created by PhpStorm.
 * User: carl
 * Date: 9/5/17
 * Time: 1:37 PM
 */

namespace Experius\AccountManager\Test\Integration;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\TestFramework\ObjectManager;
use Magento\Framework\Module\ModuleList;

class PonumberTest extends \PHPUnit_Framework_TestCase
{
    protected $moduleName = 'Experius_Ponumber';

    /**
     * Test wether the module is registered as an active module.
     */
    public function testModuleRegistration()
    {
        $registrar = new ComponentRegistrar();

        $this->assertArrayHasKey($this->moduleName, $registrar->getPaths(ComponentRegistrar::MODULE));
    }

    public function testConfiguration()
    {
        $objectManager = ObjectManager::getInstance();

        $moduleList = $objectManager->create(ModuleList::class);

        $this->assertTrue($moduleList->has($this->moduleName));
    }
}

