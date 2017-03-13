<?php
/*
 * This file is part of Pomm's Cli package.
 *
 * (c) 2014 - 2015 Grégoire HUBERT <hubert.greg@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PommProject\Cli\Test\Unit\Command;

use PommProject\Cli\Test\Fixture\StructureFixtureClient;
use PommProject\Foundation\Inspector\LegacyInspector;
use PommProject\Foundation\Session\Session;
use PommProject\ModelManager\Tester\ModelSessionAtoum;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class GenerateRelationModel extends ModelSessionAtoum
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
        $session = $this->buildSession();
        $application = new Application();
        $application->add($this->newTestedInstance()->setSession($session));
        $command = $application->find('pomm:generate:model');
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
            ->isEqualTo(" ✓  Creating file 'tmp/Model/PommTest/PommTestSchema/BetaModel.php'.".PHP_EOL)
            ->string(file_get_contents('tmp/Model/PommTest/PommTestSchema/BetaModel.php'))
            ->isEqualTo(file_get_contents('sources/tests/Fixture/BetaModel.php'))
            ->exception(function () use ($tester, $command, $command_args) {
                $tester->execute($command_args);
            })
            ->isInstanceOf('\PommProject\ModelManager\Exception\GeneratorException')
            ->message->contains('--force')
            ;
        $tester->execute(array_merge($command_args, ['--force' => null ]), $options);
        $this
            ->string($tester->getDisplay())
            ->isEqualTo(" ✓  Overwriting file 'tmp/Model/PommTest/PommTestSchema/BetaModel.php'.".PHP_EOL)
            ->string(file_get_contents('tmp/Model/PommTest/PommTestSchema/BetaModel.php'))
            ->isEqualTo(file_get_contents('sources/tests/Fixture/BetaModel.php'))
            ;
        $tester->execute(array_merge($command_args, ['relation' => 'dingo']), $options);
        $this
            ->string($tester->getDisplay())
            ->isEqualTo(" ✓  Creating file 'tmp/Model/PommTest/PommTestSchema/DingoModel.php'.".PHP_EOL)
            ->string(file_get_contents('tmp/Model/PommTest/PommTestSchema/DingoModel.php'))
            ->isEqualTo(file_get_contents('sources/tests/Fixture/DingoModel.php'))
            ;

        $inspector = new LegacyInspector;
        $inspector->initialize($session);

        if (version_compare($inspector->getVersion(), '9.3', '>=') === true) {
            $tester->execute(array_merge($command_args, ['relation' => 'pluto']), $options);
            $this
                ->string($tester->getDisplay())
                ->isEqualTo(" ✓  Creating file 'tmp/Model/PommTest/PommTestSchema/PlutoModel.php'.".PHP_EOL)
                ->string(file_get_contents('tmp/Model/PommTest/PommTestSchema/PlutoModel.php'))
                ->isEqualTo(file_get_contents('sources/tests/Fixture/PlutoModel.php'))
                ;
        }
        $command_args['--prefix-dir'] = "tmp/Model";
        $tester->execute(array_merge($command_args, ['--psr4' => null, '--force' => null ]), $options);
        $this
            ->string($tester->getDisplay())
            ->isEqualTo(" ✓  Overwriting file 'tmp/Model/PommTest/PommTestSchema/BetaModel.php'.".PHP_EOL)
            ->string(file_get_contents('tmp/Model/PommTest/PommTestSchema/BetaModel.php'))
            ->isEqualTo(file_get_contents('sources/tests/Fixture/BetaModel.php'))
        ;
    }
}
