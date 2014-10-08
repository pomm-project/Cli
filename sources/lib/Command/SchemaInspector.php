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

use PommProject\Foundation\Inflector;
use PommProject\Foundation\ResultIterator;

use PommProject\Cli\Command\SessionAwareCommand;
use PommProject\Cli\Exception\CliException;

/**
 * SchemaInspector
 *
 * Inspector from the command line.
 *
 * @package Cli
 * @copyright 2014 Grégoire HUBERT
 * @author Grégoire HUBERT
 * @license X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see SessionAwareCommand
 */
class SchemaInspector extends PommAwareCommand
{
    /**
     * configure
     *
     * @see Command
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('inspect:schema')
            ->setDescription('Print the list of tables in a schema.')
            ->addArgument(
                'schema',
                InputArgument::OPTIONAL,
                'Schema of the relation.',
                'public'
            )
            ;
    }

    /**
     * execute
     *
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $schema = $input->getArgument('schema');
        $schema_oid = $this-> getSession($input->getArgument('config-name'))-> getInspector()->getSchemaOid($schema);

        if ($schema_oid === null) {
            throw new CliException(sprintf("No such schema '%s'.", $schema));
        }

        $info = $this->getSession()->getInspector()->getSchemaRelations($schema_oid);
        $this->formatOutput($schema, $schema_oid, $output, $info);
    }

    /**
     * formatOutput
     *
     * Format result
     *
     * @access protected
     * @param  string          $schema
     * @param  int             $schema_oid
     * @param  OutputInterface $output
     * @param  ResultIterator  $info
     * @return void
     */
    protected function formatOutput($schema, $schema_oid, OutputInterface $output, ResultIterator $info)
    {
        $output->writeln(
            sprintf(
                "Found <info>%d</info> tables or views in schema <info>'%s'</info>.",
                $info->count(),
                $schema
            )
        );
        $output->writeln(sprintf("%-30s | %-5s | %6s", 'name', 'type', 'oid'));
        $output->writeln(str_repeat('-', 47));
        foreach ($info as $table_info) {

            if (strlen($table_info['name']) > 29) {
                $table_info['name'] =
                    sprintf("%s…", substr($table_info['name'], 0 ,28));
            }

            $output->writeln(
                sprintf(
                    "%-30s | %-5s | %6d",
                    $table_info['name'],
                    $table_info['type'],
                    $table_info['oid']
                )
            );
        }
    }
}
