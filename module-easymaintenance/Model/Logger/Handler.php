<?php

namespace Biztech\Easymaintenance\Model\Logger;

/**
* 
*/
class Handler extends \Magento\Framework\Logger\Handler\Base
{
	protected $loggerType = Logger::DEBUG;

	protected $fileName = '/var/log/easymaintenance.log';

}