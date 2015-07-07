<?php
/**
 * Created by IntelliJ IDEA.
 * User: arik
 * Date: 5/27/15
 * Time: 11:50 AM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Siren
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="devices")
 */
class Device {

    /**
     * @var
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    protected $operatingSystem = 'iOS';

    /**
     * @var
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $notificationToken;

    /**
     * @var
     *
     * @ORM\Column(type="decimal", scale=8, precision=15)
     */
    protected $lastKnownLatitude;

    /**
     * @var
     *
     * @ORM\Column(type="decimal", scale=8, precision=15)
     */
    protected $lastKnownLongitude;

    /**
     * @var
     *
     * @ORM\Column(type="bigint")
     */
    protected $locationUpdateTimestamp;


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
     * Set operatingSystem
     *
     * @param string $operatingSystem
     * @return Device
     */
    public function setOperatingSystem($operatingSystem)
    {
        $this->operatingSystem = $operatingSystem;

        return $this;
    }

    /**
     * Get operatingSystem
     *
     * @return string 
     */
    public function getOperatingSystem()
    {
        return $this->operatingSystem;
    }

    /**
     * Set notificationToken
     *
     * @param string $notificationToken
     * @return Device
     */
    public function setNotificationToken($notificationToken)
    {
        $this->notificationToken = $notificationToken;

        return $this;
    }

    /**
     * Get notificationToken
     *
     * @return string 
     */
    public function getNotificationToken()
    {
        return $this->notificationToken;
    }

    /**
     * Set lastKnownLatitude
     *
     * @param string $lastKnownLatitude
     * @return Device
     */
    public function setLastKnownLatitude($lastKnownLatitude)
    {
        $this->lastKnownLatitude = $lastKnownLatitude;

        return $this;
    }

    /**
     * Get lastKnownLatitude
     *
     * @return string 
     */
    public function getLastKnownLatitude()
    {
        return $this->lastKnownLatitude;
    }

    /**
     * Set lastKnownLongitude
     *
     * @param string $lastKnownLongitude
     * @return Device
     */
    public function setLastKnownLongitude($lastKnownLongitude)
    {
        $this->lastKnownLongitude = $lastKnownLongitude;

        return $this;
    }

    /**
     * Get lastKnownLongitude
     *
     * @return string 
     */
    public function getLastKnownLongitude()
    {
        return $this->lastKnownLongitude;
    }

    /**
     * Set locationUpdateTimestamp
     *
     * @param integer $locationUpdateTimestamp
     * @return Device
     */
    public function setLocationUpdateTimestamp($locationUpdateTimestamp)
    {
        $this->locationUpdateTimestamp = $locationUpdateTimestamp;

        return $this;
    }

    /**
     * Get locationUpdateTimestamp
     *
     * @return integer 
     */
    public function getLocationUpdateTimestamp()
    {
        return $this->locationUpdateTimestamp;
    }
}
