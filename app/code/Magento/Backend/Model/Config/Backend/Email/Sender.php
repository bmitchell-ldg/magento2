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
 * @package     Magento_Backend
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * System config email sender field backend model
 */
namespace Magento\Backend\Model\Config\Backend\Email;

class Sender extends \Magento\Framework\App\Config\Value
{
    /**
     * Check sender name validity
     *
     * @return $this
     * @throws \Magento\Model\Exception
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (!preg_match("/^[\S ]+$/", $value)) {
            throw new \Magento\Model\Exception(
                __('The sender name "%1" is not valid. Please use only visible characters and spaces.', $value)
            );
        }

        if (strlen($value) > 255) {
            throw new \Magento\Model\Exception(__('Maximum sender name length is 255. Please correct your settings.'));
        }
        return $this;
    }
}
