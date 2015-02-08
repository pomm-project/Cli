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

use PommProject\Foundation\ParameterHolder;
use PommProject\ModelManager\Generator\StructureGenerator;

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
    }

    /**
     * execute
     *
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->pathFile = $this->getPathFile($input->getArgument('config-name'), $this->relation, null, 'AutoStructure');
        $this->namespace = $this->getNamespace($input->getArgument('config-name'), 'AutoStructure');

        $this->updateOutput(
            $output,
            (new StructureGenerator(
                $this->getSession(),
                $this->schema,
                $this->relation,
                $this->pathFile,
                $this->namespace
            ))->generate(new ParameterHolder())
        );
    }
}
