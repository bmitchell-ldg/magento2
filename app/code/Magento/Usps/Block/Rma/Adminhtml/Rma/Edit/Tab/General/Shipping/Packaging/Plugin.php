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
namespace Magento\Usps\Block\Rma\Adminhtml\Rma\Edit\Tab\General\Shipping\Packaging;

use Magento\Framework\App\RequestInterface;
use Magento\Usps\Helper\Data as UspsHelper;
use Magento\Usps\Model\Carrier;

/**
 * Rma block plugin
 */
class Plugin
{
    /**
     * Usps helper
     *
     * @var \Magento\Usps\Helper\Data
     */
    protected $uspsHelper;

    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Construct
     *
     * @param \Magento\Usps\Helper\Data $uspsHelper
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(UspsHelper $uspsHelper, RequestInterface $request)
    {
        $this->uspsHelper = $uspsHelper;
        $this->request = $request;
    }

    /**
     * Add rule to isGirthAllowed() method
     *
     * @param \Magento\Object $subject $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsGirthAllowed(\Magento\Object $subject, $result)
    {
        return $result && $this->uspsHelper->displayGirthValue($this->request->getParam('method'));
    }

    /**
     * Add rule to isGirthAllowed() method
     *
     * @param \Magento\Object $subject
     * @param \Closure $proceed
     * @return array
     */
    public function aroundCheckSizeAndGirthParameter(\Magento\Object $subject, \Closure $proceed)
    {
        $carrier = $subject->getCarrier();
        $size = $subject->getSourceSizeModel();

        $girthEnabled = false;
        $sizeEnabled = false;
        if ($carrier && isset($size[0]['value'])) {
            if ($size[0]['value'] == Carrier::SIZE_LARGE && in_array(
                key($subject->getContainers()),
                array(Carrier::CONTAINER_NONRECTANGULAR, Carrier::CONTAINER_VARIABLE)
            )
            ) {
                $girthEnabled = true;
            }

            if (in_array(
                key($subject->getContainers()),
                array(Carrier::CONTAINER_NONRECTANGULAR, Carrier::CONTAINER_RECTANGULAR, Carrier::CONTAINER_VARIABLE)
            )
            ) {
                $sizeEnabled = true;
            }
        }

        return array($girthEnabled, $sizeEnabled);
    }
}
