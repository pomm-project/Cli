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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
class GenerateForRelation extends RelationAwareCommand
{
    public function configure()
    {
        $this
            ->setName('pomm:generate:relation-all')
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
            $this->writelnSkipFile($output, $filename, 'model');
        }

        $filename = $this->getFileName($input->getArgument('config-name'));
        if (!file_exists($filename) || $input->getOption('force')) {
            (new EntityGenerator(
                $this->getSession(),
                $this->schema,
                $this->relation,
                $filename,
                $this->getNamespace($input->getArgument('config-name')),
                $this->flexible_container
            ))->generate($input, $output);
        } elseif ($output->isVerbose()) {
            $this->writelnSkipFile($output, $filename, 'entity');
        }
    }

    /**
     * writelnSkipFile
     *
     * Write an informative message
     *
     * @access private
     * @param  string          $filename
     * @param  OutputInterface $output
     * @return void
     */
    private function writelnSkipFile(OutputInterface $output, $filename, $file_type = null)
    {
        $file_type = $file_type === null ? '' : sprintf("%s ", $file_type);

        $output->writeln(
            sprintf(
                " <fg=red>✗</fg=red>  <fg=blue>Preserving</fg=blue> existing %sfile <fg=yellow>'%s'</fg=yellow>.",
                $file_type,
                $filename
            )
        );
    }
}
