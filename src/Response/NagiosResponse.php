<?php
/**
 * Represents the response for Nagios.
 *
 * @package HotelsNL
 * @subpackage Nagios
 */

namespace HotelsNL\Nagios\Response;

/**
 * The class which represents the response for Nagios.
 */
class NagiosResponse extends ResponseAbstract
{
    /**
     * Create a new NagiosResponse from a state.
     *
     * @param State $state
     * @return static
     */
    public static function fromState(State $state)
    {
        return new static($state->getCode());
    }
}
