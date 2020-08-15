<?php
namespace Combinatoria\Banner\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Photo extends Column
{
    const NAME = 'image';

    const ALT_FIELD = 'image';

    protected $_storeManager;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $mediaRelativePath = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
                if (isset($item['image'])) {
                    $logoPath = $mediaRelativePath.$item['image'];
                    $item[$fieldName . '_src'] = $logoPath;
                    $item[$fieldName . '_alt'] = $this->getAlt($item);
                    $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                        'combinatoria_banner/banner/edit',
                        ['id' => $item['id'], 'store' => $this->context->getRequestParam('store')]
                    );
                    $item[$fieldName . '_orig_src'] = $logoPath;
                }

            }
        }

        return $dataSource;
    }

    /**
     * @param array $row
     *
     * @return null|string
     */
    protected function getAlt($row)
    {
        $altField = self::ALT_FIELD;
        return isset($row[$altField]) ? $row[$altField] : null;
    }
}