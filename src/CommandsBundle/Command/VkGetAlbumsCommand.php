<?php

namespace CommandsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
// namespaces for entities
use AppBundle\Entity\Person;
use AppBundle\Entity\Album;
use AppBundle\Entity\Photo;

class VkGetAlbumsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vk:get')
            ->setDescription('--id=foobar for specific id, --csv=barbaz to pass csv file\'s fullname which contains users\' ids ')
            ->setDefinition(
                new InputDefinition(array(
                    new InputOption('id', 'i', InputOption::VALUE_REQUIRED),
                    new InputOption('csv', 'c', InputOption::VALUE_REQUIRED),

                ))
            );;
    }

    private function iterateHelper($userVkId)
    {
        $logger = $this->getContainer()->get('logger');

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $vk = $this->getContainer()->get('vk_service');

        $personResponseArray = $vk->api(
            'users.get', [
            'user_id' => $userVkId,
        ]);
        if (array_key_exists('error', $personResponseArray)
            OR array_key_exists('deactivated', $personResponseArray[0])
            OR $personResponseArray[0]['first_name'] == 'DELETED'
        ) {
            echo("ID $userVkId is not a valid user");
            $logger->info("ID $userVkId is not a valid user");
            return false;
        }
        $personResponseArray = end($personResponseArray);

        $repo = $doctrine->getRepository('AppBundle:Person');
        $person = $repo->findOneByvkId($personResponseArray['id']);
        if (!$person) {
            $person = new Person();
            $person->setVkId($personResponseArray['id']);
            $person->setFullname($personResponseArray['first_name'] . ' ' . $personResponseArray['last_name']);
            $em->persist($person);
        }

        $albumsResponseArray = $vk->api('photos.getAlbums', [
            'owner_id' => $person->getVkId(),
        ])['items'] OR $logger->info("User with id $userVkId does not have accessible albums");

        foreach ($albumsResponseArray as $item) {
            $repo = $doctrine->getRepository('AppBundle:Album');
            $album = $repo->findOneByvkId($item['id']);
            if (!$album) {
                $album = new Album();
                $album->setVkId($item['id']);
                $album->setTitle($item['title']);
                $album->setPerson($person);
                $em->persist($album);
            }

            $photosResponseArray = $vk->api('photos.get', [
                'owner_id' => $person->getVkId(),
                'album_id' => $album->getVkId(),
                'photo_sizes' => 1,
            ])['items'];

            foreach ($photosResponseArray as $value) {

                $repo = $doctrine->getRepository('AppBundle:Photo');
                $photo = $repo->findOneByvkId($value['id']);
                if (!$photo) {
                    $photo = new Photo();
                    $photo->setVkId($value['id']);
                    $photo->setAlbum($album);
                    $em->persist($photo);
                } else {
                    continue;
                }
                $pathPrefix = '/home/andrii/vkpics/';

//               TODO: replace this file-downloading mess with S3 with good code
                $savingPath = $pathPrefix . $person->getVkId() .
                    '/' . $album->getVkId();

                $msg = array('directory' => $savingPath, 'filename' => $value['id'], 'url' => end($value['sizes'])['src']);
                $this->getContainer()->get('old_sound_rabbit_mq.download_picture_producer')->publish(serialize($msg));

                $photo->setImageFullname($savingPath . '/' . $value['id']);
                $em->flush();

            }
        }
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('id')) {
            $userVkId = $input->getOption('id');
            $this->iterateHelper($userVkId);
        }
        if ($input->getOption('csv')) {
            $csvFile = $input->getOption('csv');
            $f = fopen($csvFile, 'r');
            $idsArray = fgetcsv($f);
            array_walk($idsArray, array($this, 'iterateHelper'));
        }

        $output->writeln('Done!' . PHP_EOL);

    }
}
