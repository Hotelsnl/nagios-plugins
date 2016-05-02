<?php
/**
 * Represents the interface of a response.
 *
 * @package HotelsNL
 * @subpackage Nagios
 */

namespace HotelsNL\Nagios\Response;

/**
 * The interface of a response.
 */
interface ResponseInterface
{
    /**
     * Execute the response.
     *
     * Execute the response and exit the script with the exit code.
     *
     * @return void
     */
    public function execute();
}
