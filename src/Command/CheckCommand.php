<?php

namespace JamesHalsall\Licenser\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * License check command.
 *
 * @package JamesHalsall\Licenser\Command
 * @author  James Halsall <james.t.halsall@googlemail.com>
 */
class CheckCommand extends AbstractLicenserCommand
{
    /**
     * Configures the command.
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('check')
            ->setDescription('Checks if your source files have the correct license information.')
        ;
    }

    /**
     * Executes the command
     *
     * @param InputInterface  $input  An input stream
     * @param OutputInterface $output An output stream
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initializeLicenser($input, $output);

        $sources = $input->getArgument('sources');

        $this->licenser->check($sources);
    }
}
