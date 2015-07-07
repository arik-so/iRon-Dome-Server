<?php
/**
 * Created by IntelliJ IDEA.
 * User: arik
 * Date: 5/27/15
 * Time: 5:36 PM
 */

namespace AppBundle\Command;


use AppBundle\Interactor\AlarmDetector;
use AppBundle\Interactor\AreaScraper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AreaScrapeCommand extends ContainerAwareCommand{

    const CHECK_INTERVAL = 250000; // 250ms
    const CRON_INTERVAL = 60000000; // 60 seconds

    protected function configure(){

        $this->setName('irondome:areas:scrape');
        $this->setDescription('Scrape available areas');
        $this->addOption('sandbox', null, InputOption::VALUE_NONE, 'Run push notifications in a sandbox environment');
        $this->addOption('emulate', null, InputOption::VALUE_NONE, 'Emulate Pikud Haoref response');

    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $kernel = $this->getContainer()->get('kernel');
        $doctrine = $this->getContainer()->get('doctrine');

        $areaScraper = new AreaScraper($kernel, $doctrine);
        $areaScraper->scrapeAreas();

    }


}