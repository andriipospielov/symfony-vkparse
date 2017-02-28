<?php

namespace CommandsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
//registring namespaces for entities
use AppBundle\Entity\Person;
use AppBundle\Entity\Album;
use AppBundle\Entity\Photo;


class VkGetAlbumsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vk:getAlbums')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('argument');
        $output->writeln('starting Doctrine test...'.PHP_EOL);

        $person = new Person();
        $person->setVkId('id13666');

        $album = new Album();
        $album->setPerson($person);

        $photo = new Photo();
        $photo->setImageFullname('imagefullname');
        $photo->setAlbum($album);

        $doctrine = $this->getContainer()->get('doctrine');
        $output->writeln('initiating Doctrine...'.PHP_EOL);
        $em = $doctrine-> getManager();
        $output->writeln('initiating Manager...'.PHP_EOL);
        $em->persist($person);
        $output->writeln('persist person...'.PHP_EOL);
        $em->persist($album);
        $output->writeln('persist album...'.PHP_EOL);
        $em->persist($photo);
        $output->writeln('persist photo...'.PHP_EOL);
        $em->flush();
        $output->writeln('flush...'.PHP_EOL);
        $output->writeln('done...'.PHP_EOL);



        /* if ($input->getOption('option')) {
             // ...
             $output->writeln('option');
         }*/

        if ($input->getOption('option')) {

        }

        $output->writeln($argument);
    }

}
