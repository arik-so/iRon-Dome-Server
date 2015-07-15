<?php
/**
 * Created by IntelliJ IDEA.
 * User: arik
 * Date: 7/15/15
 * Time: 1:55 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Cron
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="crons")
 */
class Cron {

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
     * @ORM\Column(type="bigint", unique=true)
     */
    protected $timestamp;

    /**
     * @var
     *
     * @ORM\Column(type="string", length=100)
     */
    protected $action;


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
     * Set timestamp
     *
     * @param integer $timestamp
     * @return Cron
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

    /**
     * Set action
     *
     * @param string $action
     * @return Cron
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }
}
