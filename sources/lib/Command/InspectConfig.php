<?php
/*
 * This file is part of Pomm's Cli package.
 *
 * (c) 2014 - 2015 Grégoire HUBERT <hubert.greg@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PommProject\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * InspectConfig
 *
 * Display information about session builders.
 *
 * @package   Cli
 * @copyright 2014 - 2015 Grégoire HUBERT
 * @author    Grégoire HUBERT
 * @license   X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see       SchemaAwareCommand
 */
class InspectConfig extends PommAwareCommand
{
    /**
     * configure
     *
     * @see command
     */
    protected function configure()
    {
        $this
            ->setName("pomm:inspect:config")
            ->setDescription("Show session builders name.")
            ;

        parent::configure();
    }

    /**
     * execute
     *
     * Set pomm dependent variables.
     *
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $results = array_keys($this->getPomm()->getSessionBuilders());
        switch (count($results)) {
        case 0:
            $output->writeln("There are no session builders in current Pomm instance.");
            break;
        case 1:
            $output->writeln("There is <info>1</info> builder in current Pomm instance:");
            $this->showResultList($output, $results);
            break;
        default:
            $output->writeln(sprintf("There are <info>%d</info> builders in current Pomm instance:", count($results)));
            $this->showResultList($output, $results);
        }

        return 0;
    }

    /**
     * showResultList
     *
     * Add list of builders to output.
     *
     * @access private
     * @param  OutputInterface  $output
     * @param  array            $results
     * @return InspectConfig    $this
     */
    private function showResultList(OutputInterface $output, array $results)
    {
        foreach ($results as $name) {
            $output->writeln(
                sprintf(
                    " → '%s'%s",
                    $name,
                    $this->getPomm()->isDefaultSession($name)
                        ? '(default)'
                        : ''
                )
            );
        }

        return $this;
    }
}
