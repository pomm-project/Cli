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
use PommProject\Foundation\Inspector\Inspector;
use PommProject\Foundation\Session\Session;
use PommProject\ModelManager\Tester\ModelSessionAtoum;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class InspectSchema extends ModelSessionAtoum
{
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
        $command = $application->find('pomm:inspect:schema');
        $tester = new CommandTester($command);
        $tester->execute(
            [
                'command'          => $command->getName(),
                'config-name'      => 'pomm_test',
                'schema'           => 'pomm_test',
            ],
            [
                'decorated' => false
            ]
        );
        $display = $tester->getDisplay();

        $this
            ->string($display)
            ->contains("| alpha  | table")
            ->contains("| beta   | table")
            ->contains("This is the beta comment.")
            ->contains("| dingo  | view")
        ;

        $inspector = new Inspector();
        $inspector->initialize($session);

        if (version_compare($inspector->getVersion(), '9.3', '>=') === true) {
            $this
                ->string($display)
                ->contains("| pluto  | materialized view |");
        }

        $this
            ->exception(function () use ($tester, $command) {
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
