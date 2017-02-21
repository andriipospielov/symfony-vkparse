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
     * @var int
     *
     * @ORM\Column(name="album_id", type="integer")
     */
    private $albumId;


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
     * Set albumId
     *
     * @param integer $albumId
     *
     * @return Photo
     */
    public function setAlbumId($albumId)
    {
        $this->albumId = $albumId;

        return $this;
    }

    /**
     * Get albumId
     *
     * @return int
     */
    public function getAlbumId()
    {
        return $this->albumId;
    }
}

