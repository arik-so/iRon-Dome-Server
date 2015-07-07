<?php
/**
 * Created by IntelliJ IDEA.
 * User: arik
 * Date: 5/27/15
 * Time: 5:36 PM
 */

namespace AppBundle\Command;


use AppBundle\Interactor\AlarmDetector;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AlarmObservationCommand extends ContainerAwareCommand{

    const CHECK_INTERVAL = 250000; // 250ms
    const CRON_INTERVAL = 60000000; // 60 seconds

    protected function configure(){

        $this->setName('irondome:alarms:observe');
        $this->setDescription('Observe current Pikud Haoref status');
        $this->addOption('sandbox', null, InputOption::VALUE_NONE, 'Run push notifications in a sandbox environment');
        $this->addOption('emulate', null, InputOption::VALUE_NONE, 'Emulate Pikud Haoref response');

    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $i = 0;

        $kernel = $this->getContainer()->get('kernel');
        $doctrine = $this->getContainer()->get('doctrine');

        $isSandbox = $input->getOption('sandbox');
        $isEmulation = $input->getOption('emulate');

        $output->writeln('Sandbox: '.$isSandbox);
        $output->writeln('Emulation: '.$isEmulation);

        // operating under the assumption that this thing is executed by a cronjob, this thing needs to be run for just a minute
        $intervalSpent = 0;

        while($intervalSpent <= self::CRON_INTERVAL){

            $detector = new AlarmDetector($doctrine);
            $detector->detectAlarms($isEmulation);

            $output->writeln(PHP_EOL.++$i.PHP_EOL);

            usleep(self::CHECK_INTERVAL); // 250ms
            $intervalSpent += self::CHECK_INTERVAL;

        }

    }


}