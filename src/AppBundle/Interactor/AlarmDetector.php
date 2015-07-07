<?php
/**
 * Created by IntelliJ IDEA.
 * User: arik
 * Date: 5/26/15
 * Time: 11:25 PM
 */

namespace AppBundle\Interactor;


use AppBundle\Entity\Siren;
use Doctrine\Bundle\DoctrineBundle\Registry;

class AlarmDetector {

    protected $doctrine;
    protected $alarmJSON;
    protected $alertIdentifier;
    protected $affectedAreaCodes;

    /**
     * AlertLocator constructor.
     */
    public function __construct(Registry $doctrine){

        $this->doctrine = $doctrine;

        // first of all, we need to get a source

        $this->alarmJSON = '{
"id" : "1405974086170",
"title" : "פיקוד העורף התרעה במרחב ",
"data" : [
"אשקלון 238, עוטף עזה 238",
"31"
]
}
';

    }

    public function detectAlarms($emulate = false){

        if(!$emulate) {
            $this->fetchAlarmJSON();
        }


        try {
            $this->parseAlarmDetails();
        }catch (\Exception $e){ // there is a duplicate alarm
            echo $e->getMessage();
            return;
        }

        $this->storeAlarmDetails();

    }

    /**
     * @throws \Exception
     */
    private function parseAlarmDetails(){

        $alarmDetails = json_decode($this->alarmJSON, true);
        print_r($alarmDetails);

        $this->affectedAreaCodes = [];
        $this->alertIdentifier = $alarmDetails['id'];

        $repository = $this->doctrine->getRepository('AppBundle:Siren');
        $duplicateSiren = $repository->findOneByAlertIdentifier($this->alertIdentifier);

        if($duplicateSiren instanceof Siren){
            throw new \Exception("Siren already registered.");
        }

        $affectedCodesRaw = $alarmDetails['data'];

        foreach($affectedCodesRaw as $currentCodeRaw) {

            $codeParts = explode(',', $currentCodeRaw);

            foreach ($codeParts as $currentPartialCodeRaw) {

                $currentCode = preg_replace('/[^0-9]/', null, $currentPartialCodeRaw);

                if (empty($currentCode)) {
                    continue;
                } // this code is weird
                if (in_array($currentCode, $this->affectedAreaCodes)) {
                    continue;
                } // we already know this code

                $this->affectedAreaCodes[] = $currentCode;

            }

        }

        print_r($this->affectedAreaCodes);



    }

    private function storeAlarmDetails(){

        $siren = new Siren();
        $siren->setAlertIdentifier($this->alertIdentifier);

        $em = $this->doctrine->getEntityManager();
        $repository = $em->getRepository('AppBundle:Area');

        foreach($this->affectedAreaCodes as $currentCode){

            $currentArea = $repository->findOneByCode($currentCode);

            if(!$currentArea){ continue; }

            $siren->addArea($currentArea);

        }

        $em->persist($siren);
        $em->flush();

    }

    private function fetchAlarmJSON(){

        /*
        $curl = curl_init('http://www.oref.org.il/WarningMessages/alerts.json');
        // $curl = curl_init('http://www.klh-dev.com/adom/alert/alerts.json');

        // curl_setopt($curl, CURLOPT_HEADER, true); // get response header
        // curl_setopt($curl, CURLOPT_VERBOSE, true); // no idea what that means

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($curl, CURLOPT_ENCODING, '');
        $externalAlertResponse = curl_exec($curl);
        curl_close($curl);
        */

        $externalAlertResponse = file_get_contents('http://www.oref.org.il/WarningMessages/alerts.json');

        $this->alarmJSON = mb_convert_encoding($externalAlertResponse, 'utf-8', 'utf-16');

    }

}