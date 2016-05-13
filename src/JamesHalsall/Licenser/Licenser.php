<?php

namespace JamesHalsall\Licenser;

use JamesHalsall\Licenser\Model\TokenRemoval;
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
    const VERSION = '0.4.0';

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
     * @param string $path            The path to the files/directory
     * @param bool   $replaceExisting True to replace existing license headers
     * @param bool   $dryRun          True to report modified files and to not make any modifications
     */
    public function process($path, $replaceExisting = false, $dryRun = false)
    {
        $iterator = $this->finder->name('*.php')
                                 ->in(realpath($path));

        foreach ($iterator as $file) {
            $this->processFile($file, $replaceExisting, $dryRun);
        }
    }

    /**
     * Processes a single file
     *
     * @param SplFileInfo $file            The path to the file
     * @param bool        $replaceExisting True to replace existing license header
     * @param bool        $dryRun          True to report a modified file and to not make modifications
     */
    private function processFile(SplFileInfo $file, $replaceExisting, $dryRun)
    {
        if ($file->isDir()) {
            return;
        }

        $tokens = token_get_all($file->getContents());
        $licenseTokenIndex = null;

        foreach ($tokens as $index => $token) {
            if ($token[0] === T_COMMENT) {
                $licenseTokenIndex = $index;
            }

            // if we reach the class declaration then it does not have a license
            if ($token[0] === T_CLASS) {
                break;
            }
        }

        if (null !== $licenseTokenIndex && true === $replaceExisting) {
            $this->removeExistingLicense($file, $tokens, $licenseTokenIndex, $dryRun);
        }

        if (null === $licenseTokenIndex || true === $replaceExisting) {
            $this->log(sprintf('<fg=green>[+]</> Adding license header for <options=bold>%s</>', $file->getRealPath()));

            if (true === $dryRun) {
                return;
            }

            $license = explode(PHP_EOL, $this->licenseHeader);
            $license = array_map(function ($licenseLine) {
                return rtrim(' * ' . $licenseLine);
            }, $license);

            $license = implode(PHP_EOL, $license);
            $content = preg_replace('/<\?php/', '<?php' . PHP_EOL . PHP_EOL . '/*' . PHP_EOL . $license . PHP_EOL . ' */', $file->getContents(), 1);
            file_put_contents($file->getRealPath(), $content);
        } else {
            $this->log(sprintf('<fg=cyan>[S]</> Skipping <options=bold>%s</>', $file->getRealPath()));
        }
    }

    /**
     * Removes an existing license header from a file
     *
     * @param SplFileInfo $file         The file to remove the license header from
     * @param array       $tokens       File token information
     * @param integer     $licenseIndex License token index
     * @param bool        $dryRun       True to report a modified file and not to make modifications
     */
    private function removeExistingLicense(SplFileInfo $file, array $tokens, $licenseIndex, $dryRun)
    {
        $this->log(sprintf('<fg=red>[-]</> Removing license header for <options=bold>%s</>', $file->getRealPath()));

        if (true === $dryRun) {
            return;
        }

        $content = $file->getContents();

        $removals = array();

        // ignore index 0 (this should always be <?php tag) and find all whitespace tokens before license
        for ($index = 1; $index <= $licenseIndex; $index++) {
            $token = $tokens[$index];

            if ($token[0] !== T_WHITESPACE && $token[0] !== T_COMMENT) {
                continue;
            }

            $startLineNumber = $token[2];
            $removalLength   = strlen($token[1]);

            // find start line in content
            $currentLineNumber = 1;
            $removalStart = 0;

            while ($currentLineNumber < $startLineNumber) {
                $removalStart = strpos($content, PHP_EOL, $removalStart) + strlen(PHP_EOL);
                $currentLineNumber++;
            }

            $removals[] = new TokenRemoval($removalStart, $removalLength);
        }

        $removalOffset = 0;

        /** @var $removal TokenRemoval */
        foreach ($removals as $removal) {
            $content = substr($content, 0, $removal->getStart() - $removalOffset) . substr($content, $removal->getEnd());

            $removalOffset += $removal->getLength();
        }

        file_put_contents($file->getRealPath(), $content);
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
