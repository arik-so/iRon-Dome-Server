<?php
/**
 * Created by IntelliJ IDEA.
 * User: arik
 * Date: 5/27/15
 * Time: 6:22 PM
 */

namespace AppBundle\Entity;


/**
 * @Doctrine\ORM\Mapping\Entity
 * @Doctrine\ORM\Mapping\Table(name="notifications")
 */
class Notification {

    /**
     * @Doctrine\ORM\Mapping\Id
     * @Doctrine\ORM\Mapping\GeneratedValue(strategy="AUTO")
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    protected $id;


    protected $siren;


    protected $device;


    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=100)
     */
    protected $notificationDeliveryStatus;

}