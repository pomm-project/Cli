<?php
/*
 * This file is part of Pomm's Cli package.
 *
 * (c) 2014 Grégoire HUBERT <hubert.greg@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PommProject\Cli\Test\Unit\Command;

use PommProject\Cli\Test\Fixture\StructureFixtureClient;
use PommProject\Foundation\Session\Session;
use PommProject\ModelManager\Tester\ModelSessionAtoum;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class GenerateRelationStructure extends ModelSessionAtoum
{
    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove('tmp');
    }

    protected function initializeSession(Session $session)
    {
        $session
            ->registerClient(new StructureFixtureClient())
            ;
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->newTestedInstance()->setSession($this->buildSession()));
        $command = $application->find('pomm:generate:structure');
        $command_args =
            [
                'command'          => $command->getName(),
                'config-name'      => 'pomm_test',
                'schema'           => 'pomm_test',
                'relation'         => 'beta',
                '--prefix-ns'      => 'Model',
                '--prefix-dir'     => 'tmp',
            ];
        $tester = new CommandTester($command);
        $options = ['decorated' => false];
        $tester->execute($command_args, $options);

        $this
            ->string($tester->getDisplay())
            ->isEqualTo(" ✓  Creating file 'tmp/Model/PommTest/PommTestSchema/AutoStructure/Beta.php'.".PHP_EOL)
            ->string(file_get_contents('tmp/Model/PommTest/PommTestSchema/AutoStructure/Beta.php'))
            ->isEqualTo(file_get_contents('sources/tests/Fixture/BetaStructure.php'))
            ;
        $tester->execute($command_args, $options);
        $this
            ->string($tester->getDisplay())
            ->isEqualTo(" ✓  Overwriting file 'tmp/Model/PommTest/PommTestSchema/AutoStructure/Beta.php'.".PHP_EOL)
            ->string(file_get_contents('tmp/Model/PommTest/PommTestSchema/AutoStructure/Beta.php'))
            ->isEqualTo(file_get_contents('sources/tests/Fixture/BetaStructure.php'))
            ;
        $command_args['--prefix-dir'] = "tmp/Model";
        $tester->execute(array_merge($command_args, ['--psr4' => null ]), $options);
        $this
            ->string($tester->getDisplay())
            ->isEqualTo(" ✓  Overwriting file 'tmp/Model/PommTest/PommTestSchema/AutoStructure/Beta.php'.".PHP_EOL)
            ->string(file_get_contents('tmp/Model/PommTest/PommTestSchema/AutoStructure/Beta.php'))
            ->isEqualTo(file_get_contents('sources/tests/Fixture/BetaStructure.php'))
            ;
    }
}
