<?php
/**
 * Represents the diagnostic entity which detailed information about the
 * performed Nagios check.
 *
 * @package HotelsNL
 * @subpackage Nagios
 */

namespace HotelsNL\Nagios\Response\Diagnostic;

use \HotelsNL\Nagios\Response\Diagnostic\PerformanceData\PerformanceData;

/**
 * The class which represents the diagnostic entity which detailed information
 * about the performed Nagios check.
 */
class Diagnostic
{
    /**
     * The long service output which is mainly a block of additional information.
     *
     * @var string $longServiceOutput
     */
    private $longServiceOutput = '';

    /**
     * The performance data registered during a check.
     *
     * @var PerformanceData $performanceData
     */
    private $performanceData;

    /**
     * The service status containing brief information about the check.
     *
     * @var string $serviceStatus
     */
    private $serviceStatus;

    /**
     * Initialize a new Diagnostic.
     *
     * @param string $serviceStatus
     * @param string $longServiceOutput (optional)
     */
    public function __construct($serviceStatus, $longServiceOutput = '')
    {
        $this->setServiceStatus($serviceStatus);
        $this->setPerformanceData(new PerformanceData());

        if (!empty($longServiceOutput)) {
            $this->setLongServiceOutput($longServiceOutput);
        }
    }

    /**
     * Get the long service output which is mainly a block of additional
     * information.
     *
     * @return string
     */
    public function getLongServiceOutput()
    {
        return $this->longServiceOutput;
    }

    /**
     * Set the long service output which is mainly a block of additional
     * information.
     *
     * @param string $longServiceOutput
     * @return Diagnostic
     * @throws \InvalidArgumentException When $longServiceOutput is not of type
     *  string.
     */
    public function setLongServiceOutput($longServiceOutput)
    {
        if (!is_string($longServiceOutput)) {
            throw new \InvalidArgumentException(
                'Invalid longServiceOutput supplied: '
                . var_export($longServiceOutput, true)
            );
        }

        $this->longServiceOutput = $longServiceOutput;

        return $this;
    }

    /**
     * Get the performance data registered during a check.
     *
     * @return PerformanceData
     */
    public function getPerformanceData()
    {
        return $this->performanceData;
    }

    /**
     * Set the performance data registered during a check.
     *
     * @param null|PerformanceData $performanceData
     * @return Diagnostic
     */
    private function setPerformanceData(PerformanceData $performanceData = null)
    {
        $this->performanceData = $performanceData;

        return $this;
    }

    /**
     * Get the service status containing brief information about the check.
     *
     * @return string
     * @throws \LogicException When serviceStatus is not set.
     */
    public function getServiceStatus()
    {
        if (!isset($this->serviceStatus)) {
            throw new \LogicException('ServiceStatus is not set.');
        }

        return $this->serviceStatus;
    }

    /**
     * Set the service status containing brief information about the check.
     *
     * @param string $serviceStatus
     * @return Diagnostic
     * @throws \InvalidArgumentException When $serviceStatus is not of type
     *  string.
     */
    private function setServiceStatus($serviceStatus)
    {
        if (!is_string($serviceStatus)) {
            throw new \InvalidArgumentException(
                'Invalid serviceStatus supplied: '
                . var_export($serviceStatus, true)
            );
        }

        $this->serviceStatus = $serviceStatus;

        return $this;
    }
}
