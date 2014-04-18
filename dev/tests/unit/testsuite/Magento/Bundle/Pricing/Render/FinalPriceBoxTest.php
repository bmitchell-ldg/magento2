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
 * @package     Magento_Bundle
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Bundle\Pricing\Render;

use Magento\Bundle\Pricing\Price;

class FinalPriceBoxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FinalPriceBox
     */
    protected $model;

    /**
     * @var \Magento\Pricing\Object\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleableItem;

    public function setUp()
    {
        $this->saleableItem = $this->getMock('Magento\Pricing\Object\SaleableInterface');

        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectHelper->getObject('Magento\Bundle\Pricing\Render\FinalPriceBox', [
            'saleableItem' => $this->saleableItem
        ]);
    }

    /**
     * @dataProvider showRangePriceDataProvider
     */
    public function testShowRangePrice($value, $maxValue, $result)
    {
        $priceInfo = $this->getMock('Magento\Pricing\PriceInfoInterface');
        $optionPrice = $this->getMockBuilder('Magento\Bundle\Pricing\Price\BundleOptionPrice')
            ->disableOriginalConstructor()
            ->getMock();

        $this->saleableItem->expects($this->atLeastOnce())
            ->method('getPriceInfo')
            ->will($this->returnValue($priceInfo));

        $priceInfo->expects($this->atLeastOnce())
            ->method('getPrice')
            ->with(Price\BundleOptionPriceInterface::PRICE_TYPE_BUNDLE_OPTION)
            ->will($this->returnValue($optionPrice));

        $optionPrice->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($value));

        $optionPrice->expects($this->once())
            ->method('getMaxValue')
            ->will($this->returnValue($maxValue));

        $this->assertEquals($result, $this->model->showRangePrice());
    }


    /**
     * @return array
     */
    public function showRangePriceDataProvider()
    {
        return [
            ['value' => 40.2, 'maxValue' => 45., 'result' => true],
            ['value' => false, 'maxValue' => false, 'result' => false],
            ['value' => 45.0, 'maxValue' => 45., 'result' => false],
        ];
    }
}
