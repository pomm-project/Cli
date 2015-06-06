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

use PommProject\ModelManager\Generator\ModelGenerator;
use PommProject\Foundation\ParameterHolder;

/**
 * GenerateRelationModel
 *
 * Model generation command.
 *
 * @package Cli
 * @copyright 2014 Grégoire HUBERT
 * @author Grégoire HUBERT
 * @license X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see PommAwareCommand
 */
class GenerateRelationModel extends RelationAwareCommand
{

    /**
     * configure
     *
     * @see Command
     */
    public function configure()
    {
        $this
            ->setName('pomm:generate:model')
            ->setDescription('Generate a new model file.')
            ;
        parent::configure();
        $this
            ->addoption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force overwriting an existing file.'
            )
            ->addoption(
                'psr4',
                null,
                InputOption::VALUE_NONE,
                'Use PSR4 structure.'
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

        $this->pathFile  = $this->getPathFile($input->getArgument('config-name'), $this->relation, 'Model', '', $input->getOption('psr4'));
        $this->namespace = $this->getNamespace($input->getArgument('config-name'));

        $this->updateOutput(
            $output,
            (new ModelGenerator(
                $session,
                $this->schema,
                $this->relation,
                $this->pathFile,
                $this->namespace
            ))->generate(new ParameterHolder(['force' => $input->getOption('force')]))
        );
    }
}
