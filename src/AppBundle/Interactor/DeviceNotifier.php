<?php
/**
 * Created by IntelliJ IDEA.
 * User: arik
 * Date: 5/27/15
 * Time: 1:56 PM
 */

namespace AppBundle\Interactor;


use AppBundle\Entity\Area;
use AppBundle\Entity\Device;
use AppBundle\Entity\Siren;
use Doctrine\Bundle\DoctrineBundle\Registry;

class DeviceNotifier {

    protected $doctrine;
    protected $siren;

    protected $affectedDevices;

    public function __construct(Registry $doctrine, Siren $siren){

        $this->doctrine = $doctrine;
        $this->siren = $siren;

    }

    public function notifyDevices(){

        $this->findAffectedDevices();
        $this->notifyAffectedDevices();

    }

    private function findAffectedDevices(){

        $em = $this->doctrine->getEntityManager();

        /**
         * @var Area[] $sirenAreas
         */
        $sirenAreas = $this->siren->getAreas();

        $selectorString = '';
        $parameters = [];

        foreach($sirenAreas as $currentArea) {


            $currentAreaHash = md5($currentArea->getCode());

            // this sophistication below may loke menacing, but it's actually pretty simply

            /*
             * let's assume we have the bounding box. The latitude is irrelevant, but the longitude is between 30 and 50
             * in that case, we need to check that the longitude is bigger than 30 and smaller than 50
             *
             * However, if the bounding longitude is between 150 and -160 (across the 180/-180 longitude)
             * in that case, we have two scenarios: either the current longitude is bigger than 0 (approaching 180 from below)
             * or it is smaller than zero (approaching -180 from above)
             *
             * if it is smaller than zero, i. e. to the east of 180/-180, we need to make a different comparison with the west bound: we subtract 360 from the west bound
             * conversely, if it is bigger, i. e. to the west of 180/-180, we need to make a different comparison with the east bound: we add 360 to the east bound
             *
             */

            $selectorString .= '

            (

                devices.lastKnownLatitude < :northEdgeLatitude'.$currentAreaHash.' AND
                devices.lastKnownLatitude > :southEdgeLatitude'.$currentAreaHash.' AND
                (
                    (
                      devices.lastKnownLongitude > :westEdgeLongitude'.$currentAreaHash.' AND
                      devices.lastKnownLongitude < :eastEdgeLongitude'.$currentAreaHash.'
                    )
                    OR
                    (
                        :eastEdgeLongitude'.$currentAreaHash.' < :westEdgeLongitude'.$currentAreaHash.' AND
                        (
                            (
                                devices.lastKnownLongitude < 0 AND
                                devices.lastKnownLongitude + 360 > :westEdgeLongitude'.$currentAreaHash.' AND
                                devices.lastKnownLongitude < :eastEdgeLongitude'.$currentAreaHash.'
                            )
                            OR
                            (
                                devices.lastKnownLongitude > 0 AND
                                devices.lastKnownLongitude > :westEdgeLongitude'.$currentAreaHash.' AND
                                devices.lastKnownLongitude < :eastEdgeLongitude'.$currentAreaHash.' + 360
                            )
                        )

                    )

                )

            ) OR ';

            $parameters['northEdgeLatitude'.$currentAreaHash] = $currentArea->getNorthEdgeLatitude();
            $parameters['southEdgeLatitude'.$currentAreaHash] = $currentArea->getSouthEdgeLatitude();
            $parameters['westEdgeLongitude'.$currentAreaHash] = $currentArea->getWestEdgeLongitude();
            $parameters['eastEdgeLongitude'.$currentAreaHash] = $currentArea->getEastEdgeLongitude();

        }

        $selectorString = substr($selectorString, 0, -3);

        $queryString = 'SELECT devices FROM AppBundle\Entity\Device devices WHERE '.$selectorString;
        $query = $em->createQuery($queryString);

        $query->setParameters($parameters);

        echo '<pre>';
        echo $queryString;
        echo '</pre>';

        /** @var Device[] $devices */
        $devices = $query->getResult();
        $this->affectedDevices = $devices;

        echo 'Devices: '.count($devices).'<br/>';

        foreach($devices as $currentDevice){
            echo 'Device: '.$currentDevice->getOperatingSystem();
        }

    }

    private function notifyAffectedDevices(){

        // first of all, we send the important push notification

    }

}