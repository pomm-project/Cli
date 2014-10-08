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
use PommProject\Cli\Command\PommAwareCommand;
use PommProject\Cli\Generator\StructureGenerator;

/**
 * GenerateRelationStructure
 *
 * Command to scan a relation and (re)build the according structure file.
 *
 * @package Cli
 * @copyright 2014 Grégoire HUBERT
 * @author Grégoire HUBERT
 * @license X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see Command
 */
class GenerateRelationStructure extends PommAwareCommand
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
            ->setName('generate:structure')
            ->setDescription('Generate a RowStructure file based on table schema.')
            ->addArgument(
                'relation',
                InputArgument::REQUIRED,
                'Relation to inspect.'
            )
            ->addArgument(
                'schema',
                InputArgument::OPTIONAL,
                'Schema of the relation.',
                'public'
            )
            ->addOption(
                'prefix-dir',
                'd',
                InputOption::VALUE_REQUIRED,
                'Indicate a directory prefix.'
            )
            ->addOption(
                'prefix-ns',
                'a',
                InputOption::VALUE_REQUIRED,
                'Indicate a namespace prefix.'
            )->setHelp(<<<HELP
HELP
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

        $relation = $input->getArgument('relation');
        $schema   = $input->getArgument('schema');

        if (!$schema) {
            $schema = 'public';
        }

        $prefix_dir = getEnv('PWD').$input->getOption('prefix-dir');
        $prefix_ns  = $input->getOption('prefix-ns');

        $filename = sprintf(
            "%s/%s/%s/%s/Structure/%s.php",
            $prefix_dir,
            str_replace('\\', '/', trim($prefix_ns, '\\')),
            Inflector::studlyCaps($input->getArgument('config-name')),
            Inflector::studlyCaps(sprintf("%sSchema", $schema)),
            Inflector::studlyCaps($relation)
        );

        $namespace = sprintf(
            "%s\\%s\\%s\\Structure",
            $prefix_ns,
            Inflector::studlyCaps($input->getArgument('config-name')),
            Inflector::studlyCaps(sprintf("%s_schema", $schema))
        );

        (new StructureGenerator(
            $this->getSession(),
            $schema,
            $relation,
            $filename,
            $namespace
        ))->generate($input, $output);
    }

}
