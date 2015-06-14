<?php

namespace JamesHalsall\Licenser\Factory;

/**
 * License Header Factory
 *
 * @package JamesHalsall\Licenser\Factory
 * @author  James Halsall <james.t.halsall@googlemail.com>
 */
class LicenseHeaderFactory
{
    /**
     * The twig templating environment for generating license headers
     *
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Constructor.
     *
     * @param \Twig_Environment $twig The twig templating environment for generating license headers
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Creates a license header string from a license name
     *
     * @param string $licenseName The license name
     *
     * @throws \InvalidArgumentException If the license name doesn't exist
     *
     * @return string
     */
    public function createFromLicenseName($licenseName)
    {
        try {
            return $this->twig->render($licenseName);
        } catch (\Twig_Error_Loader $e) {
            throw new \InvalidArgumentException('Invalid license name provided');
        }
    }
}
