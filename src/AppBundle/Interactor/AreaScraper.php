<?php
/**
 * Created by IntelliJ IDEA.
 * User: arik
 * Date: 5/27/15
 * Time: 9:03 AM
 */

namespace AppBundle\Interactor;


use AppBundle\Entity\Area;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\KernelInterface;

class AreaScraper {

    const GOOGLE_API_KEY = 'AIzaSyA3uohwnUcr1PvfTTGRHE2oYsnq51myzEM';

    const AREA_CODE_FILTER = -1; // 26;

    protected $kernel;
    protected $doctrine;
    protected $areaCodes;
    protected $areaDetails;

    public function __construct(KernelInterface $kernel, Registry $doctrine) {

        $this->kernel = $kernel;
        $this->doctrine = $doctrine;

    }

    public function scrapeAreas() {

        $this->getAreaCodes();
        $this->lookupAreaDetails();
        $this->storeAreaDetails();

    }

    private function getAreaCodes() {

        $rootDirectory = $this->kernel->getRootDir();
        $locator = new FileLocator($rootDirectory . '/Resources/alerts');

        $areaCodeCSVPath = $locator->locate('pikud_areas.csv');
        $areaCodeCSV = file_get_contents($areaCodeCSVPath);
        $csvRows = explode(PHP_EOL, $areaCodeCSV);

        $this->areaCodes = [];

        foreach ($csvRows as $currentRow) {

            $rowParts = explode(',', $currentRow);

            $cityName = $rowParts[0];
            $areaCode = $rowParts[1];

            $this->areaCodes[$areaCode] = $cityName;

        }

        echo '<pre>';

    }

    private function lookupAreaDetails() {

        $i = 0;

        $this->areaDetails = [];

        // now we take the data from Google
        foreach ($this->areaCodes as $areaCode => $cityName) {

            if(self::AREA_CODE_FILTER > 0 && $areaCode != self::AREA_CODE_FILTER){
                continue;
            }

            if (++$i > 15) { // for the time being, we are only testing this stuff
                // break;
            }

            $googleLookupURL = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($cityName) . '&country=il&language=en&key='.self::GOOGLE_API_KEY;
            $googleLookupResponseJSON = file_get_contents($googleLookupURL);
            $googleLookupResponse = json_decode($googleLookupResponseJSON, true);

            // echo $googleLookupURL;

            $finalPlaceID = self::determineActualPlaceID($areaCode, $cityName, $googleLookupResponse);
            $actualResult = self::getResultByPlaceID($googleLookupResponse, $finalPlaceID);

            if(empty($actualResult)){
                continue;
            }

            $this->areaDetails[$areaCode] = $actualResult;

        }

    }

    private function storeAreaDetails() {

        $i = 0;

        $em = $this->doctrine->getEntityManager();
        $repository = $em->getRepository('AppBundle:Area');

        foreach($this->areaDetails as $areaCode => $details){

            if(++$i == 1){
                print_r($details);
            }

            $currentArea = $repository->findOneByCode($areaCode);

            if(!$currentArea){
                $currentArea = new Area();
            }

            $currentArea->setCode($areaCode);
            $currentArea->setGooglePlaceIdentifier($details['place_id']);

            $currentArea->setCenterLatitude($details['geometry']['location']['lat']);
            $currentArea->setCenterLongitude($details['geometry']['location']['lng']);

            $currentArea->setNorthEdgeLatitude($details['geometry']['viewport']['northeast']['lat']);
            $currentArea->setEastEdgeLongitude($details['geometry']['viewport']['northeast']['lng']);
            $currentArea->setSouthEdgeLatitude($details['geometry']['viewport']['southwest']['lat']);
            $currentArea->setWestEdgeLongitude($details['geometry']['viewport']['southwest']['lng']);

            $currentArea->setToponymLong($details['address_components'][0]['long_name']);
            $currentArea->setToponymShort($details['address_components'][0]['short_name']);

            $em->persist($currentArea);

        }

        $em->flush();

    }

    private static function determineActualPlaceID($areaCode, $cityName, $googleLookupResponse) {

        $resultCount = count($googleLookupResponse['results']);

        if($resultCount == 0){

            echo 'NO RESULT! – ' . $areaCode . ' : ' . $cityName . PHP_EOL;

            print_r($googleLookupResponse);

            return null;

        }

        $finalPlaceID = $googleLookupResponse['results'][0]['place_id'];

        if ($resultCount > 1) { // there is ambiguity

            $relevanceDetails = self::getResultRelevanceDetails($cityName);

            reset($relevanceDetails);
            $finalPlaceID = key($relevanceDetails);

            if (count($relevanceDetails) > 1) { // there is still some ambiguity regarding the best place

                // first of all, let's see how many "locality" types there are. Those tend to be the most relevant

                $relevanceDetailsLocality = [];
                foreach ($relevanceDetails as $placeID => $currentDetails) {

                    $currentTypes = $currentDetails['types'];

                    if (in_array('locality', $currentTypes)) {
                        $relevanceDetailsLocality[$placeID] = $currentDetails;
                    }
                }

                $isAmbiguous = true;

                if (count($relevanceDetailsLocality) > 0) {

                    $isAmbiguous = false;

                    reset($relevanceDetails);
                    $finalPlaceID = key($relevanceDetailsLocality);

                    if (count($relevanceDetailsLocality) > 1) {

                        $isAmbiguous = true;

                    }

                }

                if ($isAmbiguous) {

                    echo 'AMBIGUITY! – ' . $areaCode . ' : ' . $cityName . PHP_EOL;

                    print_r($googleLookupResponse);

                    return null;



                }


            }

        }

        return $finalPlaceID;

    }

    private static function getResultRelevanceDetails($cityName) {

        $hebrewGoogleLookupURL = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($cityName) . '&country=il&language=he&key='.self::GOOGLE_API_KEY;
        $hebrewGoogleLookupResponseJSON = file_get_contents($hebrewGoogleLookupURL);
        $hebrewGoogleLookupResponse = json_decode($hebrewGoogleLookupResponseJSON, true);


        // let's look at the first responses and determine how much the names deviate from the search term

        $relevanceDetails = [];
        $lowestDeviation = -1;
        foreach ($hebrewGoogleLookupResponse['results'] as $currentResult) {

            $currentPlaceID = $currentResult['place_id'];
            $currentLongName = $currentResult['address_components'][0]['long_name'];
            $currentTypes = $currentResult['address_components'][0]['types'];

            $deviation = levenshtein($cityName, $currentLongName);


            // this next block makes sure that we only maintain the elements in the array with the globally lowest deviation
            // we also don't need a second pass over the array, which actually makes it pretty convenient

            if ($lowestDeviation == -1) {
                $lowestDeviation = $deviation;
            } else {

                if ($deviation < $lowestDeviation) {
                    $relevanceDetails = [];
                    $lowestDeviation = $deviation;
                } else if ($deviation > $lowestDeviation) {
                    continue;
                }

            }

            if ($deviation > $lowestDeviation) {
                continue;
            }

            $relevanceDetails[$currentPlaceID] = ['name' => $currentLongName, 'deviation' => $deviation, 'types' => $currentTypes];

        }

        return $relevanceDetails;

    }

    private static function getResultByPlaceID($googleLookupResponse, $placeID) {

        foreach ($googleLookupResponse['results'] as $currentResult) {

            if ($currentResult['place_id'] === $placeID) {
                return $currentResult;
            }

        }

    }

}