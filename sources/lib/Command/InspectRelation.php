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
use PommProject\Foundation\ConvertedResultIterator;

use PommProject\Cli\Command\SchemaAwareCommand;
use PommProject\Cli\Exception\CliException;

/**
 * InspectRelation
 *
 * Display informations about a given relation.
 *
 * @package Cli
 * @copyright 2014 Grégoire HUBERT
 * @author Grégoire HUBERT
 * @license X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see SchemaAwareCommand
 */
class InspectRelation extends SchemaAwareCommand
{
    protected $relation_name;
    protected $relation_oid;

    protected function configure()
    /**
     * configure
     *
     * @see Command
     */
    {
        $this
            ->setName('inspect:relation')
            ->setDescription('Display a relation information.')
            ->addArgument(
                'relation',
                InputArgument::REQUIRED,
                'Name of the relation'
            )
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
        $this->relation_name = $input->getArgument('relation');
        $this->relation_oid = $this->getSession()
            ->getInspector()
            ->getTableOid($this->schema_name, $this->relation_name)
            ;

        if ($this->relation_oid === null) {
            throw new CliException(
                sprintf(
                    "Relation <comment>%s.%s</comment> does not exist.",
                    $this->schema_name,
                    $this->relation_name
                )
            );
        }
        $fields_infos = $this->getSession()
            ->getInspector()
            ->getTableFieldInformation($this->relation_oid)
            ;

        $this->formatOutput($output, $fields_infos);
    }

    protected function formatOutput(OutputInterface $output, ConvertedResultIterator $fields_infos)
    {
        $output->writeln(sprintf("Relation <fg=cyan>%s.%s</fg=cyan>", $this->schema_name, $this->relation_name));
        $table = (new Table($output))
            ->setHeaders(['pk', 'name', 'type', 'default', 'notnull', 'comment'])
            ;

        foreach ($fields_infos as $info) {
            $table->addRow(
                [
                    $info['is_primary'] ? '<fg=cyan>*</fg=cyan>' : '',
                    sprintf("<fg=yellow>%s</fg=yellow>", $info['name']),
                    $info['type'],
                    $info['default'],
                    $info['is_notnull'] ? 'yes' : 'no',
                    wordwrap($info['comment']),
                ]
            );
        }

        $table->render();
    }
}
