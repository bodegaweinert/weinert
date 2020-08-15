<?php
namespace Combinatoria\Slider\Api\Data;

/**
 * Interface SliderInterface
 * @package Combinatoria\Slider\Api\Data
 */
interface SliderInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const IMAGE = 'image';
    const ALT = 'alt';
    const LINK = 'link';
    const FROM = 'from';
    const TO = 'to';
    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME = 'update_time';
    const SORT_ORDER = 'sort_order';
    const IS_MOBILE = 'is_mobile';
    const IS_ACTIVE = 'is_active';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Image
     *
     * @return string|null
     */
    public function getImage();

    /**
     * Get Alt
     *
     * @return string|null
     */
    public function getAlt();

    /**
     * Get Link
     *
     * @return string|null
     */
    public function getLink();

    /**
     * Get From
     *
     * @return string|null
     */
    public function getFrom();

    /**
     * Get To
     *
     * @return string|null
     */
    public function getTo();


    /**
     * Get Creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get Update time
     *
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Get Sort Order
     *
     * @return int|null
     */
    public function getSortOrder();

    /**
     * Is mobile
     *
     * @return bool|null
     */
    public function isMobile();

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setId($id);

    /**
     * Set Image
     *
     * @param string $image
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setImage($image);

    /**
     * Set Alt
     *
     * @param string $alt
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setAlt($alt);

    /**
     * Set Link
     *
     * @param string $link
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setLink($link);

    /**
     * Set From
     *
     * @param string $from
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setFrom($from);

    /**
     * Set To
     *
     * @param string $to
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setTo($to);

    /**
     * Set Creation time
     *
     * @param string $creationTime
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set Update time
     *
     * @param string $updateTime
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * Set Sort Order
     *
     * @param int $sortOrder
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * Set is mobile
     *
     * @param int|bool $isMobile
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setIsMobile($isMobile);

    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \Combinatoria\Slider\Api\Data\SliderInterface
     */
    public function setIsActive($isActive);
}