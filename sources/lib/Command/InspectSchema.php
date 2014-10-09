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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Helper\Table;

use PommProject\Foundation\Inflector;
use PommProject\Foundation\ResultIterator;

use PommProject\Cli\Command\SchemaAwareCommand;
use PommProject\Cli\Exception\CliException;

/**
 * InspectSchema
 *
 * Inspector from the command line.
 *
 * @package Cli
 * @copyright 2014 Grégoire HUBERT
 * @author Grégoire HUBERT
 * @license X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see SchemaAwareCommand
 */
class InspectSchema extends SchemaAwareCommand
{
    public function configure()
    {
        $this
            ->setName('inspect:schema')
            ->setDescription('Show relations in a given schema.')
            ;

        parent::configure();
    }
    /**
     * execute
     *
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $info = $this->getSession()->getInspector()->getSchemaRelations($this->schema_oid);
        $this->formatOutput($output, $info);
    }

    /**
     * formatOutput
     *
     * Format result
     *
     * @access protected
     * @param  OutputInterface $output
     * @param  ResultIterator  $info
     * @return void
     */
    protected function formatOutput(OutputInterface $output, ResultIterator $info)
    {
        $output->writeln(
            sprintf(
                "Found <info>%d</info> relations in schema <info>'%s'</info>.",
                $info->count(),
                $this->schema_name
            )
        );
        $table = (new Table($output))
            ->setHeaders(['name', 'type', 'oid ', 'comment'])
            ;

        foreach ($info as $table_info) {

            $table->addRow([
                sprintf("<fg=yellow>%s</fg=yellow>", $table_info['name']),
                $table_info['type'],
                $table_info['oid'],
                wordwrap($table_info['comment'])
            ]);
        }

        $table->render();
    }
}
