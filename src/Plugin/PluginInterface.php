<?php
/**
 * Represents the interface of a Nagios plugin.
 *
 * @package HotelsNL
 * @subpackage Nagios
 */

namespace HotelsNL\Nagios\Plugin;

use \HotelsNL\Nagios\Response\ResponseInterface;

/**
 * The interface of a Nagios plugin.
 */
interface PluginInterface
{
    /**
     * Execute the Nagios plugin and return the result.
     *
     * @return ResponseInterface
     */
    public function execute();

    /**
     * Get the help documentation.
     *
     * @return string
     */
    public function getHelp();

    /**
     * Get the version of the plugin.
     *
     * @return string
     */
    public function getVersion();
}
