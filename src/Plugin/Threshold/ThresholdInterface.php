<?php
/**
 * Represents the interface of a threshold.
 *
 * @package HotelsNL
 * @subpackage Nagios
 */

namespace HotelsNL\Nagios\Plugin\Threshold;

/**
 * The interface of a threshold.
 */
interface ThresholdInterface
{
    /**
     * Whether a value meets a threshold.
     *
     * @param int|float $value
     * @return bool
     */
    public function meetsThreshold($value);

    /**
     * Whether the value is a valid threshold.
     *
     * When a value meets the threshold, it is considered that an alert will be
     * triggered.
     *
     * @param string $threshold
     * @return bool
     */
    public static function isValidThreshold($threshold);
}
