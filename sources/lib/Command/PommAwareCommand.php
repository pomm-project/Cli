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

use PommProject\Cli\Exception\CliException;
use PommProject\Foundation\Pomm;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * PommAwareCommand
 *
 * Base command for all Pomm Cli commands.
 *
 * @package Cli
 * @copyright 2014 Grégoire HUBERT
 * @author Grégoire HUBERT
 * @license X11 {@link http://opensource.org/licenses/mit-license.php}
 *
 *
 * @see Command
 */
class PommAwareCommand extends Command
{
    private $pomm;

    protected $config_file;

    /**
     * execute
     *
     * Set pomm dependent variables.
     *
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->config_file = $input->getOption('bootstrap-file');
    }

    /**
     * configureRequiredArguments
     *
     * In order to keep the same argument order for all commands, it is
     * necessary to be able to declare base required fields before subcommands.
     *
     * @access protected
     * @return PommAwareCommand $this
     */
    protected function configureRequiredArguments()
    {
        return $this;
    }

    /**
     * configureOptionals
     *
     * In order to keep the same argument order for all commands, it is
     * necessary to be able to declare base required fields before subcommands.
     *
     * @access protected
     * @return PommAwareCommand $this
     */
    protected function configureOptionals()
    {
        $this
            ->addOption(
            'bootstrap-file',
            '-b',
            InputArgument::OPTIONAL,
            'Complete path of the CLI bootstrap file.',
            sprintf("%s/.pomm_cli_bootstrap.php", getenv('PWD'))
        )
        ;

        return $this;
    }

    /**
     * configure
     *
     * @see command
     */
    protected function configure()
    {
        $this
            ->configureRequiredArguments()
            ->configureOptionals()
            ;
    }

    /**
     * getPomm
     *
     * Return the Pomm instance.
     *
     * @access protected
     * @return Pomm
     * @throws CliException
     */
    protected function getPomm()
    {
        if ($this->pomm === null) {
            if (!file_exists($this->config_file)) {
                throw new CliException(sprintf("Could not load configuration '%s'.", $this->config_file));
            }

            $this->pomm = require $this->config_file;

            if (!$this->pomm instanceof Pomm) {
                throw new CliException(sprintf("Invalid configuration. Bootstrap file must return a Pomm instance."));
            }
        }

        return $this->pomm;
    }

    /**
     * setPomm
     *
     * When used with a framework, it is useful to get the Pomm instance from
     * the framework configuration mechanism.
     *
     * @access public
     * @param  Pomm     $pomm
     * @return PommAwareCommand
     */
    public function setPomm(Pomm $pomm)
    {
        $this->pomm = $pomm;

        return $this;
    }
}
