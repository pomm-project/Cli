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
use Symfony\Component\Console\Input\InputArgument;

use PommProject\Foundation\Session;
use PommProject\Foundation\Query\QueryPooler;
use PommProject\Foundation\Inspector\InspectorPooler;
use PommProject\Foundation\Converter\ConverterPooler;
use PommProject\Foundation\PreparedQuery\PreparedQueryPooler;

use PommProject\Cli\Exception\CliException;

class SessionAwareCommand extends Command
{
    private $session;

    /**
     * initialize
     *
     * @see Command
     */
    protected function configure()
    {
        $this->addOption(
            'bootstrap-file',
            '-b',
            InputArgument::OPTIONAL,
            'Complete path of the CLI bootstrap file.',
            sprintf("%s/.pomm_cli_bootstrap.php", getenv('PWD'))
        )
        ;
    }

    /**
     * execute
     *
     * see @Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config_file = $input->getOption('bootstrap-file');

        if (!file_exists($config_file)) {
            $output->writeln("<error>Could not load configuration file.</error>");

            throw new \RuntimeException(sprintf("I failed to load '%s'.", $config_file));
        }

        $session = require $config_file;

        if (!$session instanceOf Session) {
            $output->writeln("<error>Invalid configuration.</error>");

            throw new \LogicException(
                sprintf(
                    "Config file does not return a Session instance. ('%s' returned).",
                    get_class($session)
                )
            );
        }

        $this->session = $session
            ->registerClientPooler(new QueryPooler())
            ->registerClientPooler(new PreparedQueryPooler())
            ->registerClientPooler(new InspectorPooler())
            ->registerClientPooler(new ConverterPooler())
            ;
    }

    /**
     * getSession
     *
     * Return a session.
     *
     * @access protected
     * @param  sring $name
     * @return Session
     */
    protected function getSession()
    {
        if ($this->session === null) {
            throw new CliException(sprintf("Session not set in Command.\nDidn't you forget to call parent::execute() in your class ?"));
        }

        return $this->session;
    }
}
