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

namespace JamesHalsall\Licenser\Model;

/**
 * Token removal model
 *
 * @package JamesHalsall\Licenser\Token
 * @author  Mark Wilson <mark@89allport.co.uk>
 */
class TokenRemoval
{
    /**
     * Start position
     *
     * @var integer
     */
    private $start;

    /**
     * Length of removal
     *
     * @var integer
     */
    private $length;

    /**
     * Constructor.
     *
     * @param integer $start  Start position
     * @param integer $length Length of removal
     */
    public function __construct($start, $length)
    {
        $this->start = $start;
        $this->length = $length;
    }

    /**
     * Get removal length
     *
     * @return integer
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Get start position
     *
     * @return integer
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Get end position
     *
     * @return integer
     */
    public function getEnd()
    {
        return $this->start + $this->length;
    }
}
