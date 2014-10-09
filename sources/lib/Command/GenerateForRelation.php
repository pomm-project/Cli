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

use PommProject\Cli\Command\BaseGenerate;
use PommProject\Cli\Generator\EntityGenerator;
use PommProject\Cli\Generator\ModelGenerator;
use PommProject\Cli\Generator\StructureGenerator;

/**
 * GenerateForRelation
 *
 * Generate a Structure, a model and an entity class if they do not already
 * exist (unless --force is specified).
 *
 * @package Cli
 * @copyright 2014 Grégoire HUBERT
 * @author Grégoire HUBERT
 * @license X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see ModelGenerator
 */
class GenerateForRelation extends BaseGenerate
{
    public function configure()
    {
        $this
            ->setName('generate:all-relation')
            ->setDescription('Generate structure, model and entity file for a given relation.')
            ;
        parent::configure();
        $this
            ->addoption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force overwriting existing files.'
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

        (new StructureGenerator(
            $this->getSession(),
            $this->schema,
            $this->relation,
            $this->getFileName($input->getArgument('config-name'), null, 'AutoStructure'),
            $this->getNamespace($input->getArgument('config-name'), 'AutoStructure')
        ))->generate($input, $output);

        $filename = $this->getFileName($input->getArgument('config-name'), 'Model');
        if (!file_exists($filename) || $input->getOption('force')) {
            (new ModelGenerator(
                $this->getSession(),
                $this->schema,
                $this->relation,
                $filename,
                $this->getNamespace($input->getArgument('config-name'))
            ))->generate($input, $output);
        } elseif ($output->isVerbose()) {
            $output->writeln(sprintf(" <fg=red>✗</fg=red>  Skipping existing model file <fg=yellow>'%s'</fg=yellow>.", $filename));
        }

        $filename = $this->getFileName($input->getArgument('config-name'));
        if (!file_exists($filename) || $input->getOption('force')) {
            (new EntityGenerator(
                $this->getSession(),
                $this->schema,
                $this->relation,
                $filename,
                $this->getNamespace($input->getArgument('config-name'))
            ))->generate($input, $output);
        } elseif ($output->isVerbose()) {
            $output->writeln(sprintf(" <fg=red>✗</fg=red>  Skipping existing model file <fg=yellow>'%s'</fg=yellow>.", $filename));
        }
    }
}
