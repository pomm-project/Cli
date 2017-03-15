<?php
/*
 * This file is part of Pomm's Cli package.
 *
 * (c) 2014 - 2017 Grégoire HUBERT <hubert.greg@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PommProject\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * InspectType
 *
 * Display the list of converted types.
 *
 * @package   Cli
 * @copyright 2014 - 2015 Grégoire HUBERT
 * @author    Grégoire HUBERT
 * @license   X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see       SessionAwareCommand
 */
class InspectType extends SessionAwareCommand
{
    /**
     * configure
     *
     * @see command
     */
    protected function configure()
    {
        $this
            ->setName("pomm:inspect:type")
            ->setDescription("Show converted types list.")
            ;

        parent::configure();
    }

    /**
     * execute
     *
     * Set pomm dependent variables.
     *
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $types = $this
            ->getSession()
            ->getPoolerForType('converter')
            ->getConverterHolder()
            ->getTypes();
        $types = array_filter($types, function ($type) {
            return !preg_match('/^pg_catalog\./', $type);
        });
        natcasesort($types);

        foreach ($types as $type) {
                $output->writeln($type);
        }
    }
}
