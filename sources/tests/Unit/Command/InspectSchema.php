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

use PommProject\Foundation\Test\Unit\SessionAwareAtoum;
use PommProject\Foundation\Session;
use PommProject\Foundation\Query\QueryPooler;
use PommProject\Foundation\Inspector\InspectorPooler;
use PommProject\Foundation\Converter\ConverterPooler;
use PommProject\Foundation\PreparedQuery\PreparedQueryPooler;

use PommProject\Cli\Test\Fixture\StructureFixtureClient;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;

class InspectSchema extends SessionAwareAtoum
{
    protected function initializeSession(Session $session)
    {
        $session
            ->registerClientPooler(new QueryPooler())
            ->registerClientPooler(new InspectorPooler())
            ->registerClientPooler(new PreparedQueryPooler())
            ->registerClientPooler(new ConverterPooler())
            ->registerClient(new StructureFixtureClient())
            ;
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->newTestedInstance()->setSession($this->getSession()));
        $command = $application->find('inspect:schema');
        $tester = new CommandTester($command);
        $tester->execute(
            [
                'command'          => $command->getName(),
                'config-name'      => 'pomm_test',
                'schema'           => 'pomm_test',
            ]
        );

        $this
            ->string($tester->getDisplay())
            ->contains("| alpha  | table")
            ->contains("| beta   | table")
            ->contains("This is the beta comment.")
            ->contains("| dingo  | view  |")
        ;
        $this
            ->exception(function() use ($tester, $command)
                {
                    $tester->execute(
                        [
                            'command'          => $command->getName(),
                            'config-name'      => 'pomm_test',
                            'schema'           => 'whatever',
                        ]
                    );
                }
            )
            ->isInstanceOf('\PommProject\Cli\Exception\CliException')
            ;
    }
}

