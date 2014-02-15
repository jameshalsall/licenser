<?php

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
