<?php
namespace Combinatoria\Slider\Model;
use Combinatoria\Slider\Api\Data\SliderInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Slider extends AbstractModel implements SliderInterface, IdentityInterface
{
    /**
     * Slider's statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'combinatoria_slider_slider';

    /**
     * @var string
     */
    protected $_cacheTag = 'combinatoria_slider_slider';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'combinatoria_slider_slider';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Combinatoria\Slider\Model\ResourceModel\Slider');
    }

    /**
     * Prepare slider's statuses.
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
     * Get From
     *
     * @return string|null
     */
    public function getFrom() {
        return $this->getData(self::FROM);
    }

    /**
     * Get To
     *
     * @return string|null
     */
    public function getTo() {
        return $this->getData(self::TO);
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
     * Get Sort Order
     *
     * @return int
     */
    public function getSortOrder() {
        return (bool) $this->getData(self::SORT_ORDER);
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
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Set Image
     *
     * @param string $image
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setImage($image) {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * Set Alt
     *
     * @param string $alt
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setAlt($alt) {
        return $this->setData(self::ALT, $alt);
    }

    /**
     * Set Link
     *
     * @param string $link
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setLink($link) {
        return $this->setData(self::LINK, $link);
    }

    /**
     * Set From
     *
     * @param string $from
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setFrom($from) {
        return $this->setData(self::FROM, $from);
    }

    /**
     * Set To
     *
     * @param string $to
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setTo($to) {
        return $this->setData(self::TO, $to);
    }

    /**
     * Set Creation time
     *
     * @param string $creationTime
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setCreationTime($creationTime) {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Set Update time
     *
     * @param string $updateTime
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setUpdateTime($updateTime) {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * Set Sort Order
     *
     * @param int $sortOrder
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setSortOrder($sortOrder) {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * Set is mobile
     *
     * @param int|bool $isMobile
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setIsMobile($isMobile) {
        return $this->setData(self::IS_MOBILE, $isMobile);
    }

    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setIsActive($isActive) {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }
}