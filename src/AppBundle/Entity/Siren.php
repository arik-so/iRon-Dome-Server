<?php
/**
 * Created by IntelliJ IDEA.
 * User: arik
 * Date: 5/26/15
 * Time: 11:00 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Siren
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="sirens")
 */
class Siren {

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
     * @ORM\Column(type="string", length=100)
     */
    protected $alertIdentifier;

    /**
     * @var
     *
     * @ORM\Column(type="bigint")
     */
    protected $timestamp;

    /**
     * @var
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Area", inversedBy="sirens")
     * @ORM\JoinTable(name="siren_areas")
     */
    protected $areas;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->areas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->timestamp = time();
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
     * Add areas
     *
     * @param \AppBundle\Entity\Area $areas
     * @return Siren
     */
    public function addArea(\AppBundle\Entity\Area $areas)
    {
        $this->areas[] = $areas;

        return $this;
    }

    /**
     * Remove areas
     *
     * @param \AppBundle\Entity\Area $areas
     */
    public function removeArea(\AppBundle\Entity\Area $areas)
    {
        $this->areas->removeElement($areas);
    }

    /**
     * Get areas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * Set alertIdentifier
     *
     * @param string $alertIdentifier
     * @return Siren
     */
    public function setAlertIdentifier($alertIdentifier)
    {
        $this->alertIdentifier = $alertIdentifier;

        return $this;
    }

    /**
     * Get alertIdentifier
     *
     * @return string 
     */
    public function getAlertIdentifier()
    {
        return $this->alertIdentifier;
    }

    /**
     * Set timestamp
     *
     * @param integer $timestamp
     * @return Siren
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return integer 
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
