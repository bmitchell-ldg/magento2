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
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\CatalogRule\Pricing\Price;

/**
 * Class CatalogRulePriceTest
 */
class CatalogRulePriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogRule\Pricing\Price\CatalogRulePrice
     */
    protected $object;

    /**
     * @var \Magento\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $salableItemMock;

    /**
     * @var \Magento\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataTimeMock;

    /**
     * @var \Magento\Store\Model\StoreManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \Magento\Pricing\PriceInfoInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceInfoMock;

    /**
     * @var \Magento\CatalogRule\Model\Resource\RuleFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogRuleResourceFactoryMock;

    /**
     * @var \Magento\CatalogRule\Model\Resource\Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogRuleResourceMock;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreWebsiteMock;

    /**
     * @var \Magento\Store\Model\Website|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreStoreMock;

    /**
     * @var \Magento\Pricing\Adjustment\Calculator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $calculator;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->salableItemMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getId', '__wakeup', 'getPriceInfo'],
            [],
            '',
            false
        );
        $this->dataTimeMock = $this->getMockForAbstractClass(
            'Magento\Stdlib\DateTime\TimezoneInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );

        $this->coreStoreMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager', [], [], '', false);
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->coreStoreMock));

        $this->customerSessionMock = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);
        $this->priceInfoMock = $this->getMock('\Magento\Pricing\PriceInfo', ['getAdjustments'], [], '', false);
        $this->catalogRuleResourceFactoryMock = $this->getMock(
            '\Magento\CatalogRule\Model\Resource\RuleFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->catalogRuleResourceMock = $this->getMock(
            '\Magento\CatalogRule\Model\Resource\Rule',
            [],
            [],
            '',
            false
        );

        $this->coreWebsiteMock = $this->getMock('\Magento\Core\Model\Website', [], [], '', false);

        $this->priceInfoMock->expects($this->any())
            ->method('getAdjustments')
            ->will($this->returnValue([]));
        $this->salableItemMock->expects($this->any())
            ->method('getPriceInfo')
            ->will($this->returnValue($this->priceInfoMock));

        $this->catalogRuleResourceFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->catalogRuleResourceMock));

        $this->calculator = $this->getMockBuilder('Magento\Pricing\Adjustment\Calculator')
            ->disableOriginalConstructor()
            ->getMock();
        $qty = 1;
        $this->object = new CatalogRulePrice(
            $this->salableItemMock,
            $qty,
            $this->calculator,
            $this->dataTimeMock,
            $this->storeManagerMock,
            $this->customerSessionMock,
            $this->catalogRuleResourceFactoryMock
        );
    }

    /**
     * Test get Value
     */
    public function testGetValue()
    {
        $coreStoreId = 1;
        $coreWebsiteId = 1;
        $productId = 1;
        $customerGroupId = 1;
        $dateTime = time();

        $expectedValue = 55.12;

        $this->coreStoreMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($coreStoreId));
        $this->coreStoreMock->expects($this->once())
            ->method('getWebsiteId')
            ->will($this->returnValue($coreWebsiteId));
        $this->dataTimeMock->expects($this->once())
            ->method('scopeTimeStamp')
            ->with($this->equalTo($coreStoreId))
            ->will($this->returnValue($dateTime));
        $this->customerSessionMock->expects($this->once())
            ->method('getCustomerGroupId')
            ->will($this->returnValue($customerGroupId));
        $this->catalogRuleResourceMock->expects($this->once())
            ->method('getRulePrice')
            ->will($this->returnValue($expectedValue));
        $this->salableItemMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($productId));

        $this->assertEquals($expectedValue, $this->object->getValue());
    }

    public function testGetAmountNoBaseAmount()
    {
        $this->catalogRuleResourceMock->expects($this->once())
            ->method('getRulePrice')
            ->will($this->returnValue(false));

        $result = $this->object->getValue();
        $this->assertFalse($result);
    }
}
