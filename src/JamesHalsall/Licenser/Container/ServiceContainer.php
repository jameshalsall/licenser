<?php

namespace JamesHalsall\Licenser\Container;

use JamesHalsall\Licenser\Command\CheckCommand;
use JamesHalsall\Licenser\Command\LicenserCommand;
use JamesHalsall\Licenser\Factory\LicenseHeaderFactory;
use JamesHalsall\Licenser\Licenser;
use Pimple\Container;
use Symfony\Component\Finder\Finder;

/**
 * Service Container.
 *
 * @package JamesHalsall\Container
 * @author  James Halsall <james.t.halsall@googlemail.com>
 */
final class ServiceContainer extends Container
{
    /**
     * {@inheritDoc}
     */
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $this->setup();
    }

    /**
     * Setup service configuration.
     */
    private function setup()
    {
        $this['licenser'] = function () {
            return new Licenser(new Finder());
        };

        $this['command.licenser'] = function ($c) {
            return new LicenserCommand($c['licenser'], $c['license_header_factory']);
        };

        $this['command.check'] = function ($c) {
            return new CheckCommand($c['licenser'], $c['license_header_factory']);
        };

        $this['twig.templating'] = function () {
            $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../../licenses');
            return new \Twig_Environment($loader);
        };

        $this['license_header_factory'] = function ($c) {
            return new LicenseHeaderFactory($c['twig.templating']);
        };
    }
}
