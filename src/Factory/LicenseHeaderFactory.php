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
     * @param string $licenseName  The license name
     * @param array  $replacements Values to use replacing placeholders in the license header (indexed by their placeholder name)
     *
     * @throws \InvalidArgumentException If the license name doesn't exist
     *
     * @return string
     */
    public function createFromLicenseName($licenseName, array $replacements = array())
    {
        $replacements['thisYear'] = date('Y');

        try {
            return $this->twig->render($licenseName, $replacements);
        } catch (\Twig_Error_Loader $e) {
            throw new \InvalidArgumentException('Invalid license name provided');
        }
    }
}
