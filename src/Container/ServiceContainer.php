<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2016
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

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
            $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../licenses');
            return new \Twig_Environment($loader);
        };

        $this['license_header_factory'] = function ($c) {
            return new LicenseHeaderFactory($c['twig.templating']);
        };
    }
}
