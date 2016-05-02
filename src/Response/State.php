<?php
/**
 * Represents a state which results after performing a check for a Nagios
 * plugin.
 *
 * @package HotelsNL
 * @subpackage Nagios
 */

namespace HotelsNL\Nagios\Response;

/**
 * The class which represents a state which results after performing a check for
 * a Nagios plugin.
 */
class State
{
    /**
     * The exit code of an 'OK' state.
     *
     * @var int STATE_OK
     */
    const STATE_OK = 0;

    /**
     * The exit code of a 'WARNING' state.
     *
     * @var int STATE_WARNING
     */
    const STATE_WARNING = 1;

    /**
     * The exit code of a 'CRITICAL' state.
     *
     * @var int STATE_CRITICAL
     */
    const STATE_CRITICAL = 2;

    /**
     * The exit code of an 'UNKNOWN' state.
     *
     * @var int STATE_UNKNOWN
     */
    const STATE_UNKNOWN = 3;

    /**
     * Initialize a new State.
     *
     * @param int $code
     */
    public function __construct($code)
    {
        $this->setCode($code);
    }

    /**
     * The code of which the state represents.
     *
     * @var int $code
     */
    private $code;

    /**
     * Get the textual representation of all states.
     *
     * @return string[]
     */
    protected static function getStateTexts()
    {
        return array(
            static::STATE_OK => 'OK',
            static::STATE_WARNING => 'WARNING',
            static::STATE_CRITICAL => 'CRITICAL',
            static::STATE_UNKNOWN => 'UNKNOWN'
        );
    }

    /**
     * Get the textual representation of the state.
     *
     * @return string
     */
    public function getTextRepresentation()
    {
        $stateTexts = $this->getStateTexts();

        return $stateTexts[$this->getCode()];
    }

    /**
     * Get the code of which the state represents.
     *
     * @return int
     * @throws \LogicException When code is not set.
     */
    public function getCode()
    {
        if (!isset($this->code)) {
            throw new \LogicException('Code is not set.');
        }

        return $this->code;
    }

    /**
     * Set the code of which the state represents.
     *
     * @param int $code
     * @return static
     * @throws \InvalidArgumentException When $code is invalid.
     */
    private function setCode($code)
    {
        $stateTexts = static::getStateTexts();

        if (!in_array($code, $stateTexts, true)) {
            throw new \InvalidArgumentException(
                'Invalid code supplied: ' . var_export($code, true)
            );
        }

        $this->code = $code;

        return $this;
    }
}
