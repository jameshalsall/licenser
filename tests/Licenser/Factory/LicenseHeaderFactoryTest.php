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

namespace JamesHalsall\Licenser\Tests\Factory;

use JamesHalsall\Licenser\Factory\LicenseHeaderFactory;

/**
 * LicenseHeaderFactory tests
 *
 * @package JamesHalsall\Licenser\Tests\Factory
 * @author  James Halsall <james@rippleffect.com>
 */
class LicenseHeaderFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getLicenseNameFixtures
     */
    public function testCreateFromLicenseName($licenseName, array $replacements = array(), $expectedThatLicenseExists = true)
    {
        if (false === $expectedThatLicenseExists) {
            $this->setExpectedException('\InvalidArgumentException');
            $this->getFactory()->createFromLicenseName($licenseName);
        } else {
            $licenseHeader = $this->getFactory()->createFromLicenseName($licenseName, $replacements);
            $this->assertNotEmpty($licenseHeader);
            $this->assertContains(date('Y'), $licenseHeader);

            foreach ($replacements as $replacement) {
                $this->assertContains($replacement, $licenseHeader);
            }
        }
    }

    public function getLicenseNameFixtures()
    {
        return array(
            array('mit'),
            array('apache-2.0'),
            array('gplv3', array(), false),
            array('mit', array('owners' => 'james.t.halsall@googlemail.com')),
            array('mit', array('owners' => 'james.t.halsall@googlemail.com, james.t.halsall@gmail.com'))
        );
    }

    /**
     * @return LicenseHeaderFactory
     */
    private function getFactory()
    {
        return new LicenseHeaderFactory(
            new \Twig_Environment(new \Twig_Loader_Filesystem(__DIR__ . '/../../../licenses'))
        );
    }
}
