<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Plugin\Cms\Block;

use Magento\Cms\Block\Page as MagentoPageBlock;
use Amasty\CrossLinks\Helper\Data as CrossLinksHelper;

class Page
{
    /**
     * @var \Amasty\CrossLinks\Model\ReplaceManager
     */
    protected $replaceManager;

    /**
     * Page constructor.
     * @param \Amasty\CrossLinks\Model\ReplaceManager $replaceManager
     */
    public function __construct(\Amasty\CrossLinks\Model\ReplaceManager $replaceManager)
    {
        $this->replaceManager = $replaceManager;
    }

    /**
     * @param MagentoPageBlock $subject
     * @param $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterToHtml(MagentoPageBlock $subject, $result)
    {
        $this->replaceManager->setEntityType(CrossLinksHelper::TYPE_CMS);
        return $this->replaceManager->processCmsPageContent($result);
        return $result;
    }
}
