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
    const VERSION = '0.2.1';

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
     * @param string  $path           The path to the files/directory
     * @param boolean $removeExisting True to remove existing license headers in files before adding
     *                                new license (defaults to false)
     */
    public function process($path, $removeExisting = false)
    {
        $iterator = $this->getFiles($path);

        foreach ($iterator as $file) {
            $this->processFile($file, $removeExisting);
        }
    }

    /**
     * Processes a single file
     *
     * @param SplFileInfo $file           The path to the file
     * @param boolean     $removeExisting True to remove existing license header before adding new one
     */
    private function processFile(SplFileInfo $file, $removeExisting)
    {
        if ($file->isDir()) {
            return;
        }

        $tokens = token_get_all($file->getContents());

        $licenseTokenIndex = $this->getLicensetokenIndex($tokens);

        if (null !== $licenseTokenIndex && true === $removeExisting) {
            $this->removeExistingLicense($file, $tokens, $licenseTokenIndex);
        }

        if (null === $licenseTokenIndex || true === $removeExisting) {
            $this->log(sprintf('Adding license header for "%s"', $file->getRealPath()));

            $license = $this->getLicenseAsComment();
            $content = preg_replace('/<\?php/', '<?php' . PHP_EOL . PHP_EOL . $license , $file->getContents(), 1);
            file_put_contents($file->getRealPath(), $content);
        } else {
            $this->log(sprintf('Skipping "%s"', $file->getRealPath()));
        }
    }

    /**
     * Removes an existing license header from a file
     *
     * @param SplFileInfo $file         The file to remove the license header from
     * @param array       $tokens       File token information
     * @param integer     $licenseIndex License token index
     */
    private function removeExistingLicense(SplFileInfo $file, array $tokens, $licenseIndex)
    {
        $this->log(sprintf('Removing license header for "%s"', $file->getRealPath()));

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

    /**
     * @param string  $path The path to the files/directory
     */
    public function check($path)
    {
        foreach ( $this->getFiles($path) as $file) {
            $this->checkFile($file);
        }
    }

    /**
     * Checks the header of 1 file
     * @param SplFileInfo $file
     */
    private function checkFile(SplFileInfo $file)
    {
        $tokens = token_get_all($file->getContents());
        $licenseTokenIndex = $this->getLicensetokenIndex($tokens);

        if ($licenseTokenIndex === null) {
            $this->log(sprintf('Missing license header in "%s"', $file->getRealPath()));
        } elseif ($tokens[$licenseTokenIndex][1] != $this->getLicenseAsComment()) {
            $this->log(sprintf('Different license header in "%s"', $file->getRealPath()));
        }
    }

    /**
     * @param $path
     * @return SplFileInfo[]
     */
    private function getFiles($path)
    {
        if (is_file($path)) {
            return [new SplFileInfo($path, '', '')];
        } else {
            $iterator = $this->finder->name('*.php')->in(realpath($path));
        }
        return $iterator;
    }

    /**
     * returns the index of the first token that is a comment
     *
     * @param $tokens
     * @return int|null the index of the token or null when no comment is found before the class declaration
     */
    private function getLicensetokenIndex($tokens)
    {
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
        return $licenseTokenIndex;
    }

    /**
     * returns the license formatted as a comment
     * @return string
     */
    private function getLicenseAsComment()
    {
        $license = explode(PHP_EOL, $this->licenseHeader);
        $license = array_map(function ($licenseLine) {
            return rtrim(' * ' . $licenseLine);
        }, $license);

        $license = implode(PHP_EOL, $license);
        $license = '/*' . PHP_EOL . $license . PHP_EOL . ' */';
        return $license;
    }
}
