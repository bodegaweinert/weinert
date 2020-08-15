<?php
namespace Combinatoria\Slider\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;

class MassDelete extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Combinatoria_Slider::delete');
    }
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $selectedIds = $this->getRequest()->getParam('selected');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $countSuccess = 0;
        $countError = 0;
        foreach( $selectedIds as $id ) {
            try {
                $model = $this->_objectManager->create('Combinatoria\Slider\Model\Slider');
                $model->load( $id );
                $model->delete();
                $countSuccess++;
            } catch ( \Exception $e ) {
                //Handle error.
                //$this->messageManager->addErrorMessage( $e->getMessage() );
                $countError++;
            }
        }

        if( $countSuccess > 0 ) {
            $this->messageManager->addSuccessMessage( __('Total of %1 record(s) were deleted.', $countSuccess ) );
        }
        if( $countError > 0 ) {
            $this->messageManager->addErrorMessage( __('Total of %1 record(s) were NOT deleted.', $countError ) );
        }

        return $resultRedirect->setPath('*/*/');
    }
}