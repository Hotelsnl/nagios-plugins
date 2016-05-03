<?php
/**
 * Represents the threshold for a percentage value.
 *
 * @package HotelsNL
 * @subpackage Nagios
 */

namespace HotelsNL\Nagios\Plugin\Threshold;

/**
 * The class which represents the threshold for a percentage value.
 */
class PercentageThreshold implements ThresholdInterface
{
    /**
     * The percentage which the threshold holds.
     *
     * @var int $percentage
     */
    private $percentage;

    /**
     * Initialize a new PercentageThreshold.
     *
     * @param string|int $percentage
     */
    public function __construct($percentage)
    {
        $this->parsePercentage($percentage);
    }

    /**
     * Whether a value meets a threshold.
     *
     * @param int|float $value
     * @return bool
     */
    public function meetsThreshold($value)
    {
        return (float) $value > $this->getPercentage();
    }

    /**
     * Whether the value is a valid threshold.
     *
     * When a value meets the threshold, it is considered that an alert will be
     * triggered.
     *
     * @param string $threshold
     * @return bool
     */
    public static function isValidThreshold($threshold)
    {
        $pattern = '/^(100|[0-9]{1,2})%$/';

        return preg_match($pattern, $threshold) === 1;
    }

    /**
     * Parse a percentage.
     *
     * This function populates the percentage property.
     *
     * @param string|int $percentage
     * @return void
     * @throws \InvalidArgumentException When $percentage is invalid.
     */
    private function parsePercentage($percentage)
    {
        if (!static::isValidThreshold($percentage)) {
            throw new \InvalidArgumentException(
                'Invalid percentage supplied: ' . var_export($percentage, true)
            );
        }

        $percentage = rtrim($percentage, '%');

        $this->setPercentage((int) $percentage);
    }

    /**
     * Get the percentage which the threshold holds.
     *
     * @return int
     * @throws \LogicException When percentage is not set.
     */
    public function getPercentage()
    {
        if (!isset($this->percentage)) {
            throw new \LogicException('Percentage is not set.');
        }

        return $this->percentage;
    }

    /**
     * Set the percentage which the threshold holds.
     *
     * @param int $percentage
     * @return PercentageThreshold
     * @throws \InvalidArgumentException When $percentage is not of type int.
     */
    private function setPercentage($percentage)
    {
        if (!is_int($percentage)) {
            throw new \InvalidArgumentException(
                'Invalid percentage supplied: ' . var_export($percentage, true)
            );
        }

        $this->percentage = $percentage;

        return $this;
    }
}
