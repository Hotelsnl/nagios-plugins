<?php
/**
 * Represents the abstract of a response.
 *
 * @package HotelsNL
 * @subpackage Nagios
 */

namespace HotelsNL\Nagios\Response;

/**
 * The class which represents an abstract of a response.
 */
abstract class ResponseAbstract implements ResponseInterface
{
    /**
     * The exit status of the response.
     *
     * @var int $exitStatus
     */
    private $exitStatus;

    /**
     * Initialize a new ResponseAbstract.
     *
     * @param int $exitStatus
     */
    public function __construct($exitStatus)
    {
        $this->setExitStatus($exitStatus);
    }

    /**
     * Execute the response.
     *
     * Execute the response and exit the script with the exit code.
     *
     * @return void
     */
    public function execute()
    {
        exit($this->getExitStatus());
    }

    /**
     * Get the exit status of the response.
     *
     * @return int
     * @throws \LogicException When exitStatus is not set.
     */
    protected function getExitStatus()
    {
        if (!isset($this->exitStatus)) {
            throw new \LogicException('ExitStatus is not set.');
        }

        return $this->exitStatus;
    }

    /**
     * Set the exit status of the response.
     *
     * @param int $exitStatus
     * @return ResponseAbstract
     * @throws \InvalidArgumentException When $exitStatus is not of type int.
     */
    private function setExitStatus($exitStatus)
    {
        if (!is_int($exitStatus)) {
            throw new \InvalidArgumentException(
                'Invalid exitStatus supplied: ' . var_export($exitStatus, true)
            );
        }

        $this->exitStatus = $exitStatus;

        return $this;
    }
}
