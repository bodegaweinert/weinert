<?php

namespace Biztech\Easymaintenance\Observer;

use Magento\Security\Model\AdminSessionsManager;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;

class Auth 
{
	const ADMIN_SESSION_LOGIN = 'EASYADMINLOGGEDIN';

	protected $sessionsManager;
	protected $cookieInterface;
	protected $publiccookieMetadata;

	public function __construct(
        AdminSessionsManager $sessionsManager,
        CookieManagerInterface $cookieInterface,
        PublicCookieMetadata $publiccookieMetadata
    ) {
        $this->sessionsManager = $sessionsManager;
        $this->cookieInterface = $cookieInterface;
        $this->publiccookieMetadata = $publiccookieMetadata;
    }

    /**
     * @param \Magento\Backend\Model\Auth $authModel
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterLogin(\Magento\Backend\Model\Auth $authModel)
    {
        $this->sessionsManager->processLogin();
        if ($this->sessionsManager->getCurrentSession()) {
        	$this->publiccookieMetadata->setDuration(time() + (86400 * 30))
        								->setPath('/');
            $this->cookieInterface->setPublicCookie(self::ADMIN_SESSION_LOGIN,1,$this->publiccookieMetadata);
        }
    }

    /**
     * @param \Magento\Backend\Model\Auth $authModel
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeLogout(\Magento\Backend\Model\Auth $authModel)
    {
        if ($this->cookieInterface->getCookie(self::ADMIN_SESSION_LOGIN)) {
        	$this->publiccookieMetadata->setDuration(time() + (86400 * 30))
        								->setPath('/');
        	$this->cookieInterface->deleteCookie(self::ADMIN_SESSION_LOGIN, $this->publiccookieMetadata);
        }
        $this->sessionsManager->processLogout();
    }
}