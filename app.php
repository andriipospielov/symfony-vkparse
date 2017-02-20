<?php
/**
 * Created by PhpStorm.
 * User: andrii
 * Date: 20.02.17
 * Time: 17:03
 */
require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();


$application->run();
echo '123';


// ... register commands
$application->add(new GenerateAdminCommand());
