<?php
/*
 * This file is part of Pomm's Cli package.
 *
 * (c) 2014 Grégoire HUBERT <hubert.greg@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PommProject\Cli\Generator;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableSeparator;

use PommProject\Foundation\Session;
use PommProject\Foundation\Inspector;
use PommProject\Foundation\Inflector;
use PommProject\Foundation\ConvertedResultIterator;

use PommProject\Cli\Generator\BaseGenerator;

/**
 * StructureGenerator
 *
 * Generate a RowStructure file from relation inspection.
 *
 * @package Cli
 * @copyright 2014 Grégoire HUBERT
 * @author Grégoire HUBERT
 * @license X11 {@link http://opensource.org/licenses/mit-license.php}
 */
class StructureGenerator extends BaseGenerator
{
    /*
     * __construct
     *
     * Constructor
     *
     * @access public
     * @param  Session $session
     * @param  string  $relation
     * @param  string  $filename
     * @param  string  $namespace
     * @return void
     */
    public function __construct(Session $session, $schema, $relation, $filename, $namespace)
    {
        parent::setSession($session);

        $this->schema    = $schema;
        $this->relation  = $relation;
        $this->filename  = $filename;
        $this->namespace = $namespace;
    }

    /**
     * generate
     *
     * Generate structure file.
     *
     * @access public
     * @param  InputInterface $input
     * @param  OutputInterface $output
     * @return null
     */
    public function generate(InputInterface $input, OutputInterface $output)
    {
        $table_oid          = $this->checkRelationInformation();
        $field_informations = $this->getFieldInformation($table_oid);
        $primary_key        = $this->getPrimaryKey($table_oid);

        if (file_exists($this->filename)) {
            $output->writeln(
                sprintf(
                    "<fg=orange>Overwriting</fg=orange> file '%s'.",
                    $this->filename
                )
            );
        } else {
            $output->writeln(
                sprintf(
                    "<fg=yellow>Creating</fg=yellow> file '%s'.",
                    $this->filename
                )
            );
        }

        if ($output->isVerbose()) {
            $table = $this->createTableHelper($output);
            $table->addRow(['relation :', $this->relation]);
            $table->addRow(['namespace :', $this->namespace]);
            $table->render();
        }

        if ($output->isVeryVerbose()) {
            $table = $this->createTableHelper($output);
            $table->setHeaders(['field', 'type']);

            foreach ($field_informations as $result) {
                $table->addRow([$result['name'], $result['type']]);
            }
            $table->render();
        }

        $output->writeln(
            $this->mergeTemplate(
                [
                    'namespace' => $this->namespace,
                    'entity'    => Inflector::studlyCaps($this->relation),
                    'relation'  => $this->relation,
                    'primary_key' => join(', ', array_map(function($val) { return sprintf("'%s'", $val); }, $primary_key)),
                    'add_fields'  => $this->formatAddFields($field_informations),
                ]
            )
        );
    }

    protected function formatAddFields(ConvertedResultIterator $field_informations)
    {
        $strings = [];
        foreach ($field_informations as $info) {
            $strings[] = sprintf("            ->addField('%s', '%s')", $info['name'], $info['type']);
        }

        return join("\n", $strings);
    }

    /**
     * checkRelationInformation
     *
     * Check if the given schema and relation exist. If so, the table oid is
     * returned, otherwise a GeneratorException is thrown.
     *
     * @access private
     * @throw  GeneratorException
     * @return int     $oid
     */
    private function checkRelationInformation()
    {
        if ($this->getInspector()->getSchemaOid($this->schema) === null) {
            throw new GeneratorException(sprintf("Schema '%s' not found.", $this->schema));
        }

        $table_oid = $this->getInspector()->getTableOid($this->schema, $this->relation);

        if ($table_oid === null) {
            throw new GeneratorException(
                sprintf(
                    "Relation '%s' could not be found in schema '%s'.",
                    $this->relation,
                    $this->schema
                )
            );
        }

        return $table_oid;
    }

    /**
     * getFieldInformation
     *
     * Fetch a table field information.
     *
     * @access protected
     * @param  int       $table_oid
     * @return array     $informations
     */
    protected function getFieldInformation($table_oid)
    {
        $fields_info = $this
            ->getInspector()
            ->getTableFieldInformation($table_oid)
            ;

        if ($fields_info === null) {
            throw new GeneratorException(
                sprintf(
                    "Error while fetching fields information for table oid '%s'.",
                    $table_oid
                )
            );
        }

        return $fields_info;
    }

    /**
     * getPrimaryKey
     *
     * Return the primary key of a relation if any.
     *
     * @access protected
     * @param  string $table_oid
     * @return array  $primary_key
     */
    protected function getPrimaryKey($table_oid)
    {
        $primary_key = $this
            ->getInspector()
            ->getPrimaryKey($table_oid)
            ;

        return $primary_key;
    }

    /**
     * getCodeTemplate
     *
     * @see BaseGenerator
     */
    protected function getCodeTemplate()
    {
        return <<<'_'
<?php
/**
 * This file has been automaticaly generated by Pomm Cli package.
 * DO NOT edit this file as your changes will be lost at next generation.
 */

namespace {:namespace:};

use PommProject\ModelManager\Model\RowStructure;

class {:entity:} extends RowStructure
{
    protected function initialize()
    {
        $this
            ->setRelation('{:relation:}')
            ->setPrimaryKey([{:primary_key:}])
{:add_fields:}
            ;
    }
}
_;
    }

    protected function outputFields(OutputInterface $output, ConvertedResultIterator $results)
    {

    }
}
