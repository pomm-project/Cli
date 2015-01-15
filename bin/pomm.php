#!/usr/bin/env php
<?php
/*
 * This file is part of the Pomm's Cli package.
 *
 * (c) 2014 Grégoire HUBERT <hubert.greg@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Console\Application;

use PommProject\Cli\Command\InspectSchema;
use PommProject\Cli\Command\InspectRelation;
use PommProject\Cli\Command\InspectDatabase;
use PommProject\Cli\Command\InspectConfig;
use PommProject\Cli\Command\GenerateRelationStructure;
use PommProject\Cli\Command\GenerateRelationModel;
use PommProject\Cli\Command\GenerateEntity;
use PommProject\Cli\Command\GenerateForRelation;
use PommProject\Cli\Command\GenerateForSchema;

function includeIfExists($file)
{
    if (file_exists($file)) {
        return include $file;
    }
}

if (
    (!$loader = includeIfExists(__DIR__ . '/../vendor/autoload.php'))
    && (!$loader = includeIfExists(__DIR__ . '/../../../autoload.php'))
) {
    die(
        'You must set up the project dependencies, run the following commands:' . PHP_EOL
        . 'curl -s http://getcomposer.org/installer | php' . PHP_EOL
        . 'php composer.phar install' . PHP_EOL
    );
}

$application = new Application('pomm', 'NextGen early-dev');
$application->add(new InspectConfig);
$application->add(new InspectDatabase);
$application->add(new InspectSchema);
$application->add(new InspectRelation);
$application->add(new GenerateRelationStructure);
$application->add(new GenerateRelationModel);
$application->add(new GenerateEntity);
$application->add(new GenerateForRelation);
$application->add(new GenerateForSchema);
$application->run();
