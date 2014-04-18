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
 * @package     Magento_Downloadable
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/** @var $this \Magento\Catalog\Model\Resource\Setup */
$installFile = __DIR__ . '/upgrade-1.6.0.0.1-1.6.0.0.2.php';

/** @var \Magento\Filesystem\Directory\Read $moduleDirectory */
$moduleDirectory = $this->getFilesystem()->getDirectoryRead(\Magento\Framework\App\Filesystem::MODULES_DIR);
if ($moduleDirectory->isExist($moduleDirectory->getRelativePath($installFile))) {
    include $installFile;
}

/** @var $connection \Magento\DB\Adapter\Pdo\Mysql */
$connection = $this->getConnection();
$connection->changeTableEngine(
    $this->getTable('catalog_product_index_price_downlod_tmp'),
    \Magento\DB\Adapter\Pdo\Mysql::ENGINE_MEMORY
);
