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
     * The exit status for success.
     *
     * @var int EXIT_STATUS_SUCCESS
     */
    const EXIT_STATUS_SUCCESS = 0;

    /**
     * The exit status for an error.
     *
     * @var int EXIT_STATUS_ERROR
     */
    const EXIT_STATUS_ERROR = 1;

    /**
     * Execute the response.
     *
     * Execute the response and exit the script with the exit code.
     *
     * @return void
     */
    public function execute();
}
