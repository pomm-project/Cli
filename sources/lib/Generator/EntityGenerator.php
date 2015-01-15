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

use PommProject\Foundation\Inflector;

use PommProject\Cli\Exception\GeneratorException;

/**
 * EntityGenerator
 *
 * Entity generator.
 *
 * @package Cli
 * @copyright 2014 Grégoire HUBERT
 * @author Grégoire HUBERT
 * @license X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see BaseGenerator
 */
class EntityGenerator extends BaseGenerator
{
    /**
     * generate
     *
     * Generate Entity file.
     *
     * @see BaseGenerator
     */
    public function generate(InputInterface $input, OutputInterface $output)
    {
        if (file_exists($this->filename) && !$input->getOption('force')) {
            throw new GeneratorException(sprintf("Cannot overwrite file '%s' without --force option.", $this->filename));
        }

        $this->outputFileCreation($output);
        $this->saveFile(
            $this->filename,
            $this->mergeTemplate(
                [
                    'namespace' => $this->namespace,
                    'entity'    => Inflector::studlyCaps($this->relation),
                    'relation'  => $this->relation,
                    'schema'    => $this->schema,
                    'flexible_container' => $this->flexibe_container,
                    'flexible_container_class' => array_reverse(explode('\\', $this->flexibe_container))[0]
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

use {:flexible_container:};

/**
 * {:entity:}
 *
 * Flexible entity for relation
 * {:schema:}.{:relation:}
 *
 * @see FlexibleEntity
 */
class {:entity:} extends {:flexible_container_class:}
{
}

_;
    }
}
