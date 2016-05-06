<?php
/**
 * Represents the threshold for a range.
 *
 * @package HotelsNL
 * @subpackage Nagios
 */

namespace HotelsNL\Nagios\Plugin\Threshold;

/**
 * The class which represents the threshold for a range.
 */
class RangeThreshold implements ThresholdInterface
{
    /**
     * The type which meets a threshold when the value is exclusive of the
     * endpoints.
     *
     * @var int TYPE_EXCLUDING
     */
    const TYPE_EXCLUDING = 1;

    /**
     * The type which meets a threshold when the value is inclusive of the
     * endpoints.
     *
     * @var int TYPE_INCLUDING
     */
    const TYPE_INCLUDING = 2;

    /**
     * The end of the range.
     *
     * @var int $end
     */
    private $end;

    /**
     * The start of the range.
     *
     * @var int $start
     */
    private $start;

    /**
     * The type which specifies whether the value meets the threshold when being
     * inclusive or exclusive of the endpoints.
     *
     * @var int $type
     */
    private $type = self::TYPE_EXCLUDING;

    /**
     * Initialize a new RangeThreshold.
     *
     * @param mixed $range
     */
    public function __construct($range)
    {
        $this->parseRange((string) $range);
    }

    /**
     * Whether a value meets the threshold.
     *
     * When a value meets the threshold, it is considered that an alert will be
     * triggered.
     *
     * @param int|float $value
     * @return bool
     * @throws \InvalidArgumentException When $value is not an int or float.
     */
    public function meetsThreshold($value)
    {
        $value = (float) $value;

        return $this->getType() === static::TYPE_INCLUDING
            ? $value >= $this->getStart() && $value <= $this->getEnd()
            : $value < $this->getStart() || $value > $this->getEnd();
    }

    /**
     * Whether the value is a valid threshold.
     *
     * @param string $threshold
     * @return bool
     */
    public static function isValidThreshold($threshold)
    {
        $pattern = '/^@?(~|\d+)(:(\d+)?)?$/';

        return preg_match($pattern, $threshold) === 1;
    }

    /**
     * Parse a range according to Nagios guidelines.
     *
     * This function populates the start and end of the range.
     *
     * @see https://nagios-plugins.org/doc/guidelines.html
     * @param string $range
     * @return void
     * @throws \InvalidArgumentException When $range is invalid.
     */
    private function parseRange($range)
    {
        if (!static::isValidThreshold($range)) {
            throw new \InvalidArgumentException(
                'Invalid range supplied: ' . var_export($range, true)
            );
        }

        // Invert the type.
        if ($range{0} === '@') {
            $this->setType(static::TYPE_INCLUDING);
            $range = substr($range, 1);
        }

        $rangeSplit = explode(':', $range);

        // Check for the start and end of the range.
        if (count($rangeSplit) === 1) {
            $start = 0;
            $end = $range;
        } else {
            $start = $rangeSplit[0];
            $end = $rangeSplit[1];
        }

        // Translate ~ to min/max.
        if ($start === '~') {
            $start = 0 - PHP_INT_MAX;
        }

        if ($end === '~') {
            $end = PHP_INT_MAX;
        }

        $this->setStart((int) $start);
        $this->setEnd((int) $end);
    }

    /**
     * Get the value of the threshold which will meet the threshold itself.
     *
     * @return int
     */
    public function getThresholdValue()
    {
        return $this->getEnd();
    }

    /**
     * Get the end of the range.
     *
     * @return int
     * @throws \LogicException When end is not set.
     */
    public function getEnd()
    {
        if (!isset($this->end)) {
            throw new \LogicException('End is not set.');
        }

        return $this->end;
    }

    /**
     * Set the end of the range.
     *
     * @param int $end
     * @return RangeThreshold
     * @throws \InvalidArgumentException When $end is not of type int.
     */
    private function setEnd($end)
    {
        if (!is_int($end)) {
            throw new \InvalidArgumentException(
                'Invalid end supplied: ' . var_export($end, true)
            );
        }

        $this->end = $end;

        return $this;
    }

    /**
     * Get the start of the range.
     *
     * @return int
     * @throws \LogicException When start is not set.
     */
    public function getStart()
    {
        if (!isset($this->start)) {
            throw new \LogicException('Start is not set.');
        }

        return $this->start;
    }

    /**
     * Set the start of the range.
     *
     * @param int $start
     * @return RangeThreshold
     * @throws \InvalidArgumentException When $start is not of type int.
     */
    private function setStart($start)
    {
        if (!is_int($start)) {
            throw new \InvalidArgumentException(
                'Invalid start supplied: ' . var_export($start, true)
            );
        }

        $this->start = $start;

        return $this;
    }

    /**
     * Get the type which specifies whether the value meets the threshold when
     * being inclusive or exclusive of the endpoints.
     *
     * @return int
     * @throws \LogicException When type is not set.
     */
    public function getType()
    {
        if (!isset($this->type)) {
            throw new \LogicException('Type is not set.');
        }

        return $this->type;
    }

    /**
     * Set the type which specifies whether the value meets the threshold when
     * being inclusive or exclusive of the endpoints.
     *
     * @param int $type
     * @return RangeThreshold
     * @throws \InvalidArgumentException When $type is not of type int.
     */
    private function setType($type)
    {
        if (!is_int($type)) {
            throw new \InvalidArgumentException(
                'Invalid type supplied: ' . var_export($type, true)
            );
        }

        $this->type = $type;

        return $this;
    }
}
