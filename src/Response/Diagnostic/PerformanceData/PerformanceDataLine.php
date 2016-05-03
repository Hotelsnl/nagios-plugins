<?php
/**
 * Represents a line of performance data.
 *
 * @package HotelsNL
 * @subpackage Nagios
 */

namespace HotelsNL\Nagios\Response\Diagnostic\PerformanceData;

/**
 * The class whichi represents a line of performance data.
 */
class PerformanceDataLine
{
    /**
     * The unit of measurement for 'unknown'.
     *
     * @var string UOM_UNKNOWN
     */
    const UOM_UNKNOWN = 'U';

    /**
     * The threshold value considered as critical (without unit of measurement).
     *
     * @var null|int|float $critical
     */
    private $critical;

    /**
     * The label of the performance data line.
     *
     * @var string $label
     */
    private $label;

    /**
     * The maximum value possible.
     *
     * @var float|int $maximum
     */
    private $maximum;

    /**
     * The minimum value possible.
     *
     * @var float|int $minimum
     */
    private $minimum;

    /**
     * The unit of measurement of the values.
     *
     * @var string $unitOfMeasurement
     */
    private $unitOfMeasurement;

    /**
     * The value of the measured performance data line.
     *
     * @var float|int $value
     */
    private $value;

    /**
     * The threshold value considered as warning (without unit of measurement).
     *
     * @var null|int|float $warning
     */
    private $warning;

    /**
     * Initialize a new PerformanceDataLine.
     *
     * @param string $label
     * @param string $valueWithUnit
     * @param float|int $minimum (optional for percentage values)
     * @param float|int $maximum (optional for percentage values)
     */
    public function __construct($label, $valueWithUnit, $minimum = 0, $maximum = 100)
    {
        $this->setLabel($label);
        $this->parseValueWithUnit($valueWithUnit);

        $this->setMinimum($minimum);
        $this->setMaximum($maximum);
    }

    /**
     * Parse a value with unit of measurement.
     *
     * @param string $valueWithUnit
     * @return void
     * @throws \InvalidArgumentException When $value is invalid.
     * @throws \InvalidArgumentException When the unit of measurement is not
     *  allowed.
     */
    private function parseValueWithUnit($valueWithUnit)
    {
        $pattern = '/^\s*([-0-9.]+)\s*([A-Z%]+)?\s*$/i';

        if (!preg_match($pattern, $valueWithUnit, $matches)) {
            throw new \InvalidArgumentException(
                'Invalid value with unit of measurement supplied: '
                . var_export($valueWithUnit, true)
            );
        }

        $value = $matches[0];

        // Set the value.
        if (strpos($value, '.') >= 0) {
            $this->setValue((float) $value);
        } else {
            $this->setValue((int) $value);
        }

        // Set the unit of measurement.
        if (count($matches) === 2) {
            $uom = $matches[1];

            $allowedUnits = array_map(
                'strtolower',
                static::getAllowedUnitsOfMeasurement()
            );

            if (!in_array(strtolower($uom), $allowedUnits, true)) {
                throw new \InvalidArgumentException(
                    'Invalid unit of measurement supplied: '
                    . var_export($uom, true)
                );
            }
        } else {
            $this->setUnitOfMeasurement(static::UOM_UNKNOWN);
        }
    }

    /**
     * Get the allowed units of measurement.
     *
     * @return string[]
     */
    private static function getAllowedUnitsOfMeasurement()
    {
        return array(
            static::UOM_UNKNOWN, 's', 'us', 'ms', '%', 'KB', 'MB', 'TB', 'c'
        );
    }

    /**
     * Get the threshold value considered as critical (without unit of
     * measurement).
     *
     * @return null|int|float
     */
    public function getCritical()
    {
        return $this->critical;
    }

    /**
     * Set the threshold value considered as critical (without unit of
     * measurement).
     *
     * @param null|int|float $critical
     * @return PerformanceDataLine
     * @throws \InvalidArgumentException When $critical is invalid.
     */
    public function setCritical($critical)
    {
        if (!is_float($critical) && !is_int($critical) && $critical !== null) {
            throw new \InvalidArgumentException(
                'Invalid critical supplied: ' . var_export($critical, true)
            );
        }

        $this->critical = $critical;

        return $this;
    }

    /**
     * Get the label of the performance data line.
     *
     * @return string
     * @throws \LogicException When label is not set.
     */
    public function getLabel()
    {
        if (!isset($this->label)) {
            throw new \LogicException('Label is not set.');
        }

        return $this->label;
    }

