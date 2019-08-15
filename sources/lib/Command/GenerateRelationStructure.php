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

use PommProject\Foundation\ParameterHolder;
use PommProject\ModelManager\Generator\StructureGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * GenerateRelationStructure
 *
 * Command to scan a relation and (re)build the according structure file.
 *
 * @package   Cli
 * @copyright 2014 - 2015 Grégoire HUBERT
 * @author    Grégoire HUBERT
 * @license   X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see       Command
 */
class GenerateRelationStructure extends RelationAwareCommand
{
    /**
     * configure
     *
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('pomm:generate:structure')
            ->setDescription('Generate a RowStructure file based on table schema.')
            ->setHelp(<<<HELP
HELP
        )
            ;
        parent::configure();
        $this
            ->addOption(
                'psr4',
                null,
                InputOption::VALUE_NONE,
                'Use PSR4 structure.'
            )
            ->addOption(
                'path-pattern',
                null,
                InputOption::VALUE_REQUIRED,
                'Use a different directory pattern when generating classes.',
                '{session}/{schema}Schema'
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

        $session = $this->mustBeModelManagerSession($this->getSession());

        $this->pathFile = $this->getPathFile(
            $input->getArgument('config-name'),
            $this->relation,
            null,
            'AutoStructure',
            $input->getOption('psr4'),
            $input->getOption('path-pattern')
        );
        $this->namespace = $this->getNamespace($input->getArgument('config-name'), 'AutoStructure', $input->getOption('path-pattern'));

        $this->updateOutput(
            $output,
            (new StructureGenerator(
                $session,
                $this->schema,
                $this->relation,
                $this->pathFile,
                $this->namespace
            ))->generate(new ParameterHolder())
        );
    }
}
