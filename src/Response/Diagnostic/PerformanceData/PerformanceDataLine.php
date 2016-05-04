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
     * The value for 'unknown'.
     *
     * @var string VALUE_UNKNOWN
     */
    const VALUE_UNKNOWN = 'U';

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
     * MUST be a float, int or 'U' (string) for unknown.
     *
     * @var float|int|string $value
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
        if ($valueWithUnit === static::VALUE_UNKNOWN) {
            $this->setValue(static::VALUE_UNKNOWN);
            $this->setUnitOfMeasurement('');
        } else {
            $pattern = '/^\s*([-0-9.]+)\s*([A-Z%]+)?\s*$/i';

            if (preg_match($pattern, $valueWithUnit, $matches) !== 1) {
                throw new \InvalidArgumentException(
                    'Invalid value with unit of measurement supplied: '
                    . var_export($valueWithUnit, true)
                );
            }

            $value = $matches[1];

            // Set the value.
            if (strpos($value, '.') >= 0) {
                $this->setValue((float)$value);
            } else {
                $this->setValue((int)$value);
            }

            // Set the unit of measurement.
            if (count($matches) === 3) {
                $uom = $matches[2];

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

                $this->setUnitOfMeasurement($uom);
            } else {
                $this->setUnitOfMeasurement('');
            }
        }
    }

    /**
     * Create a text version of the performance data line.
     *
     * @return string
     */
    public function toText()
    {
        $label = $this->getLabel();
        $value = $this->getValue();
        $uom = $this->getUnitOfMeasurement();
        $warning = $this->getWarning();
        $critical = $this->getCritical();
        $min = $this->getMinimum();
        $max = $this->getMaximum();

        // Double single quote to escape single quote.
        $label = str_replace("'", "''", $label);

        $warning = ($warning === null) ? 'null' : $warning;
        $critical = ($critical === null) ? 'null' : $critical;

        // Not necessary for percentages to supply a min and max.
        $min = ($uom === '%' || $uom === '') ? static::VALUE_UNKNOWN : $min;
        $max = ($uom === '%' || $uom === '') ? static::VALUE_UNKNOWN : $max;

        return "'{$label}'={$value}{$uom};{$warning};{$critical};{$min};{$max}";
    }

    /**
     * Magic method which returns the text version when the object is being cast
     * to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toText();
    }

    /**
     * Get the allowed units of measurement.
     *
     * @return string[]
     */
    private static function getAllowedUnitsOfMeasurement()
    {
        return array('s', 'us', 'ms', '%', 'KB', 'MB', 'TB', 'c');
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
        $pattern = '/^[^=]+$/';

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
     * @return float|int|string
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
     * @param float|int|string $value
     * @return PerformanceDataLine
     * @throws \InvalidArgumentException When $value is invalid.
     */
    private function setValue($value)
    {
        if (!is_float($value)
            && !is_int($value)
            && $value !== static::VALUE_UNKNOWN
        ) {
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
