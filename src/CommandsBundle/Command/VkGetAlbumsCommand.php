<?php

namespace CommandsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
//registring namespaces for entities
use AppBundle\Entity\Person;
use AppBundle\Entity\Album;
use AppBundle\Entity\Photo;
//registering VK API
use \BW\Vkontakte as Vk;

class VkGetAlbumsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vk:getAlbums')
            ->setDescription('--id=foobar  Looks up for user whose vk id equals foobar'
                . PHP_EOL . '--csv=barbaz Specify csv file location (in this case barbaz) which contains users ids and looks up for them')
            ->setDefinition(
                new InputDefinition(array(
                    new InputOption('id', 'i', InputOption::VALUE_REQUIRED),
                    new InputOption('csv', 'c', InputOption::VALUE_REQUIRED),

                ))
            );;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        TODO:refactor all of beyond

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine-> getManager();



        $userVkId = $input->getOption('id');

        $vk = new Vk([
            'client_id' => '5900438',
            'client_secret' => 'OwEKI1Wt9Xmhi3LgDmSt',

        ]);
        $userVkArray = $vk->api('users.get', [
            'user_id' => $userVkId,
        ])[0];
        $person = new Person();
        $person->setVkId($userVkId);
        $person->setFullname($userVkArray['first_name'] . ' ' . $userVkArray['last_name']);
        $em->persist($person);


        $albums = $vk->api('photos.getAlbums', [
            'owner_id' => $userVkId,
        ])['items'];
        foreach ($albums as $item) {
            $album = new Album();
            $album->setVkId($item['id']);
            $album->setTitle($item['title']);
            $album->setPerson($person);
            $em->persist($album);

            $photosArray = $vk->api('photos.get', [
                'owner_id' => $person->getVkId(),
                'album_id' => $album->getVkId(),

            ])['items'];
            foreach ($photosArray as $item) {
                $photo = new Photo();
                $photo->setVkId($item['id']);
                $photo->setAlbum($album);

//               TODO: replace this file-downloading mess with S3 with good code
                $savingPath = '/home/andrii/vkpics/' . $person->getVkId() .
                    '/' . $album->getVkId();




//                $savingPath = '/home/andrii/vkpics/' . $person->getVkId() .
//                    '/' . $album->getVkId() . '/';



                if (!file_exists($savingPath) ){
                    mkdir($savingPath, 0777, true);
                }

                $photo->setImageFullname($savingPath. '/'.$item['id']);
                $em->persist($photo);


                file_put_contents($photo->getImageFullname(), fopen($item['photo_604'], 'r'));

            }


        }


        $em->flush();


        $output->writeln('Job done!' . PHP_EOL);


        /* $argument = $input->getArgument('argument');
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
         $output->writeln('done...'.PHP_EOL);*/


    }

}
