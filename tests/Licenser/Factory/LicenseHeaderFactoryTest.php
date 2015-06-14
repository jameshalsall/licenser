<?php

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
    public function testCreateFromLicenseName($licenseName, $expectedThatLicenseExists = true)
    {
        if (false === $expectedThatLicenseExists) {
            $this->setExpectedException('\InvalidArgumentException');
            $this->getFactory()->createFromLicenseName($licenseName);
        } else {
            $licenseHeader = $this->getFactory()->createFromLicenseName($licenseName);
            $this->assertNotEmpty($licenseHeader);
        }
    }

    public function getLicenseNameFixtures()
    {
        return array(
            array('mit'),
            array('apache-2.0'),
            array('gplv3', false)
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
