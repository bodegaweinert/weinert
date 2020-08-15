<?php

namespace Biztech\Easymaintenance\Controller\Adminhtml\Notificationcontroller;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Model\Export\ConvertToCsv;
use Magento\Framework\App\Response\Http\FileFactory;

class ExportXml extends Action {

    /**
     * @var ConvertToCsv
     */
    protected $converter;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @param Context $context
     * @param ConvertToCsv $converter
     * @param FileFactory $fileFactory
     */
    public function __construct(
    Context $context, ConvertToCsv $converter, FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->converter = $converter;
        $this->fileFactory = $fileFactory;
    }

    /**
     * Export data provider to CSV
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute() {
        $layout = $this->_objectManager->get('\Magento\Framework\View\LayoutInterface');
        $fileName = 'notification.xml';
        $content = $layout->createBlock('Biztech\Easymaintenance\Block\Adminhtml\Notification\Grid')->getXml();

        return $this->fileFactory->create($fileName, $content, 'var');
    }

}
