<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Photo
 *
 * @ORM\Table(name="photo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PhotoRepository")
 */
class Photo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="image_fullname", type="string", length=255, nullable=true)
     */
    private $imageFullname;

    /**
     * @var string
     *
     * @ORM\Column(name="vk_id", type="string", length=255, nullable=true)
     */
    private $vkId;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set imageFullname
     *
     * @param string $imageFullname
     *
     * @return Photo
     */
    public function setImageFullname($imageFullname)
    {
        $this->imageFullname = $imageFullname;

        return $this;
    }

    /**
     * Get imageFullname
     *
     * @return string
     */
    public function getImageFullname()
    {
        return $this->imageFullname;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Album", inversedBy="photos")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id")
     */
    private $album;


    /**
     * Set album
     *
     * @param \AppBundle\Entity\Album $album
     *
     * @return Photo
     */
    public function setAlbum(\AppBundle\Entity\Album $album = null)
    {
        $this->album = $album;

        return $this;
    }

    /**
     * Get album
     *
     * @return \AppBundle\Entity\Album
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * Set vkId
     *
     * @param string $vkId
     *
     * @return Photo
     */
    public function setVkId($vkId)
    {
        $this->vkId = $vkId;

        return $this;
    }

    /**
     * Get vkId
     *
     * @return string
     */
    public function getVkId()
    {
        return $this->vkId;
    }
}
