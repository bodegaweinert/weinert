<?php
namespace Combinatoria\Banner\Api\Data;

/**
 * Interface BannerInterface
 * @package Combinatoria\Banner\Api\Data
 */
interface BannerInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const IMAGE = 'image';
    const ALT = 'alt';
    const LINK = 'link';
    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME = 'update_time';
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
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     */
    public function setId($id);

    /**
     * Set Image
     *
     * @param string $image
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     */
    public function setImage($image);

    /**
     * Set Alt
     *
     * @param string $alt
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     */
    public function setAlt($alt);

    /**
     * Set Link
     *
     * @param string $link
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     */
    public function setLink($link);
    
    /**
     * Set Creation time
     *
     * @param string $creationTime
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set Update time
     *
     * @param string $updateTime
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * Set is mobile
     *
     * @param int|bool $isMobile
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     */
    public function setIsMobile($isMobile);

    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \Combinatoria\Banner\Api\Data\BannerInterface
     */
    public function setIsActive($isActive);
}