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
use PommProject\ModelManager\Generator\EntityGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * GenerateEntity
 *
 * Entity generation command.
 *
 * @package   Cli
 * @copyright 2014 - 2015 Grégoire HUBERT
 * @author    Grégoire HUBERT
 * @license   X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see       PommAwareCommand
 */
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
This command generates an empty FlexibleEntity class in the given directory with the given namespace. By default, it creates a tree structure in the following format: ConfigName/NameSchema.

In order to comply with the project’s autoloading rules, it is possible to prefix this directory structure and / or namespace:

<info>pomm:generate:entity -d sources/lib/Model -a 'Vendor\Project\Model' --psr4 builder_name</info>

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
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force overwriting an existing file.'
            )
            ->addOption(
                'psr4',
                null,
                InputOption::VALUE_NONE,
                'Use PSR4 structure.'
            )
            ->addOption(
                'dir-pattern',
                null,
                InputOption::VALUE_OPTIONAL,
                'Specify the pattern path for files.',
                "{Session}/{Schema}Schema"
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

        $session = $this->mustBeModelManagerSession($this->getSession());

        $this->pathFile = $this->getPathFile(
            $input->getArgument('config-name'),
            $this->relation,
            '',
            '',
            $input->getOption('psr4'),
            $input->getOption('dir-pattern')
        );

        $this->namespace = $this->getNamespace($input->getArgument('config-name'), '', $input->getOption('dir-pattern'));

        $this->updateOutput(
            $output,
            (new EntityGenerator(
                $session,
                $this->schema,
                $this->relation,
                $this->pathFile,
                $this->namespace,
                $this->flexible_container
            ))->generate(new ParameterHolder(['force' => $input->getOption('force')]))
        );
    }
}
