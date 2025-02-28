<?php

namespace Lucca\AdherentBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\AdherentBundle\Generator\CodeGenerator;
use Lucca\Bundle\AdherentBundle\Mailer\SummaryAdherentSubscriptionMailer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Helper\ProgressBar;

use Doctrine\Common\Persistence\ObjectManager;
use Lucca\AdherentBundle\Entity\Adherent;
use Lucca\UserBundle\Entity\User;
use Lucca\AdherentBundle\Generator\CodeGenerator;
use Lucca\AdherentBundle\Mailer\SummaryAdherentSubscriptionMailer;
use FOS\UserBundle\Model\UserManager;

/**
 * Class LuccaImportAdherentCommand
 *
 * @package Lucca\AdherentBundle\Command
 * @author Terence <terence@numeric-wave.tech>
 */
class LuccaImportAdherentCommand extends ContainerAwareCommand
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CodeGenerator $codeGenerator,
        private readonly SummaryAdherentSubscriptionMailer $mailer,
        private readonly UserManager $userManager,
        private $file,
    )
    {
    }

    /**
     * Configure command parameters
     * Option : path of csv file
     */
    protected function configure()
    {
        $this
            ->setName('lucca:import:adherent')
            ->setDescription('Import adherent csv files.')
            ->setHelp('Import a csv file for add adherent in Lucca application.')
            ->addOption(
                'path', null, InputOption::VALUE_REQUIRED,
                'Give the path of adherent csv file.'
            );
    }

    /**
     * Initialize var for process
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();

        // Catch path option
        $optionPath = $input->getOption('path');

        // Test if file provide by option
        if ($optionPath) {
            if ($fs->exists($optionPath))
                $this->file = $optionPath;
        }

        // If file don't exist, or option is not given
        if ($this->file === null)
            $this->file = $this->getContainer()->getParameter('path.import') . $this->getContainer()->getParameter('file.adherent');

        // If file doesn't exist
        if (!$fs->exists($this->file)) {
            throw new \Exception('File can not be empty');
        }
    }

    /**
     * Execute command
     * Write log and start the import
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = microtime(true);

        // Start command
        $now = new \DateTime();
        $output->writeln([
            '',
            'Import Intercommunal for Lucca\'s file',
            'Start: ' . $now->format('d-m-Y H:i:s') . '',
            '-----------------------------------------------',
        ]);

        // Importing CSV on DB via Doctrine ORM
        $this->import($input, $output, $this->file);

        // Showing when the script is over
        $now = new \DateTime();
        $output->writeln([
            '',
            '-----------------------------------------------',
            'End: ' . $now->format('d-m-Y H:i:s') . '',
        ]);

        $finishTime = microtime(true);
        $elapsedTime = $finishTime - $startTime;
        $output->writeln([
            '',
            sprintf('<comment>[INFO] Import stored in database: Elapsed time %.2f ms</comment>', $elapsedTime * 1000),
            '-----------------------------------------------',
        ]);

        return true;
    }

    /**
     * Import from file and create objects
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $file
     */
    protected function import(InputInterface $input, OutputInterface $output, $file)
    {
        $em = $this->em;
        $data = $this->get($input, $output, $file);

        // Turning off doctrine default logs queries for saving memory
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        /**
         * Step 1 : init var and progress
         * Define the size of record, the frequency for persisting the data and the current index of records
         */
        $size = count($data);
        $progress = new ProgressBar($output, $size);
        $progress->start();

        /**
         * Step 2 : Processing on each row of data
         * Parse data and create object for each one
         */
        foreach ($data as $row) {

            /**
             * Check if intercommunal exist
             */
            $adherent = $em->getRepository('LuccaAdherentBundle:Adherent')->findOneByEmail(utf8_encode($row['email']));

            /**
             * If intercommunal doesn't exist in application
             * create a new intercommunal
             */
            if (!is_object($adherent)) {
                $adherent = new Adherent();
                $user = new User();

                $adherent->setUser($user);
                $user->setEmail(utf8_encode($row['email']));
                $user->setEnabled(true);

                /** Set Town/Interco/Service of an adherent */
                if (utf8_encode($row['town'])) {
                    $town = $em->getRepository('LuccaParameterBundle:Town')->findOneBy(array(
                        'code' => utf8_encode($row['town'])
                    ));

                    if ($town)
                        $adherent->setTown($town);

                } elseif (utf8_encode($row['interco'])) {
                    $interco = $em->getRepository('LuccaParameterBundle:Intercommunal')->findOneBy(array(
                        'code' => utf8_encode($row['interco'])
                    ));

                    if ($interco)
                        $adherent->setIntercommunal($interco);

                } elseif (utf8_encode($row['service'])) {
                    $service = $em->getRepository('LuccaParameterBundle:Service')->findOneBy(array(
                        'code' => utf8_encode($row['service'])
                    ));

                    if ($service)
                        $adherent->setService($service);
                }

                $user->setUsername($this->AGenerator->generate($adherent));

                $group = $em->getRepository('LuccaUserBundle:Group')->findOneBy(array(
                    'name' => utf8_encode($row['group'])
                ));

                if ($group)
                    $adherent->getUser()->addGroup($group);
            }

            $adherent->setName(utf8_encode($row['name']));
            $adherent->setFirstname(utf8_encode($row['firstname']));
            $adherent->getUser()->setName(utf8_encode($row['name']));
            $adherent->setFunction(utf8_encode($row['function']));

            $adherent->setAddress(utf8_encode($row['address']));
            $adherent->setZipcode(utf8_encode($row['zipcode']));
            $adherent->setCity(utf8_encode($row['city']));
            $adherent->setPhone(utf8_encode($row['phone']));
            $adherent->setMobile(utf8_encode($row['mobile']));

            /** Password */
            $adherent->getUser()->setPlainPassword(uniqid());
            $this->AMailer->sendSubscriptionToAdherent($adherent, $adherent->getUser()->getPlainPassword());

            $this->userManager->updateUser($adherent->getUser());

            $em->persist($adherent);
            $em->flush();
            $progress->advance(1);
        }

        /**
         * Step 3 : flush last items, detach all for doctrine and finish progress
         */
        $em->flush();
        $em->clear();
        $progress->finish();
    }

    /**
     * Return data array provided by csv file
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $fileName
     * @return mixed
     */
    protected function get(InputInterface $input, OutputInterface $output, $fileName)
    {
        $converter = $this->getContainer()->get('lucca.converter.csv_array');
        $data = $converter->convert($fileName, ';');

        return $data;
    }
}
