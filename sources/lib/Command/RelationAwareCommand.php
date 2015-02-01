<?php
/*
 * This file is part of Pomm's Cli package.
 *
 * (c) 2014 Grégoire HUBERT <hubert.greg@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PommProject\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * RelationAwareCommand
 *
 * Base class for generator commands.
 *
 * @abstract
 * @package Cli
 * @copyright 2014 Grégoire HUBERT
 * @author Grégoire HUBERT
 * @license X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see PommAwareCommand
 */
abstract class RelationAwareCommand extends SchemaAwareCommand
{
    protected $relation;

    /**
     * configure
     *
     * @see PommAwareCommand
     */
    protected function configureRequiredArguments()
    {
        parent::configureRequiredArguments()
            ->addArgument(
                'relation',
                InputArgument::REQUIRED,
                'Relation to inspect.'
            )
            ;

        return $this;
    }

    /**
     * execute
     *
     * see @Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->relation = $input->getArgument('relation');
    }

    /**
     * updateOutput
     *
     * Add ModelManager output lines to the CLI output.
     *
     * @access protected
     * @param  OutputInterface  $output
     * @param  array            $lines
     * @return RelationAwareCommand
     */
    protected function updateOutput(OutputInterface $output, array $lines = [])
    {
        foreach ($lines as $line) {
            $status = $line["status"] == "ok" ? "<fg=green>✓</fg=green>" : "<fg=red>✗</fg=red>";

            switch ($line['operation']) {
            case "creating":
                $operation = sprintf("<fg=green>%s</fg=green>", ucwords($line['operation']));
                break;
            case "overwritting":
                $operation = sprintf("<fg=cyan>%s</fg=cyan>", ucwords($line['operation']));
                break;
            case "deleting":
                $operation = sprintf("<fg=red>%s</fg=red>", ucwords($line['operation']));
                break;
            default:
                $operation = ucwords($line['operation']);
            }

            $output->writeln(
                sprintf(
                    " %s  %s file <fg=yellow>'%s'</fg=yellow>.",
                    $status,
                    $operation,
                    $this->filename
                )
            );
        }

        return $this;
    }
}
