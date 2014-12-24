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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

use PommProject\Foundation\ConvertedResultIterator;

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
class InspectRelation extends RelationAwareCommand
{
    protected $relation_oid;

    /**
     * configure
     *
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('pomm:inspect:relation')
            ->setDescription('Display a relation information.')
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
        $this->relation = $input->getArgument('relation');
        $this->relation_oid = $this->getSession()
            ->getInspector()
            ->getTableOid($this->schema, $this->relation)
            ;

        if ($this->relation_oid === null) {
            throw new CliException(
                sprintf(
                    "Relation <comment>%s.%s</comment> not found.",
                    $this->schema,
                    $this->relation
                )
            );
        }

        $fields_infos = $this->getSession()
            ->getInspector()
            ->getTableFieldInformation($this->relation_oid)
            ;

        $this->formatOutput($output, $fields_infos);
    }

    /**
     * formatOutput
     *
     * Render output.
     *
     * @access protected
     * @param  OutputInterface         $output
     * @param  ConvertedResultIterator $fields_infos
     * @return void
     */
    protected function formatOutput(OutputInterface $output, ConvertedResultIterator $fields_infos)
    {
        $output->writeln(sprintf("Relation <fg=cyan>%s.%s</fg=cyan>", $this->schema, $this->relation));
        $table = (new Table($output))
            ->setHeaders(['pk', 'name', 'type', 'default', 'notnull', 'comment'])
            ;

        foreach ($fields_infos as $info) {
            $table->addRow(
                [
                    $info['is_primary'] ? '<fg=cyan>*</fg=cyan>' : '',
                    sprintf("<fg=yellow>%s</fg=yellow>", $info['name']),
                    $this->formatType($info['type']),
                    $info['default'],
                    $info['is_notnull'] ? 'yes' : 'no',
                    wordwrap($info['comment']),
                ]
            );
        }

        $table->render();
    }

    /**
     * formatType
     *
     * Format type.
     *
     * @access protected
     * @param  string $type
     * @return string
     */
    protected function formatType($type)
    {
        if (preg_match('/^(?:(.*)\.)?_(.*)$/', $type, $matchs)) {
            if ($matchs[1] !== '') {
                return sprintf("%s.%s[]", $matchs[1], $matchs[2]);
            } else {
                return $matchs[2].'[]';
            }
        } else {
            return $type;
        }
    }
}
