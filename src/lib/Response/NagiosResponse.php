<?php
/**
 * Represents the response for Nagios.
 *
 * @package HotelsNL
 * @subpackage Nagios
 */

namespace HotelsNL\Nagios\Response;

use \HotelsNL\Nagios\Response\Diagnostic\Diagnostic;

/**
 * The class which represents the response for Nagios.
 */
class NagiosResponse extends ResponseAbstract
{
    /**
     * The diagnostic information which is being output.
     *
     * @var Diagnostic $diagnostic
     */
    private $diagnostic;

    /**
     * Create a new NagiosResponse from a state.
     *
     * @param State $state
     * @param Diagnostic $diagnostic
     * @return static
     */
    public static function fromState(State $state, Diagnostic $diagnostic)
    {
        $response = new static($state->getCode());
        $response->setDiagnostic($diagnostic);

        return $response;
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
        $diagnostic = $this->getDiagnostic();
        $serviceStatus = $diagnostic->getServiceStatus();
        $performanceData = $diagnostic->getPerformanceData();

        $dataLines = $performanceData->getLines();

        // Get the first part of the performance data.
        if (count($dataLines) > 0) {
            $firstDataLine = array_shift($dataLines);
            $serviceData= " | {$firstDataLine}";
        } else {
            $serviceData = '';
        }

        // Construct the output.
        $output = "{$serviceStatus}{$serviceData}\n";
        $output .= "{$diagnostic->getLongServiceOutput()}";

        // Add additional performance data.
        if (count($dataLines) > 0) {
            $output .= ' | ';

            foreach ($dataLines as $line) {
                $output .= "{$line}\n";
            }
        }

        $this->write($output);

        parent::execute();
    }

    /**
     * Get the diagnostic information which is being output.
     *
     * @return Diagnostic
     * @throws \LogicException When diagnostic is not set.
     */
    public function getDiagnostic()
    {
        if (!isset($this->diagnostic)) {
            throw new \LogicException('Diagnostic is not set.');
        }

        return $this->diagnostic;
    }

    /**
     * Set the diagnostic information which is being output.
     *
     * @param Diagnostic $diagnostic
     * @return NagiosResponse
     */
    public function setDiagnostic(Diagnostic $diagnostic)
    {
        $this->diagnostic = $diagnostic;

        return $this;
    }
}
