<?php

namespace JamesHalsall\Licenser\Command;

use JamesHalsall\Licenser\Licenser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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
class LicenserCommand extends Command
{
    /**
     * The licenser utility
     *
     * @var Licenser
     */
    private $licenser;

    /**
     * Constructor
     *
     * @param Licenser $licenser The licenser utility
     */
    public function __construct(Licenser $licenser)
    {
        $this->licenser = $licenser;

        parent::__construct();
    }

    /**
     * Configures the command interface
     */
    protected function configure()
    {
        $this->setName('run')
             ->setDescription('Runs the licenser against the given source path')
             ->addArgument(
                 'sources',
                 InputArgument::REQUIRED,
                 'The path to the source files that the licenser will process'
             )
             ->addArgument(
                 'license',
                 InputArgument::REQUIRED,
                'The path to the file containing your license header doc block as it will appear when prepended to ' .
                'your source files'
             )
             ->addOption('remove-existing', 'r', InputOption::VALUE_OPTIONAL, 'Remove existing license headers', false);
    }

    /**
     * Executes the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $licenseHeader = file_get_contents($input->getArgument('license'));

        $this->licenser->setLicenseHeader($licenseHeader);
        $this->licenser->setOutputStream($output);

        $sources = $input->getArgument('sources');
        $this->licenser->process($sources, (boolean) $input->getOption('remove-existing'));
    }
}
