<?php
/**
 * Created by IntelliJ IDEA.
 * User: arik
 * Date: 5/27/15
 * Time: 9:21 AM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Area
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="areas")
 */
class Area {

    /**
     * @var
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Siren", mappedBy="Area")
     */
    protected $sirens;

    /**
     * @var
     *
     * @ORM\Column(type="integer", unique=true)
     */
    protected $code;

    /**
     * @var
     *
     * @ORM\Column(type="string", length=100)
     */
    protected $googlePlaceIdentifier;

    /**
     * @var
     *
     * @ORM\Column(type="string", length=100)
     */
    protected $toponymLong;

    /**
     * @var
     *
     * @ORM\Column(type="string", length=100)
     */
    protected $toponymShort;

    /**
     * @var
     *
     * @ORM\Column(type="decimal", scale=8, precision=15)
     */
    protected $centerLatitude;

    /**
     * @var
     *
     * @ORM\Column(type="decimal", scale=8, precision=15)
     */
    protected $centerLongitude;

    /**
     * @var
     *
     * @ORM\Column(type="decimal", scale=8, precision=15)
     */
    protected $northEdgeLatitude;

    /**
     * @var
     *
     * @ORM\Column(type="decimal", scale=8, precision=15)
     */
    protected $southEdgeLatitude;

    /**
     * @var
     *
     * @ORM\Column(type="decimal", scale=8, precision=15)
     */
    protected $westEdgeLongitude;

    /**
     * @var
     *
     * @ORM\Column(type="decimal", scale=8, precision=15)
     */
    protected $eastEdgeLongitude;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sirens = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param integer $code
     * @return Area
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set googlePlaceIdentifier
     *
     * @param string $googlePlaceIdentifier
     * @return Area
     */
    public function setGooglePlaceIdentifier($googlePlaceIdentifier)
    {
        $this->googlePlaceIdentifier = $googlePlaceIdentifier;

        return $this;
    }

    /**
     * Get googlePlaceIdentifier
     *
     * @return string 
     */
    public function getGooglePlaceIdentifier()
    {
        return $this->googlePlaceIdentifier;
    }

    /**
     * Set toponymLong
     *
     * @param string $toponymLong
     * @return Area
     */
    public function setToponymLong($toponymLong)
    {
        $this->toponymLong = $toponymLong;

        return $this;
    }

    /**
     * Get toponymLong
     *
     * @return string 
     */
    public function getToponymLong()
    {
        return $this->toponymLong;
    }

    /**
     * Set toponymShort
     *
     * @param string $toponymShort
     * @return Area
     */
    public function setToponymShort($toponymShort)
    {
        $this->toponymShort = $toponymShort;

        return $this;
    }

    /**
     * Get toponymShort
     *
     * @return string 
     */
    public function getToponymShort()
    {
        return $this->toponymShort;
    }

    /**
     * Set centerLatitude
     *
     * @param string $centerLatitude
     * @return Area
     */
    public function setCenterLatitude($centerLatitude)
    {
        $this->centerLatitude = $centerLatitude;

        return $this;
    }

    /**
     * Get centerLatitude
     *
     * @return string 
     */
    public function getCenterLatitude()
    {
        return $this->centerLatitude;
    }

    /**
     * Set centerLongitude
     *
     * @param string $centerLongitude
     * @return Area
     */
    public function setCenterLongitude($centerLongitude)
    {
        $this->centerLongitude = $centerLongitude;

        return $this;
    }

    /**
     * Get centerLongitude
     *
     * @return string 
     */
    public function getCenterLongitude()
    {
        return $this->centerLongitude;
    }

    /**
     * Set northEdgeLatitude
     *
     * @param string $northEdgeLatitude
     * @return Area
     */
    public function setNorthEdgeLatitude($northEdgeLatitude)
    {
        $this->northEdgeLatitude = $northEdgeLatitude;

        return $this;
    }

    /**
     * Get northEdgeLatitude
     *
     * @return string 
     */
    public function getNorthEdgeLatitude()
    {
        return $this->northEdgeLatitude;
    }

    /**
     * Set southEdgeLatitude
     *
     * @param string $southEdgeLatitude
     * @return Area
     */
    public function setSouthEdgeLatitude($southEdgeLatitude)
    {
        $this->southEdgeLatitude = $southEdgeLatitude;

        return $this;
    }

    /**
     * Get southEdgeLatitude
     *
     * @return string 
     */
    public function getSouthEdgeLatitude()
    {
        return $this->southEdgeLatitude;
    }

    /**
     * Set westEdgeLongitude
     *
     * @param string $westEdgeLongitude
     * @return Area
     */
    public function setWestEdgeLongitude($westEdgeLongitude)
    {
        $this->westEdgeLongitude = $westEdgeLongitude;

        return $this;
    }

    /**
     * Get westEdgeLongitude
     *
     * @return string 
     */
    public function getWestEdgeLongitude()
    {
        return $this->westEdgeLongitude;
    }

    /**
     * Set eastEdgeLongitude
     *
     * @param string $eastEdgeLongitude
     * @return Area
     */
    public function setEastEdgeLongitude($eastEdgeLongitude)
    {
        $this->eastEdgeLongitude = $eastEdgeLongitude;

        return $this;
    }

    /**
     * Get eastEdgeLongitude
     *
     * @return string 
     */
    public function getEastEdgeLongitude()
    {
        return $this->eastEdgeLongitude;
    }

    /**
     * Add sirens
     *
     * @param \AppBundle\Entity\Siren $sirens
     * @return Area
     */
    public function addSiren(\AppBundle\Entity\Siren $sirens)
    {
        $this->sirens[] = $sirens;

        return $this;
    }

    /**
     * Remove sirens
     *
     * @param \AppBundle\Entity\Siren $sirens
     */
    public function removeSiren(\AppBundle\Entity\Siren $sirens)
    {
        $this->sirens->removeElement($sirens);
    }

    /**
     * Get sirens
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSirens()
    {
        return $this->sirens;
    }
}
