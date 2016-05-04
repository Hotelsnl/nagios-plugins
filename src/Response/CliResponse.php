<?php
/**
 * Represents a CLI response.
 *
 * @package HotelsNL
 * @subpackage Nagios
 */

namespace HotelsNL\Nagios\Response;

/**
 * The class which represents a CLI response.
 */
class CliResponse extends ResponseAbstract
{
    /**
     * The output which is send to STDOUT.
     *
     * @var string $output
     */
    private $output;

    /**
     * Initialize a new CliResponse.
     *
     * @param int $exitStatus
     * @param string $output
     */
    public function __construct($exitStatus, $output)
    {
        parent::__construct($exitStatus);

        $this->setOutput($output);
    }

    /**
     * Execute the response.
     *
     * Send the output and exit the script with the exit code.
     *
     * @return void
     */
    public function execute()
    {
        $this->write($this->getOutput());

        parent::execute();
    }

    /**
     * Get the output which is send to STDOUT.
     *
     * @return string
     * @throws \LogicException When output is not set.
     */
    public function getOutput()
    {
        if (!isset($this->output)) {
            throw new \LogicException('Output is not set.');
        }

        return $this->output;
    }

    /**
     * Set the output which is send to STDOUT.
     *
     * @param string $output
     * @return static
     * @throws \InvalidArgumentException When $output is not of type string.
     */
    private function setOutput($output)
    {
        if (!is_string($output)) {
            throw new \InvalidArgumentException(
                'Invalid output supplied: ' . var_export($output, true)
            );
        }

        $this->output = $output;

        return $this;
    }
}
