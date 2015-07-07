<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Area;
use AppBundle\Entity\Device;
use AppBundle\Entity\Siren;
use AppBundle\Interactor\AlarmDetector;
use AppBundle\Interactor\AreaScraper;
use AppBundle\Interactor\DeviceNotifier;
use Doctrine\Common\Persistence\ObjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller {

    /**
     * @Route("/test")
     */
    public function testResponsiveness(){
        return new JsonResponse(['status' => 1, 'message' => 'success']);
    }

    /**
     * @param $lastKnownID
     * @Route("/alarms")
     * @Route("/alarms/{lastKnownID}")
     */
    public function getAlarmsAction($lastKnownID = null){

        $doctrine = $this->getDoctrine();
        $entityManager = $doctrine->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('sirens')
            ->from('AppBundle:Siren', 'sirens');

        if($lastKnownID != null){
            $queryBuilder->where('sirens.alertIdentifier > :lastKnownID')
                ->setParameter('lastKnownID', $lastKnownID);
        }

        // we wanna get the latest results first, right?
        $queryBuilder->orderBy('sirens.alertIdentifier', 'DESC');

        $sirenDetails = [];
        $areaDetails = [];

        /**
         * @var Siren[] $results;
         */
        $results = $queryBuilder->getQuery()->getResult();
        foreach($results as $currentSiren){

            // they realized he wanted to cook. He had no formal training.

            $currentDetails = [];
            $currentDetails['alert_id'] = $currentSiren->getId();
            $currentDetails['timestamp'] = $currentSiren->getTimestamp();

            foreach($currentSiren->getAreas() as $currentArea){

                /* @var Area $currentArea; */

                $currentDetails['area_ids'][] = $currentArea->getGooglePlaceIdentifier();

                if(isset($areaDetails[$currentArea->getGooglePlaceIdentifier()])){
                    continue;
                }

                $currentAreaDetails = [];
                $currentAreaDetails['area_id'] = $currentArea->getGooglePlaceIdentifier();
                $currentAreaDetails['toponym_long'] = $currentArea->getToponymLong();
                $currentAreaDetails['toponym_short'] = $currentArea->getToponymShort();

                $areaDetails[$currentArea->getGooglePlaceIdentifier()] = $currentAreaDetails;

            }

            $sirenDetails[] = $currentDetails;

        }

        $outputData = ['sirens' => $sirenDetails, 'areas' => $areaDetails];

        return new JsonResponse(['status' => 1, 'response' => $outputData]);

        die();

        // let's get all the sirens after the last known ID

        /**
         * @var ObjectRepository $sirenRepository;
         */
        $sirenRepository = $doctrine->getRepository('AppBundle:Siren');




    }

    /**
     * @Route("/alarms/detect", name="homepage")
     */
    public function detectAlarmsAction() {

        $doctrine = $this->getDoctrine();

        $detector = new AlarmDetector($doctrine);
        $detector->detectAlarms();

        // $alertLocator = new AlertLocator();
        return new Response();
        // return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/areas/scrape")
     */
    public function scrapeAreasAction() {

        $kernel = $this->get('kernel');
        $doctrine = $this->getDoctrine();

        $areaScraper = new AreaScraper($kernel, $doctrine);
        $areaScraper->scrapeAreas();

        return new Response();

    }

    /**
     * @Route("/devices/register/{latitude}/{longitude}")
     */
    public function registerTestDeviceAction($latitude, $longitude) {

        $doctrine = $this->getDoctrine();
        $em = $doctrine->getEntityManager();

        $device = new Device();
        $device->setLocationUpdateTimestamp(time());
        $device->setLastKnownLatitude($latitude);
        $device->setLastKnownLongitude($longitude);

        $em->persist($device);
        $em->flush();

        return new Response('Device registered with coordinates ' . $latitude . ' | ' . $longitude);

    }

    /**
     * @Route("/devices/notify/{alertIdentifier}")
     */
    public function notifyDevices($alertIdentifier){

        $doctrine = $this->getDoctrine();
        $repository = $doctrine->getRepository('AppBundle:Siren');

        $siren = $repository->findOneByAlertIdentifier($alertIdentifier);
        $deviceNotifier = new DeviceNotifier($doctrine, $siren);

        // echo '<pre>';
        $deviceNotifier->notifyDevices();
        // echo '</pre>';

        return new Response();

    }

}
