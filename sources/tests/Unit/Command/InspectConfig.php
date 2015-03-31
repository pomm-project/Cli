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

use PommProject\Foundation\Session\Session;
use PommProject\Foundation\Tester\FoundationSessionAtoum;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class InspectConfig extends FoundationSessionAtoum
{
    protected function initializeSession(Session $session)
    {

    }

    private function getPommMock($nb_builder = 0)
    {
        $pomm_mock = new \mock\PommProject\Foundation\Pomm;

        $pomm_mock->getMockController()->getSessionBuilders = function() use($nb_builder) {
            $builders = [];

            for($i = 0; $i < $nb_builder; $i++)
                $builders['my_db'.$i] = "fake_builder";

            return $builders;
        };

        $pomm_mock->getMockController()->isDefaultSession = function($name){
            if($name == "my_db0") return true;
        };

        return $pomm_mock;
    }

    private function getCommandTester( $nb_builder = 0)
    {
        $application = new Application();
        $application->add((new $this->newTestedInstance())->setPomm($this->getPommMock($nb_builder)));
        $command = $application->find('pomm:inspect:config');
        $tester = new CommandTester($command);
        $tester->execute(
            [
                'command' => $command->getName()
            ]
        );

        return $tester;
    }

    public function testExecute()
    {
        $tester = $this->getCommandTester(0);
        $this
            ->string($tester->getDisplay())
            ->contains("no session builders")
        ;

        $tester = $this->getCommandTester(1);
        $this
            ->string($tester->getDisplay())
            ->contains("my_db")
            ->contains("builder")
            ->contains("(default)")
            ;

        $tester = $this->getCommandTester(2);
        $this
            ->string($tester->getDisplay())
            ->contains("my_db1")
            ->contains("builders")
            ->contains("(default)")
        ;
    }
}


