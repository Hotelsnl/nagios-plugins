<?php
/**
 * Represents the performance data registered during a check.
 *
 * @package HotelsNL
 * @subpackage Nagios
 */

namespace HotelsNL\Nagios\Response\Diagnostic\PerformanceData;

/**
 * The class which represents the performance data registered during a check.
 */
class PerformanceData
{
    /**
     * The lines of performance data.
     *
     * @var PerformanceDataLine[] $lines
     */
    private $lines = array();

    /**
     * Get the lines of performance data.
     *
     * @return PerformanceDataLine[]
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * Add a performance data line.
     *
     * @param PerformanceDataLine $line
     * @return PerformanceData
     */
    public function addLine(PerformanceDataLine $line)
    {
        array_push($this->lines, $line);

        return $this;
    }

    /**
     * Set the lines of performance data.
     *
     * @param PerformanceDataLine[] $lines
     * @return PerformanceData
     * @throws \InvalidArgumentException When a line is not of type
     *  PerformanceDataLine.
     */
    public function setLines(array $lines)
    {
        foreach ($lines as $line) {
            if (!($line instanceof PerformanceDataLine)) {
                throw new \InvalidArgumentException(
                    'Expected the line to be an instanceof PerformanceDataLine, '
                    . 'got: ' . get_class($line)
                );
            }
        }
    }
}
