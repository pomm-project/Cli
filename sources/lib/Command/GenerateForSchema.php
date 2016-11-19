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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * GenerateForSchema
 *
 * Generate a Structure, a model and an entity class if they do not already
 * exist (unless --force is specified).
 *
 * @package   Cli
 * @copyright 2014 - 2015 Grégoire HUBERT
 * @author    Grégoire HUBERT
 * @license   X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see       SchemaAwareCommand
 */
class GenerateForSchema extends SchemaAwareCommand
{
    /**
     * configure
     *
     * @see Command
     */
    public function configure()
    {
        $this
            ->setName('pomm:generate:schema-all')
            ->setDescription('Generate structure, model and entity file for all relations in a schema.')
            ;
        parent::configure();
        $this
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force overwriting existing files.'
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

        $relations = $session->getInspector()
            ->getSchemaRelations($this->fetchSchemaOid()
        );

        $output->writeln(
            sprintf(
                "Scanning schema '<fg=green>%s</fg=green>'.",
                $this->schema
            )
        );

        if ($relations->isEmpty()) {
            $output->writeln("<bg=yellow>No relations found.</bg=yellow>");
        } else {
            foreach ($relations as $relation_info) {
                $command = $this->getApplication()->find('pomm:generate:relation-all');
                $arguments = [
                    'command'          => 'pomm:generate:relation-all',
                    'config-name'      => $this->config_name,
                    'relation'         => $relation_info['name'],
                    'schema'           => $this->schema,
                    '--force'          => $input->getOption('force'),
                    '--bootstrap-file' => $input->getOption('bootstrap-file'),
                    '--prefix-dir'     => $input->getOption('prefix-dir'),
                    '--prefix-ns'      => $input->getOption('prefix-ns'),
                    '--flexible-container' => $input->getOption('flexible-container'),
                    '--psr4'           => $input->getOption('psr4'),
                    '--dir-pattern'    => $input->getOption('dir-pattern')
                ];
                $command->run(new ArrayInput($arguments), $output);
            }
        }
    }
}
