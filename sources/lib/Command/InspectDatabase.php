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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

use PommProject\Foundation\ResultIterator;

use PommProject\Cli\Command\PommAwareCommand;

/**
 * InspectDatabase
 *
 * Return the list of schemas in the current database.
 *
 * @package Cli
 * @copyright 2014 Grégoire HUBERT
 * @author Grégoire HUBERT
 * @license X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see PommAwareCommand
 */
class InspectDatabase extends PommAwareCommand
{
    /**
     * configure
     *
     * @see Command
     */
    public function configure()
    {
        $this
            ->setName('inspect:database')
            ->setDescription('Show schemas in the current database.')
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
        $info = $this
            ->getSession()
            ->getInspector()
            ->getSchemas()
            ;
        $this->formatOutput($output, $info);
    }

    /**
     * formatOutput
     *
     * Format command output from the inspector's result.
     *
     * @access protected
     * @param  OutputInterface  $output
     * @param  ResultIterator   $iterator
     * @return null
     */
    protected function formatOutput(OutputInterface $output, ResultIterator $iterator)
    {
        $output->writeln(
            sprintf(
                "Found <info>%d</info> schemas in database.",
                $iterator->count()
            )
        );
        $table = (new Table($output))
            ->setHeaders(['name', 'oid ', 'relations', 'comment'])
            ;

        foreach ($iterator as $schema_info) {

            $table->addRow([
                sprintf("<fg=yellow>%s</fg=yellow>", $schema_info['name']),
                $schema_info['oid'],
                $schema_info['relations'],
                wordwrap($schema_info['comment'])
            ]);
        }

        $table->render();
    }
}
