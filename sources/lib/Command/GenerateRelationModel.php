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
use PommProject\Cli\Generator\ModelGenerator;

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
class GenerateRelationModel extends BaseGenerate
{

    /**
     * configure
     *
     * @see Command
     */
    public function configure()
    {
        $this
            ->setName('generate:model')
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

        $this->filename  = $this->getFileName($input->getArgument('config-name'), 'Model');
        $this->namespace = $this->getNamespace($input->getArgument('config-name'));

        (new ModelGenerator(
            $this->getSession(),
            $this->schema,
            $this->relation,
            $this->filename,
            $this->namespace
        ))->generate($input, $output);
    }
}
