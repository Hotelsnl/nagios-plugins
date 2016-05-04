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
     * Add a performance data line conveniently.
     *
     * @param string $label
     * @param int|float|string $valueWithUnit
     * @param null|int|float $warning
     * @param null|int|float $critical
     * @param int|float $minimum
     * @param int|float $maximum
     * @return PerformanceData
     */
    public function add(
        $label,
        $valueWithUnit,
        $warning = null,
        $critical = null,
        $minimum = 0,
        $maximum = 100
    ) {
        $line = new PerformanceDataLine($label, $valueWithUnit, $minimum, $maximum);
        $line->setWarning($warning);
        $line->setCritical($critical);

        $this->addLine($line);

        return $this;
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
