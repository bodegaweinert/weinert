<?php
/**
 * Anowave Magento 2 Package
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * DO NOT EDIT or ADD to this file. Editing this file is direct violation of our license agreement.
 *
 * @category 	Anowave
 * @package 	Anowave_Package
 * @copyright 	Copyright (c) 2018 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Package\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

abstract class Base extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Key bits
     *
     * @var int
     */
    private $bits = 3;

    /**
     * Maximum key bits
     *
     * @var int
     */
    private $bits_max= 4;

    /**
     * Package name
     * @var string
     */
    protected $package = '';

    /**
     * Config path
     * @var string
     */
    protected $config = '';

    /**
     * Context
     *
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $_context = null;

    /**
     * Errors array
     *
     * @var array
     */
    private $errors = array();

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context)
    {
        $this->_context = $context;
    }

    /**
     * Check if customer is ligitimate to use the extension
     *
     * @return boolean
     */
    final public function legit()
    {
        return true;
        /**
         * Disable Anowave Modules for CRONJOBS
         */
        if (PHP_SAPI == 'cli')
        {
            $this->errors[] = __('Enhanced ecommerce cannot be used in cron tasks');

            return false;
        }

        if (!isset($_SERVER['HTTP_HOST']))
        {
            $this->errors[] = __('Cannot detect host');

            return false;
        }

        if (!extension_loaded('openssl'))
        {
            $this->errors[] = __('Extension requires OpenSSL');

            return false;
        }

        try
        {
            /**
             * Get license depending on area code
             */
            if (\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE == \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\State')->getAreaCode())
            {
                $license = $this->_context->getScopeConfig()->getValue($this->config);
            }
            else
            {
                $license = $this->_context->getScopeConfig()->getValue($this->config, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            }
        }
        catch (\Exception $e)
        {
            $this->errors[] = __($e->getMessage());
        }

        /**
         * Get key
         *
         * @var string
         */
        $key = (array) explode(chr(58), (string) $this->decrypt($license));

        /**
         * Check if key includes port and remove it from the []
         */
        if ($this->bits_max == count($key))
        {
            unset($key[2]);

            $key = array_values($key);
        }

        /**
         * Check if license key configuration is available
         */
        if (!$this->config)
        {
            $this->errors[] = __('Invalid license key configuration');

            return false;
        }

        /**
         * Check if package is available
         */
        if (!$this->package)
        {
            $this->errors[] = __('Invalid license key package');

            return false;
        }

        /**
         * Key must contain 3 nodes
         */
        if ($this->bits !== count($key))
        {
            /**
             * License key is invalid
             */
            $this->errors[] = __('Invalid license key');

            return false;
        }

        /**
         * Check if domain contains port
         */
        if (false !== strpos($_SERVER['HTTP_HOST'], chr(58)))
        {
            /**
             * Get host and port
             */
            list($host, $port) = explode(chr(58), $_SERVER['HTTP_HOST']);
        }
        else
        {
            /**
             * Get host
             */
            $host = $_SERVER['HTTP_HOST'];

            /**
             * Set port to null
             */
            $port = null;
        }

        /**
         * Check if license key is present. If so, allow localhost always.
         */
        if ('localhost' === $host)
        {
            return true;
        }

        /**
         * Package and extension should match
         */
        if (strtolower($host) === strtolower($key[1]))
        {
            if (strtoupper($this->package) === strtoupper($key[2]))
            {
                return true;
            }
            else
            {
                $this->errors[] = __('The provided license key is invalid for this package');
            }

        }
        else
        {
            $this->errors[] = __('The provided license key is invalid for domain:') . chr(32) . $_SERVER['HTTP_HOST'];
        }

        return false;
    }

    /**
     * Prevent extension from generating output
     *
     * @param string $content
     * @return NULL
     */
    final public function filter($content = '')
    {
        if (!$this->legit())
        {
            if (is_array($content))
            {
                return array();
            }

            if (is_numeric($content))
            {
                return 0;
            }

            if (is_string($content))
            {
                return '';
            }
        }

        return $content;
    }

    /**
     * Extend content
     *
     * @param mixed $content
     */
    final public function extend($content)
    {
        return $this->filter($content);
    }

    /**
     * Augment content
     *
     * @param mixed $content
     */
    final public function augment($content)
    {
        return $this->filter($content);
    }

    /**
     * Get license
     *
     * @return mixed|NULL
     */
    final public function license()
    {
        if ($this->legit())
        {
            return $this->_context->getScopeConfig()->getValue($this->config, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }

        return null;
    }

    /**
     * Notify
     *
     * @param \Magento\Framework\Message\ManagerInterface $messanger
     */
    final public function notify(\Magento\Framework\Message\ManagerInterface $messanger)
    {
        if (!$this->legit())
        {
            foreach ($this->errors as $error)
            {
                $messanger->addErrorMessage($error);
            }
        }

        return true;
    }

    /**
     * Decrypt key using password
     *
     * @param unknown $string
     * @param string $password
     * @return string|NULL
     */
    final private function decrypt($string)
    {
        if (extension_loaded('openssl'))
        {
            return openssl_decrypt($string, 'aes-128-cbc', openssl_decrypt('tfMyW8UoiI1or4W0q2teCG5dRuJ1MqqpGnYYYSp0dJQSykFOh1LMvqPCoG1E7Om6', 'aes-128-cbc', 'anowave'));
        }

        return null;
    }
}