    /**
     * Set the label of the performance data line.
     *
     * @param string $label
     * @return PerformanceDataLine
     * @throws \InvalidArgumentException When $label is invalid.
     */
    private function setLabel($label)
    {
        $pattern = '/^[^\'=]+$/';

        if (!is_string($label) || preg_match($pattern, $label) !== 1) {
            throw new \InvalidArgumentException(
                'Invalid label supplied: ' . var_export($label, true)
            );
        }

        $this->label = $label;

        return $this;
    }

    /**
     * Get the maximum value possible.
     *
     * @return float|int
     * @throws \LogicException When maximum is not set.
     */
    public function getMaximum()
    {
        if (!isset($this->maximum)) {
            throw new \LogicException('Maximum is not set.');
        }

        return $this->maximum;
    }

    /**
     * Set the maximum value possible.
     *
     * @param float|int $maximum
     * @return PerformanceDataLine
     * @throws \InvalidArgumentException When $maximum is invalid.
     */
    private function setMaximum($maximum)
    {
        if (!is_float($maximum) && !is_int($maximum)) {
            throw new \InvalidArgumentException(
                'Invalid maximum supplied: ' . var_export($maximum)
            );
        }

        $this->maximum = $maximum;

        return $this;
    }

    /**
     * Get the minimum value possible.
     *
     * @return float|int
     * @throws \LogicException When minimum is not set.
     */
    public function getMinimum()
    {
        if (!isset($this->minimum)) {
            throw new \LogicException('Minimum is not set.');
        }

        return $this->minimum;
    }

    /**
     * Set the minimum value possible.
     *
     * @param float|int $minimum
     * @return PerformanceDataLine
     * @throws \InvalidArgumentException When $minimum is invalid.
     */
    private function setMinimum($minimum)
    {
        if (!is_float($minimum) && !is_int($minimum)) {
            throw new \InvalidArgumentException(
                'Invalid minimum supplied: ' . var_export($minimum)
            );
        }

        $this->minimum = $minimum;

        return $this;
    }

    /**
     * Get the unit of measurement of the values.
     *
     * @return string
     * @throws \LogicException When unitOfMeasurement is not set.
     */
    public function getUnitOfMeasurement()
    {
        if (!isset($this->unitOfMeasurement)) {
            throw new \LogicException('UnitOfMeasurement is not set.');
        }

        return $this->unitOfMeasurement;
    }

    /**
     * Set the unit of measurement of the values.
     *
     * @param string $unitOfMeasurement
     * @return PerformanceDataLine
     * @throws \InvalidArgumentException When $unitOfMeasurement is not of type
     *  string.
     */
    private function setUnitOfMeasurement($unitOfMeasurement)
    {
        if (!is_string($unitOfMeasurement)) {
            throw new \InvalidArgumentException(
                'Invalid unitOfMeasurement supplied: '
                . var_export($unitOfMeasurement, true)
            );
        }

        $this->unitOfMeasurement = $unitOfMeasurement;

        return $this;
    }

    /**
     * Get the value of the measured performance data line.
     *
     * @return float|int
     * @throws \LogicException When value is not set.
     */
    public function getValue()
    {
        if (!isset($this->value)) {
            throw new \LogicException('Value is not set.');
        }

        return $this->value;
    }

    /**
     * Set the value of the measured performance data line.
     *
     * @param float|int $value
     * @return PerformanceDataLine
     * @throws \InvalidArgumentException When $value is invalid.
     */
    private function setValue($value)
    {
        if (!is_float($value) && !is_int($value)) {
            throw new \InvalidArgumentException(
                'Invalid value supplied, got: ' . var_export($value, true)
            );
        }

        $this->value = $value;

        return $this;
    }

    /**
     * Get the threshold value considered as warning (without unit of
     * measurement).
     *
     * @return null|int|float
     */
    public function getWarning()
    {
        return $this->warning;
    }

    /**
     * Set the threshold value considered as warning (without unit of
     * measurement).
     *
     * @param null|int|float $warning
     * @return PerformanceDataLine
     * @throws \InvalidArgumentException When $warning is invalid.
     */
    public function setWarning($warning)
    {
        if (!is_float($warning) && !is_int($warning) && $warning !== null) {
            throw new \InvalidArgumentException(
                'Invalid warning supplied: ' . var_export($warning, true)
            );
        }

        $this->warning = $warning;

        return $this;
    }
}
