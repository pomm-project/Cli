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

use PommProject\Cli\Command\PommAwareCommand;

/**
 * BaseGenerate
 *
 * Base class for generator commands.
 *
 * @abstract
 * @package Cli
 * @copyright 2014 Grégoire HUBERT
 * @author Grégoire HUBERT
 * @license X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see PommAwareCommand
 */
abstract class BaseGenerate extends PommAwareCommand
{
    protected $relation;
    protected $schema;
    protected $prefix_dir;
    protected $prefix_ns;
    protected $filename;
    protected $namespace;

    protected function configure()
    {
        parent::configure();

        $this
            ->addArgument(
                'relation',
                InputArgument::REQUIRED,
                'Relation to inspect.'
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
            )
            ->addArgument(
                'schema',
                InputArgument::OPTIONAL,
                'Schema of the relation.',
                'public'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->relation = $input->getArgument('relation');
        $this->schema   = $input->getArgument('schema');

        if (!$this->schema) {
            $this->schema = 'public';
        }

        $this->prefix_dir = $input->getOption('prefix-dir');
        $this->prefix_ns  = $input->getOption('prefix-ns');
    }
}
