<?php
namespace Combinatoria\Banner\Controller\Adminhtml\Banner;

use Combinatoria\Banner\Model\BannerFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Backend\App\Action;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{
    /**
     * @var BannerFactory
     */
    private $bannerFactory;
    /**
     * @var \Combinatoria\Banner\Model\ResourceModel\Banner
     */
    private $bannerResource;

    protected $fileSystem;

    protected $uploaderFactory;

    protected $allowedExtensions = ['jpg','jpeg','gif','png'];

    protected $fileId = 'photo_uploader';

    protected $bannerDirectory = 'images';

    /**
     * @param Action\Context $context
//     * @param \Combinatoria\Banner\Api\BannerRepositoryInterface $bannerRepositoryInterface
//     * @param \Combinatoria\Banner\Api\Data\BannerInterface $bannerInterface
     * @param \Combinatoria\Banner\Model\BannerFactory $bannerFactory
     * @param \Combinatoria\Banner\Model\ResourceModel\Banner $bannerResource
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     */
    public function __construct(
        Action\Context $context,

        BannerFactory $bannerFactory,
        \Combinatoria\Banner\Model\ResourceModel\Banner $bannerResource,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory
    )
    {

        $this->bannerFactory = $bannerFactory;
        $this->bannerResource = $bannerResource;
        $this->fileSystem = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;
        parent::__construct( $context );
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Combinatoria_Banner::save');
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
                        $file = $this->bannerDirectory . '/'. $uploadedFile['file'];
                        $formData['image'] = $file;
                    }
                } catch ( \Exception $e ) {
                    $this->messageManager->addErrorMessage( __( $e->getMessage() ) );
                }
            }

            //TODO: Replace with repository pattern implementation.
            $bannerModel = $this->_objectManager->create('\Combinatoria\Banner\Model\Banner');
            $bannerModel->setData( $formData );
            $bannerModel->save();
            $this->messageManager->addSuccessMessage( __('The banner has been successfully saved.') );

        } catch ( CouldNotSaveException $e ) {
            $this->messageManager->addErrorMessage( $e->getMessage() );
            $this->_redirect('combinatoria_banner/banner/new');
        }

        $this->_redirect('combinatoria_banner/banner/index');
    }

    public function getDestinationPath()
    {
        return $this->fileSystem
            ->getDirectoryWrite(DirectoryList::MEDIA)
            ->getAbsolutePath( $this->bannerDirectory );
    }
}