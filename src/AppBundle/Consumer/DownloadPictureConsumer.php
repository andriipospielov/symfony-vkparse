<?php
/**
 * Created by PhpStorm.
 * User: andrii
 * Date: 02.03.17
 * Time: 20:41
 */

namespace AppBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


class DownloadPictureConsumer implements ConsumerInterface
{
    public function execute(AMQPMessage $msg)
    {
        $fs = new Filesystem();
        $pictureMsgArray = unserialize($msg->body);
        print_r(unserialize($msg->body));
        try {
            if (!$fs->exists($pictureMsgArray['directory'])) {
                $fs->mkdir($pictureMsgArray['directory']);
            }
        } catch (IOExceptionInterface $e) {
            echo "IOExceptionInterface" . $e->getMessage();
        }
//If image downloading failed, false is returned from callback so the message will be rejected by the consumer and requeued by RabbitMQ.
        $downloadIsSuccesfull = file_put_contents($pictureMsgArray['directory'] . '/' . $pictureMsgArray['filename'], fopen($pictureMsgArray['url'], 'r'));
        if (!$downloadIsSuccesfull) {
            return false;
        }


        return true;
    }

}