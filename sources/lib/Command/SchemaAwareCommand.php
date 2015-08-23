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

use PommProject\Cli\Exception\CliException;
use PommProject\Foundation\Inflector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * SchemaAwareCommand
 *
 * Base class for generator commands.
 *
 * @abstract
 * @package   Cli
 * @copyright 2014 - 2015 Grégoire HUBERT
 * @author    Grégoire HUBERT
 * @license   X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see       PommAwareCommand
 */
abstract class SchemaAwareCommand extends SessionAwareCommand
{
    protected $schema;
    protected $prefix_dir;
    protected $prefix_ns;
    protected $pathFile;
    protected $namespace;
    protected $flexible_container;

    /**
     * configure
     *
     * @see PommAwareCommand
     */
    protected function configureRequiredArguments()
    {
        parent::configureRequiredArguments()
            ->addOption(
                'prefix-dir',
                'd',
                InputOption::VALUE_REQUIRED,
                'Indicate a directory prefix.',
                '.'
            )
            ->addOption(
                'prefix-ns',
                'a',
                InputOption::VALUE_REQUIRED,
                'Indicate a namespace prefix.',
                ''
            )
        ;

        return $this;
    }

    /**
     * configureOptionals
     *
     * @see PommAwareCommand
     */
    protected function configureOptionals()
    {
        parent::configureOptionals()
            ->addArgument(
                'schema',
                InputArgument::OPTIONAL,
                'Schema of the relation.',
                'public'
            )
            ->addOption(
                'flexible-container',
                null,
                InputOption::VALUE_REQUIRED,
                'Use an alternative flexible entity container',
                'PommProject\ModelManager\Model\FlexibleEntity'
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
        $this->schema   = $input->getArgument('schema');

        if (!$this->schema) {
            $this->schema = 'public';
        }

        $this->prefix_dir = $input->getOption('prefix-dir');
        $this->prefix_ns  = $input->getOption('prefix-ns');
        $this->flexible_container = $input->getOption('flexible-container');
    }

    /**
     * getPathFile
     *
     * Create path file from parameters and namespace.
     *
     * @access protected
     * @param  string $config_name
     * @param  string $file_suffix
     * @param  string $extra_dir
     * @param  string $file_name
     * @param  bool   $format_psr4
     * @return string
     */
    protected function getPathFile($config_name, $file_name, $file_suffix = '', $extra_dir = '', $format_psr4 = null)
    {
        $format_psr4 = $format_psr4 === null ? false : (bool) $format_psr4;

        $prefix_ns = "";
        if (!$format_psr4) {
            $prefix_ns = str_replace('\\', '/', trim($this->prefix_ns, '\\'));
        }

        $elements =
            [
                ltrim($this->prefix_dir, '/'),
                $prefix_ns,
                Inflector::studlyCaps($config_name),
                Inflector::studlyCaps(sprintf("%s_schema", $this->schema)),
                $extra_dir,
                sprintf("%s%s.php", Inflector::studlyCaps($file_name), $file_suffix)
            ];

        return join('/', array_filter($elements, function ($val) { return $val != null; }));
    }

    /**
     * getNamespace
     *
     * Create namespace from parameters.
     *
     * @access protected
     * @param  string $config_name
     * @param  string $extra_ns
     * @return string
     */
    protected function getNamespace($config_name, $extra_ns = '')
    {
        $elements =
            [
                $this->prefix_ns,
                Inflector::studlyCaps($config_name),
                Inflector::studlyCaps(sprintf("%s_schema", $this->schema)),
                $extra_ns
            ];

        return join('\\', array_filter($elements, function ($val) { return $val != null; }));
    }

    /**
     * fetchSchemaOid
     *
     * Get the schema Oid from database.
     *
     * @access protected
     * @return int $oid
     * @throws CliException
     */
    protected function fetchSchemaOid()
    {
        $schema_oid = $this
            ->getSession()
            ->getInspector()
            ->getSchemaOid($this->schema)
            ;

        if ($schema_oid === null) {
            throw new CliException(
                sprintf(
                    "Could not find schema '%s'.",
                    $this->schema
                )
            );
        }

        return $schema_oid;
    }
}
