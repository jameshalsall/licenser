<?php

namespace JamesHalsall\Licenser\Container;

use JamesHalsall\Licenser\Command\LicenserCommand;
use JamesHalsall\Licenser\Licenser;
use Symfony\Component\Finder\Finder;

/**
 * Service Container.
 *
 * @package JamesHalsall\Container
 * @author  James Halsall <james.t.halsall@googlemail.com>
 */
final class ServiceContainer extends \Pimple
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
        $this['licenser'] = function() {
            return new Licenser(new Finder());
        };

        $this['command'] = function($c) {
            return new LicenserCommand($c['licenser']);
        };
    }
}
