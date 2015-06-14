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
