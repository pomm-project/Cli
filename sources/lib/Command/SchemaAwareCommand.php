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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use PommProject\Cli\Command\PommAwareCommand;
use PommProject\Cli\Exception\CliException;

/**
 * BaseInspect
 *
 * Base class for all inspect commands.
 *
 * @package Cli
 * @copyright 2014 Grégoire HUBERT
 * @author Grégoire HUBERT
 * @license X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see PommAwareCommand
 * @abstract
 */
abstract class SchemaAwareCommand extends PommAwareCommand
{
    protected $schema_name;
    protected $schema_oid;

    /**
     * @see Command
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->addArgument(
                'schema',
                InputArgument::OPTIONAL,
                'Schema of the relation.',
                'public'
            )
        ;
    }

    /**
     * execute
     *
     * Get and check schema information.
     *
     * @see @Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->schema_name = $input->getArgument('schema');
        $this->schema_oid = $this-> getSession($input->getArgument('config-name'))-> getInspector()->getSchemaOid($this->schema_name);

        if ($this->schema_oid === null) {
            throw new CliException(sprintf("No such schema '%s'.", $this->schema_name));
        }
    }
}
