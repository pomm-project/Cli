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
use Symfony\Component\Console\Output\OutputInterface;

/**
 * RelationAwareCommand
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
abstract class RelationAwareCommand extends SchemaAwareCommand
{
    protected $relation;

    /**
     * configure
     *
     * @see Command
     */
    protected function configure()
    {
        $this
            ->addArgument(
                'relation',
                InputArgument::REQUIRED,
                'Relation to inspect.'
            )
            ;

        parent::configure();
    }

    /**
     * execute
     *
     * see @Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->relation = $input->getArgument('relation');
    }
}
