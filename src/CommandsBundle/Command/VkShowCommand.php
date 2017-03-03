<?php

namespace CommandsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
// namespaces for entities
use AppBundle\Entity\Person;
use AppBundle\Entity\Album;
use AppBundle\Entity\Photo;

class VkShowCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vk:show')
            ->setDescription('lists all parsed accounts, --id to show for specific acc')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'Shows albums for given userid');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if ($input->getOption('id')) {
            $doctrine = $this->getContainer()->get('doctrine');
            $repo = $doctrine->getRepository('AppBundle:Person');
            if (!$person = $repo->findOneByVkId($input->getOption('id'))) {
                $output->writeln('User not found');
                die();
            }
            $output->writeln($person->getFullName() . ': ' . 'id' . $person->getVkId().':');
            foreach ($person->getAlbums() as $album) {

                echo '  '.$album->getTitle() . ' (id' . $album->getVkId() . '):' . PHP_EOL;
                foreach ($album->getPhotos() as $photo) {
                    echo '    ' . $photo->getImageFullname() . PHP_EOL;
                }
            }

        } else {
            $doctrine = $this->getContainer()->get('doctrine');
            $em = $doctrine->getManager();
            $people = $doctrine->getRepository('AppBundle:Person')->findAll();
            foreach ($people as $person) {
                echo $person->getFullName() . ': ' . 'id' . $person->getVkId() . PHP_EOL;
            }//
        }
    }
}
