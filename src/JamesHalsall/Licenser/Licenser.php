<?php

namespace JamesHalsall\Licenser;

use JamesHalsall\ConstantResolver\ConstantResolver;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Licenser
 *
 * @package JamesHalsall\Licenser
 * @author  James Halsall <james.t.halsall@googlemail.com>
 */
class Licenser
{
    const VERSION = '0.1.0';

    /**
     * The finder component
     *
     * @var Finder
     */
    private $finder;

    /**
     * The license header
     *
     * @var string
     */
    private $licenseHeader;

    /**
     * An output stream
     *
     * @var OutputInterface
     */
    private $output;

    /**
     * Constructor.
     *
     * @param Finder $finder        The file finder
     */
    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Sets the output stream as an optional dependency
     *
     * @param OutputInterface $output An output stream
     */
    public function setOutputStream(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Sets the license header content
     *
     * @param string $licenseHeader The license header content to prepend
     */
    public function setLicenseHeader($licenseHeader)
    {
        $this->licenseHeader = $licenseHeader;
    }

    /**
     * Gets the license header
     *
     * @throws \RuntimeException If the license header has not been set
     *
     * @return string
     */
    public function getLicenseHeader()
    {
        if (null === $this->licenseHeader) {
            throw new \RuntimeException('No license header has been set on the Licenser instance');
        }

        return $this->licenseHeader;
    }

    /**
     * Processes a path and adds licenses
     *
     * @param string $path The path to the files/directory
     */
    public function process($path)
    {
        $iterator = $this->finder->name('*.php')
                                 ->in(realpath($path));

        foreach ($iterator as $file) {
            $this->processFile($file);
        }
    }

    /**
     * Processes a single file
     *
     * @param SplFileInfo $file The path to the file
     */
    private function processFile(SplFileInfo $file)
    {
        if ($file->isDir()) {
            return;
        }

        $tokens = token_get_all($file->getContents());
        $hasLicense = false;
        foreach ($tokens as $token) {

            if ($token[0] === T_COMMENT) {
                $hasLicense = true;
            }

            // if we reach the class declaration then it does not have a license
            if ($token[0] === T_CLASS) {
                break;
            }
        }

        if (false === $hasLicense) {
            $this->log('Adding license header for "' . $file->getRealPath() . '"');
            $content = str_replace("<?php", "<?php \n\n" . $this->licenseHeader, $file->getContents());
            file_put_contents($file->getRealPath(), $content);
        } else {
            $this->log('Skipping "' . $file->getRealPath() . '"');
        }
    }

    /**
     * Logs to the output stream
     *
     * @param string $message The message to log
     */
    private function log($message)
    {
        if (null === $this->output) {
            return;
        }

        $this->output->writeln($message);
    }
}
