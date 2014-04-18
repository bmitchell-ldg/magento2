<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Catalog\Pricing;

/**
 * Class RenderTest
 */
class RenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Pricing\Render
     */
    protected $object;

    /**
     * @var \Magento\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

    /**
     * @var \Magento\View\LayoutInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pricingRenderBlock;

    protected function setUp()
    {
        $this->registry = $this->getMock('Magento\Registry', ['registry'], [], '', false);

        $this->pricingRenderBlock = $this->getMock('Magento\Pricing\Render', [], [], '', false);

        $this->layout = $this->getMock('Magento\View\Layout', [], [], '', false);

        $eventManager = $this->getMock('Magento\Event\ManagerStub', [], [], '', false);
        $config = $this->getMock('Magento\Store\Model\Store\Config', [], [], '', false);
        $scopeConfigMock = $this->getMockForAbstractClass('Magento\Framework\App\Config\ScopeConfigInterface');
        $context = $this->getMock('Magento\View\Element\Template\Context', [], [], '', false);
        $context->expects($this->any())
            ->method('getEventManager')
            ->will($this->returnValue($eventManager));
        $context->expects($this->any())
            ->method('getStoreConfig')
            ->will($this->returnValue($config));
        $context->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($this->layout));
        $context->expects($this->any())
            ->method('getScopeConfig')
            ->will($this->returnValue($scopeConfigMock));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->object = $objectManager->getObject(
            'Magento\Catalog\Pricing\Render',
            [
                'context' => $context,
                'registry' => $this->registry,
                'data' => [
                    'price_render' => 'test_price_render',
                    'price_type_code' => 'test_price_type_code',
                    'module_name' => 'test_module_name'
                ]
            ]
        );
    }

    public function testToHtmlProductFromRegistry()
    {
        $expectedValue = 'string';

        $product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);

        $this->layout->expects($this->any())
            ->method('getBlock')
            ->will($this->returnValue($this->pricingRenderBlock));

        $this->registry->expects($this->once())
            ->method('registry')
            ->with($this->equalTo('product'))
            ->will($this->returnValue($product));

        $this->pricingRenderBlock->expects($this->any())
            ->method('render')
            ->with(
                $this->equalTo('test_price_type_code'),
                $this->equalTo($product),
                $this->equalTo($this->object->getData())
            )
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $this->object->toHtml());
    }

    public function testToHtmlProductFromParentBlock()
    {
        $expectedValue = 'string';

        $product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);

        $this->registry->expects($this->never())
            ->method('registry');

        $block = $this->getMock('Magento\Pricing\Render', ['getProductItem', 'render'], [], '', false);

        $block->expects($this->any())
            ->method('render')
            ->with(
                $this->equalTo('test_price_type_code'),
                $this->equalTo($product),
                $this->equalTo($this->object->getData())
            )
            ->will($this->returnValue($expectedValue));

        $block->expects($this->any())
            ->method('getProductItem')
            ->will($this->returnValue($product));

        $this->layout->expects($this->once())
            ->method('getParentName')
            ->will($this->returnValue('parent_name'));

        $this->layout->expects($this->any())
            ->method('getBlock')
            ->will($this->returnValue($block));

        $this->assertEquals($expectedValue, $this->object->toHtml());
    }
}
