<?php
/**
 *
 * SM Social Login - Version 1.0.0
 * Copyright (c) 2017 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 */
 
namespace Sm\SocialLogin\Controller\Popup;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\SecurityViolationException;
use Magento\Framework\Controller\Result\JsonFactory;

class Forgot extends Action
{
	protected $customerAccountManagement;

	protected $escaper;
	
	protected $session;

	protected $resultJsonFactory;
	
	protected $captchaHelper;
	
	protected $socialHelper;

	public function __construct(
		Context $context,
		Session $customerSession,
		AccountManagementInterface $customerAccountManagement,
		Escaper $escaper,
		JsonFactory $resultJsonFactory,
		\Magento\Captcha\Helper\Data $captchaHelper,
		\Sm\SocialLogin\Helper\Data $socialHelper
	)
	{
		$this->session                   = $customerSession;
		$this->customerAccountManagement = $customerAccountManagement;
		$this->escaper                   = $escaper;
		$this->resultJsonFactory         = $resultJsonFactory;
		$this->captchaHelper             = $captchaHelper;
		$this->socialHelper              = $socialHelper;

		parent::__construct($context);
	}

	public function execute()
	{
		$resultJson = $this->resultJsonFactory->create();
		$result = array(
			'success' => false,
			'message' => array()
		);
		$formId       = 'user_forgotpassword';
		$captchaModel = $this->captchaHelper->getCaptcha($formId);
		if ($captchaModel->isRequired()) {
			if (!$captchaModel->isCorrect($this->socialHelper->captchaResolve($this->getRequest(), $formId))) {
				$result['message'] = __('Incorrect CAPTCHA.');

				return $resultJson->setData($result);
			}
			$captchaModel->generate();
			$result['imgSrc'] = $captchaModel->getImgSrc();
		}

		$email = (string)$this->getRequest()->getPost('email');
		if ($email) {
			if (!\Zend_Validate::is($email, 'EmailAddress')) {
				$this->session->setForgottenEmail($email);
				$result['message'][] = __('Please correct the email address.');
			}

			try {
				$this->customerAccountManagement->initiatePasswordReset(
					$email,
					AccountManagement::EMAIL_RESET
				);
				$result['success']   = true;
				$result['message'][] = __('If there is an account associated with %1 you will receive an email with a link to reset your password.', $this->escaper->escapeHtml($email));
			} catch (NoSuchEntityException $e) {
				$result['success']   = true;
				$result['message'][] = __('If there is an account associated with %1 you will receive an email with a link to reset your password.', $this->escaper->escapeHtml($email));
			} catch (SecurityViolationException $exception) {
				$result['error']     = true;
				$result['message'][] = $exception->getMessage();
			} catch (\Exception $exception) {
				$result['error']     = true;
				$result['message'][] = __('We\'re unable to send the password reset email.');
			}
		}
		return $resultJson->setData($result);
	}
}
