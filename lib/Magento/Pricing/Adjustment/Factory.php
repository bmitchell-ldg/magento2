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

namespace Magento\Pricing\Adjustment;

/**
 * Adjustment factory
 */
class Factory
{
    /**
     * Object Manager
     *
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * Construct
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create shared price adjustment
     *
     * @param string $className
     * @param array $arguments
     * @return \Magento\Pricing\Adjustment\AdjustmentInterface
     * @throws \InvalidArgumentException
     */
    public function create($className, array $arguments = [])
    {
        $price = $this->objectManager->create($className, $arguments);
        if (!$price instanceof AdjustmentInterface) {
            throw new \InvalidArgumentException(
                $className . ' doesn\'t implement \Magento\Pricing\Adjustment\AdjustmentInterface'
            );
        }
        return $price;
    }
}
