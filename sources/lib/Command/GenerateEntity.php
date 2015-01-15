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

class GenerateEntity extends RelationAwareCommand
{
    /**
     * configure
     *
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('pomm:generate:entity')
            ->setDescription('Generate an Entity class.')
            ->setHelp(<<<HELP
HELP
        )
        ;
        parent::configure();
    }

    /**
     * configureOptionals
     *
     * @see PommAwareCommand
     */
    protected function configureOptionals()
    {
        parent::configureOptionals()
            ->addoption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force overwriting an existing file.'
            )
        ;

        return $this;
    }

    /**
     * execute
     *
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->filename = $this->getFileName($input->getArgument('config-name'));
        $this->namespace = $this->getNamespace($input->getArgument('config-name'));

        (new EntityGenerator(
            $this->getSession(),
            $this->schema,
            $this->relation,
            $this->filename,
            $this->namespace,
            $this->flexible_container
        ))->generate($input, $output);
    }
}
