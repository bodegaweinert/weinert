<?php
namespace Combinatoria\Banner\Model;
use Combinatoria\Banner\Api\Data\BannerInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Banner extends AbstractModel implements BannerInterface, IdentityInterface
{
    /**
     * Banner's statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'combinatoria_banner_banner';

    /**
     * @var string
     */
    protected $_cacheTag = 'combinatoria_banner_banner';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'combinatoria_banner_banner';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Combinatoria\Banner\Model\ResourceModel\Banner');
    }

    /**
     * Prepare banner's statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Get Image
     *
     * @return string|null
     */
    public function getImage() {
        return $this->getData(self::IMAGE);
    }

    /**
     * Get Alt
     *
     * @return string|null
     */
    public function getAlt() {
        return $this->getData(self::ALT);
    }

    /**
     * Get Link
     *
     * @return string|null
     */
    public function getLink() {
        return $this->getData(self::LINK);
    }

    /**
     * Get Creation time
     *
     * @return string|null
     */
    public function getCreationTime() {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Get Update time
     *
     * @return string|null
     */
    public function getUpdateTime() {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * Is mobile
     *
     * @return bool|null
     */
    public function isMobile() {
        return (bool) $this->getData(self::IS_MOBILE);
    }

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive() {
        return (bool) $this->getData(self::IS_ACTIVE);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Set Image
     *
     * @param string $image
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     */
    public function setImage($image) {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * Set Alt
     *
     * @param string $alt
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     */
    public function setAlt($alt) {
        return $this->setData(self::ALT, $alt);
    }

    /**
     * Set Link
     *
     * @param string $link
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     */
    public function setLink($link) {
        return $this->setData(self::LINK, $link);
    }

    /**
     * Set Creation time
     *
     * @param string $creationTime
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     */
    public function setCreationTime($creationTime) {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Set Update time
     *
     * @param string $updateTime
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     */
    public function setUpdateTime($updateTime) {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }
    

    /**
     * Set is mobile
     *
     * @param int|bool $isMobile
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     */
    public function setIsMobile($isMobile) {
        return $this->setData(self::IS_MOBILE, $isMobile);
    }

    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     */
    public function setIsActive($isActive) {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }
}