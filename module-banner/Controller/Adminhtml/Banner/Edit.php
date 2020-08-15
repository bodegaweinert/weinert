<?php
namespace Combinatoria\Banner\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Combinatoria_Banner::save');
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('Combinatoria_Banner::combinatoria_banner_section')
            ->addBreadcrumb(__('Combinatoria'), __('Combinatoria'))
            ->addBreadcrumb(__('Manage Banner'), __('Manage Banner'));

        return $resultPage;
    }

    /**
     * Edit banner
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Combinatoria\Banner\Model\Banner');

        if ( $id ) {
            $model->load( $id );
            if ( !$model->getId() ) {
                $this->messageManager->addErrorMessage( __('This banner no longer exists.') );
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);

        if ( !empty( $data ) ) {
            $model->setData( $data );
        }

        $this->_coreRegistry->register('combinatoria_banner_banner', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();

        $resultPage->addBreadcrumb(
            $id ? __('Edit Banner') : __('New Banner'),
            $id ? __('Edit Banner') : __('New Banner')
        );

        $resultPage->getConfig()->getTitle()->prepend( __('Banners') );
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Banner'));

        return $resultPage;
    }
}