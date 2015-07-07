<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Device;
use AppBundle\Interactor\AlarmDetector;
use AppBundle\Interactor\AreaScraper;
use AppBundle\Interactor\DeviceNotifier;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller {

    /**
     * @Route("/test", name="homepage")
     */
    public function testResponsiveness(){
        return new JsonResponse(['status' => 1, 'message' => 'success']);
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
