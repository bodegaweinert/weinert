<?php
namespace Combinatoria\Slider\Controller\Adminhtml\Slider;

use Combinatoria\Slider\Model\SliderFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Backend\App\Action;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{
    /**
     * @var SliderFactory
     */
    private $sliderFactory;
    /**
     * @var \Combinatoria\Slider\Model\ResourceModel\Slider
     */
    private $sliderResource;

    protected $fileSystem;

    protected $uploaderFactory;

    protected $allowedExtensions = ['jpg','jpeg','gif','png'];

    protected $fileId = 'photo_uploader';

    protected $sliderDirectory = 'images';

    /**
     * @param Action\Context $context
//     * @param \Combinatoria\Slider\Api\SliderRepositoryInterface $sliderRepositoryInterface
//     * @param \Combinatoria\Slider\Api\Data\SliderInterface $sliderInterface
     * @param \Combinatoria\Slider\Model\SliderFactory $sliderFactory
     * @param \Combinatoria\Slider\Model\ResourceModel\Slider $sliderResource
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     */
    public function __construct(
        Action\Context $context,

        SliderFactory $sliderFactory,
        \Combinatoria\Slider\Model\ResourceModel\Slider $sliderResource,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory
    )
    {

        $this->sliderFactory = $sliderFactory;
        $this->sliderResource = $sliderResource;
        $this->fileSystem = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;
        parent::__construct( $context );
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Combinatoria_Slider::save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $formData= $this->getRequest()->getParams();

            if( count( $_FILES ) > 0 && $_FILES['photo_uploader']['error'] == 0 ) {
                try {
                    $destinationPath = $this->getDestinationPath();
                    $uploader = $this->uploaderFactory->create( ['fileId' => $this->fileId] );
                    $uploader->setAllowCreateFolders( true );
                    $uploader->setAllowedExtensions( $this->allowedExtensions );
                    $uploader->setFilesDispersion( false );
                    $uploadedFile = $uploader->save( $destinationPath );

                    if ( !$uploadedFile ) {
                        throw new LocalizedException( __( 'File cannot be saved to path: $1', $destinationPath ) );
                    } else {
                        $file = $this->sliderDirectory . '/'. $uploadedFile['file'];
                        $formData['image'] = $file;
                    }
                } catch ( \Exception $e ) {
                    $this->messageManager->addErrorMessage( __( $e->getMessage() ) );
                }
            }

            //TODO: Replace with repository pattern implementation.
            $sliderModel = $this->_objectManager->create('\Combinatoria\Slider\Model\Slider');
            $sliderModel->setData( $formData );
            $sliderModel->save();
            $this->messageManager->addSuccessMessage( __('The slider has been successfully saved.') );

        } catch ( CouldNotSaveException $e ) {
            $this->messageManager->addErrorMessage( $e->getMessage() );
            $this->_redirect('combinatoria_slider/slider/new');
        }

        $this->_redirect('combinatoria_slider/slider/index');
    }

    public function getDestinationPath()
    {
        return $this->fileSystem
            ->getDirectoryWrite(DirectoryList::MEDIA)
            ->getAbsolutePath( $this->sliderDirectory );
    }
}