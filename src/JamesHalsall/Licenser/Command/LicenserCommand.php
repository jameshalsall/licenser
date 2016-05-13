<?php

namespace JamesHalsall\Licenser\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Licenser console command.
 *
 * Hooks the Licenser into the symfony console.
 *
 * @package JamesHalsall\Licenser\Command
 * @author  James Halsall <james.t.halsall@googlemail.com>
 * @see     JamesHalsall\Licenser\Licenser
 */
class LicenserCommand extends AbstractLicenserCommand
{
    /**
     * Configures the command interface
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('run')
            ->setDescription('Runs the licenser against the given source path')
            ->addOption(
                'replace-existing',
                'r',
                InputOption::VALUE_NONE,
                'Replace existing license headers'
            )
            ->addOption(
                'dry-run',
                '',
                InputOption::VALUE_NONE,
                'If specified, the command will report a list of affected files but will make no modifications'
            )
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

        $this->licenser->process(
            $sources,
            (bool) $input->getOption('replace-existing'),
            (bool) $input->getOption('dry-run')
        );

        return 0;
    }
}
