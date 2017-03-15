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

use PommProject\Cli\Exception\CliException;
use PommProject\Foundation\ConvertedResultIterator;
use PommProject\Foundation\Exception\SqlException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * InspectRelation
 *
 * Display information about a given relation.
 *
 * @package   Cli
 * @copyright 2014 - 2015 Grégoire HUBERT
 * @author    Grégoire HUBERT
 * @license   X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see       SchemaAwareCommand
 */
class InspectRelation extends RelationAwareCommand
{
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
     * @throws CliException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->relation = $input->getArgument('relation');
        $inspector = $this->getSession()
            ->getInspector('relation')
            ;
        try {
            $relation_size = $inspector
                ->getTableTotalSizeOnDisk($this->schema, $this->relation);
        } catch (SqlException $e) {
            throw new CliException(
                sprintf(
                    "Relation '%s.%s' not found.\nRelations in this schema are {%s}.",
                    $this->schema,
                    $this->relation,
                    join(', ', $inspector->getRelationsInSchema($this->schema)->slice('name'))
                )
            );
        }

        $fields_infos = $inspector->getTableFieldInformationName($this->schema, $this->relation);

        $this->formatOutput($output, $fields_infos, $relation_size);
    }

    /**
     * formatOutput
     *
     * Render output.
     *
     * @access protected
     * @param  OutputInterface         $output
     * @param  ConvertedResultIterator $fields_infos
     * @param  int                     $size
     * @return void
     */
    protected function formatOutput(OutputInterface $output, ConvertedResultIterator $fields_infos, $size)
    {
        $output->writeln(
            sprintf(
                "Relation <fg=cyan>%s.%s</fg=cyan> (size with indexes: %d bytes)",
                $this->schema,
                $this->relation,
                $size
            )
        );
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
        if (preg_match('/^(?:(.*)\.)?_(.*)$/', $type, $matches)) {
            if ($matches[1] !== '') {
                return sprintf("%s.%s[]", $matches[1], $matches[2]);
            } else {
                return $matches[2].'[]';
            }
        } else {
            return $type;
        }
    }
}
