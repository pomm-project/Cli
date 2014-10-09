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
use PommProject\Cli\Command\GenerateRelationStructure;
use PommProject\Cli\Command\GenerateRelationModel;
use PommProject\Cli\Command\GenerateEntity;

define('PROJECT_DIR', getenv('PWD'));
require PROJECT_DIR.'/vendor/autoload.php';

$application = new Application('pomm', 'NextGen 0.1');
$application->add(new InspectSchema());
$application->add(new InspectRelation());
$application->add(new GenerateRelationStructure());
$application->add(new GenerateRelationModel());
$application->add(new GenerateEntity());
$application->run();
