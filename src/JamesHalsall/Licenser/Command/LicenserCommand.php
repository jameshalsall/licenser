<?php

namespace JamesHalsall\Licenser\Command;

use JamesHalsall\Licenser\Factory\LicenseHeaderFactory;
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
     * The license header factory.
     *
     * @var LicenseHeaderFactory
     */
    private $licenseHeaderFactory;

    /**
     * Constructor
     *
     * @param Licenser             $licenser             The licenser utility
     * @param LicenseHeaderFactory $licenseHeaderFactory The license header factory
     */
    public function __construct(Licenser $licenser, LicenseHeaderFactory $licenseHeaderFactory)
    {
        $this->licenser = $licenser;
        $this->licenseHeaderFactory = $licenseHeaderFactory;

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
                'The name of a built in license or a path to the file containing your custom license header doc block ' .
                'as it will appear when prepended to your source files'
             )
             ->addOption(
                 'owners',
                 'o',
                 InputOption::VALUE_OPTIONAL,
                 'The owner email addresses of the licensed files. This is used in conjunction with the built-in ' .
                 'license to add the email address(es) of the license(es) to the license header. Can be a comma ' .
                 'separated list of email addresses or a single email address'
             )
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
             );
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
        $license = $input->getArgument('license');

        try {
            $licenseHeader = $this->licenseHeaderFactory->createFromLicenseName(
                $license,
                array('owners' => $input->getOption('owners'))
            );
        } catch (\InvalidArgumentException $e) {
            $licenseHeader = file_get_contents($license);
        }

        $this->licenser->setLicenseHeader($licenseHeader);
        $this->licenser->setOutputStream($output);

        $sources = $input->getArgument('sources');

        $this->licenser->process(
            $sources,
            (bool) $input->getOption('replace-existing'),
            (bool) $input->getOption('dry-run')
        );
    }
}
