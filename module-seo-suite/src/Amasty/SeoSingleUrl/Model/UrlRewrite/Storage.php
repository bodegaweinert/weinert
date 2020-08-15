<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


namespace Amasty\SeoSingleUrl\Model\UrlRewrite;

use Magento\CatalogUrlRewrite\Model\ResourceModel\Category\Product;

class Storage extends \Magento\UrlRewrite\Model\Storage\DbStorage
{
    public function findAllByDataWithoutCategory(array $data)
    {
        $rows = $this->doFindAllByDataWithoutCategory($data);

        $urlRewrites = [];
        foreach ($rows as $row) {
            $urlRewrites[] = $this->createUrlRewrite($row);
        }

        return $urlRewrites;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFindAllByDataWithoutCategory(array $data)
    {
        return $this->connection->fetchAll($this->prepareSelectWithoutCategory($data));
    }

    protected function prepareSelectWithoutCategory(array $data)
    {
        $select = $this->connection->select();
        $select->from(['url_rewrite' => $this->resource->getTableName('url_rewrite')])
            ->joinLeft(
                ['relation' => $this->resource->getTableName(Product::TABLE_NAME)],
                'url_rewrite.url_rewrite_id = relation.url_rewrite_id'
            )
            ->where('url_rewrite.entity_id IN (?)', $data['entity_id'])
            ->where('url_rewrite.entity_type = ?', $data['entity_type'])
            ->where('url_rewrite.store_id IN (?)', $data['store_id']);

        return $select;
    }
}
