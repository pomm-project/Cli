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

use PommProject\Foundation\Where;
use PommProject\Foundation\Inflector;

use PommProject\Cli\Generator\BaseGenerator;
use PommProject\Cli\Exception\GeneratorException;

/**
 * ModelGenerator
 *
 * Generate a new model file.
 * If the given file already exist, it needs the force option to be set at
 * 'yes'.
 *
 * @package Cli
 * @copyright 2014 Grégoire HUBERT
 * @author Grégoire HUBERT
 * @license X11 {@link http://opensource.org/licenses/mit-license.php}
 */
class ModelGenerator extends BaseGenerator
{
    /**
     * generate
     *
     * Generate structure file.
     *
     * @access public
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return null
     */
    public function generate(InputInterface $input, OutputInterface $output)
    {
        $schema_oid = $this
            ->getSession()
            ->getInspector()
            ->getSchemaOid($this->schema);

        if ($schema_oid === null) {
            throw new GeneratorException(sprintf("Schema '%s' does not exist.", $this->schema));
        }

        $relations_info = $this
            ->getSession()
            ->getInspector()
            ->getSchemaRelations($schema_oid, new Where('cl.relname = $*', [$this->relation]))
            ;

        if ($relations_info->isEmpty()) {
            throw new GeneratorException(sprintf("Relation '%s.%s' does not exist.", $this->schema, $this->relation));
        }

        if (file_exists($this->filename) && !$input->getOption('force')) {
            throw new GeneratorException(sprintf("Cannot overwrite file '%s' without --force option.", $this->filename));
        }

        $this->outputFileCreation($output);

        $this->saveFile(
            $this->filename,
            $this->mergeTemplate(
                [
                    'entity'        => Inflector::studlyCaps($this->relation),
                    'namespace'     => trim($this->namespace, '\\'),
                    'trait'         => $relations_info->current()['type'] === 'table' ? 'WriteTrait' : 'ReadTrait',
                    'relation_type' => $relations_info->current()['type'],
                    'relation'      => $this->relation
                ]
            )
        );
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

namespace {:namespace:};

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\ModelTrait\{:trait:};

use PommProject\Foundation\Where;

use {:namespace:}\AutoStructure\{:entity:} as {:entity:}Structure;
use {:namespace:}\{:entity:};

/**
 * {:entity:}Model
 *
 * Model class for {:relation_type:} {:relation:}.
 *
 * @see Model
 */
class {:entity:}Model extends Model
{
    use {:trait:};

    /**
     * __construct()
     *
     * Model constructor
     *
     * @access public
     * @return void
     */
    protected function __construct()
    {
        $this->structure = new {:entity:}Structure;
        $this->flexible_entity_class = "\{:namespace:}\{:entity:}";
    }
}

_;
    }
}
