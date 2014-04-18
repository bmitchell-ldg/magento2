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
namespace Magento\Backend\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Data extends AbstractHelper
{
    const XML_PATH_USE_CUSTOM_ADMIN_URL = 'admin/url/use_custom';

    /**
     * @var string
     */
    protected $_pageHelpUrl;

    /**
     * @var \Magento\Framework\App\Route\Config
     */
    protected $_routeConfig;

    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_locale;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    /**
     * @var \Magento\Backend\App\Area\FrontNameResolver
     */
    protected $_frontNameResolver;

    /**
     * @var \Magento\Math\Random
     */
    protected $mathRandom;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Route\Config $routeConfig
     * @param \Magento\Locale\ResolverInterface $locale
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Backend\Model\Auth $auth
     * @param \Magento\Backend\App\Area\FrontNameResolver $frontNameResolver
     * @param \Magento\Math\Random $mathRandom
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Route\Config $routeConfig,
        \Magento\Locale\ResolverInterface $locale,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Backend\App\Area\FrontNameResolver $frontNameResolver,
        \Magento\Math\Random $mathRandom
    ) {
        parent::__construct($context);
        $this->_routeConfig = $routeConfig;
        $this->_locale = $locale;
        $this->_backendUrl = $backendUrl;
        $this->_auth = $auth;
        $this->_frontNameResolver = $frontNameResolver;
        $this->mathRandom = $mathRandom;
    }

    /**
     * @return string
     */
    public function getPageHelpUrl()
    {
        if (!$this->_pageHelpUrl) {
            $this->setPageHelpUrl();
        }
        return $this->_pageHelpUrl;
    }

    /**
     * @param string|null $url
     * @return $this
     */
    public function setPageHelpUrl($url = null)
    {
        if (is_null($url)) {
            $request = $this->_request;
            $frontModule = $request->getControllerModule();
            if (!$frontModule) {
                $frontModule = $this->_routeConfig->getModulesByFrontName($request->getModuleName());
                if (empty($frontModule) === false) {
                    $frontModule = $frontModule[0];
                } else {
                    $frontModule = null;
                }
            }
            $url = 'http://www.magentocommerce.com/gethelp/';
            $url .= $this->_locale->getLocaleCode() . '/';
            $url .= $frontModule . '/';
            $url .= $request->getControllerName() . '/';
            $url .= $request->getActionName() . '/';

            $this->_pageHelpUrl = $url;
        }
        $this->_pageHelpUrl = $url;

        return $this;
    }

    /**
     * @param string $suffix
     * @return $this
     */
    public function addPageHelpUrl($suffix)
    {
        $this->_pageHelpUrl = $this->getPageHelpUrl() . $suffix;
        return $this;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = array())
    {
        return $this->_backendUrl->getUrl($route, $params);
    }

    /**
     * @return int|bool
     */
    public function getCurrentUserId()
    {
        if ($this->_auth->getUser()) {
            return $this->_auth->getUser()->getId();
        }
        return false;
    }

    /**
     * Decode filter string
     *
     * @param string $filterString
     * @return array
     */
    public function prepareFilterString($filterString)
    {
        $data = array();
        $filterString = base64_decode($filterString);
        parse_str($filterString, $data);
        array_walk_recursive($data, array($this, 'decodeFilter'));
        return $data;
    }

    /**
     * Decode URL encoded filter value recursive callback method
     *
     * @param string &$value
     * @return void
     */
    public function decodeFilter(&$value)
    {
        $value = rawurldecode($value);
    }

    /**
     * Generate unique token for reset password confirmation link
     *
     * @return string
     */
    public function generateResetPasswordLinkToken()
    {
        return $this->mathRandom->getUniqueHash();
    }

    /**
     * Get backend start page URL
     *
     * @return string
     */
    public function getHomePageUrl()
    {
        return $this->_backendUrl->getRouteUrl('adminhtml');
    }

    /**
     * Return Backend area front name
     *
     * @return string
     */
    public function getAreaFrontName()
    {
        return $this->_frontNameResolver->getFrontName();
    }
}
