<?php
/**
 * Represents an option which can be supplied through the command line.
 *
 * @package HotelsNL
 * @subpackage Nagios
 */

namespace HotelsNL\Nagios\Plugin\Option;

/**
 * The class which represents an option which can be supplied through the
 * command line.
 */
class Option
{
    /**
     * The mode for an option which does not accept a value.
     *
     * @var int MODE_NO_VALUE
     */
    const MODE_NO_VALUE = 1;

    /**
     * The mode for an option which requires a value.
     *
     * @var int MODE_REQUIRE_VALUE
     */
    const MODE_REQUIRE_VALUE = 2;

    /**
     * The mode for an option which accepts an optional value.
     *
     * @var int MODE_OPTIONAL_VALUE
     */
    const MODE_OPTIONAL_VALUE = 3;

    /**
     * The description of the option.
     *
     * @var string $description
     */
    private $description;

    /**
     * The name of the option for long option format.
     *
     * @var string $longOption
     */
    private $longOption;

    /**
     * The mode of the option.
     *
     * @var int $mode
     */
    private $mode;

    /**
     * The character for the short option.
     *
     * @var string $shortOption
     */
    private $shortOption;

    /**
     * The value of the option.
     *
     * @var mixed $value
     */
    private $value = false;

    /**
     * Initialize a new Option.
     *
     * @param string $shortOption
     * @param string $description
     * @param int $mode
     * @param string $longOption
     */
    public function __construct($shortOption, $description, $mode,  $longOption = '')
    {
        $this->setShortOption($shortOption);
        $this->setDescription($description);
        $this->setMode($mode);
        $this->setLongOption($longOption);
    }

    /**
     * Get the description of the option.
     *
     * @return string
     * @throws \LogicException When description is not set.
     */
    public function getDescription()
    {
        if (!isset($this->description)) {
            throw new \LogicException('Description is not set.');
        }

        return $this->description;
    }

    /**
     * Set the description of the option.
     *
     * @param string $description
     * @return Option
     * @throws \InvalidArgumentException When $description is not of type string.
     */
    private function setDescription($description)
    {
        if (!is_string($description)) {
            throw new \InvalidArgumentException(
                'Invalid description supplied: ' . var_export($description, true)
            );
        }

        $this->description = $description;

        return $this;
    }

    /**
     * Get the name of the option for long option format.
     *
     * @return string
     * @throws \LogicException When longOption is not set.
     */
    public function getLongOption()
    {
        if (!isset($this->longOption)) {
            throw new \LogicException('LongOption is not set.');
        }

        return $this->longOption;
    }

    /**
     * Set the name of the option for long option format.
     *
     * @param string $longOption
     * @return Option
     * @throws \InvalidArgumentException When $longOption is not of type string.
     */
    private function setLongOption($longOption)
    {
        if (!is_string($longOption)) {
            throw new \InvalidArgumentException(
                'Invalid longOption supplied: ' . var_export($longOption, true)
            );
        }

        $this->longOption = $longOption;

        return $this;
    }

    /**
     * Get the mode of the option.
     *
     * @return int
     * @throws \LogicException When mode is not set.
     */
    public function getMode()
    {
        if (!isset($this->mode)) {
            throw new \LogicException('Mode is not set.');
        }

        return $this->mode;
    }

    /**
     * Set the mode of the option.
     *
     * @param int $mode
     * @return Option
     * @throws \InvalidArgumentException When $mode is invalid.
     */
    private function setMode($mode)
    {
        $allowedValues = range(
            static::MODE_NO_VALUE,
            static::MODE_OPTIONAL_VALUE
        );

        if (!is_int($mode) || !in_array($mode, $allowedValues)) {
            throw new \InvalidArgumentException(
                'Invalid mode supplied: ' . var_export($mode, true)
            );
        }

        $this->mode = $mode;

        return $this;
    }

    /**
     * Get the character for the short option.
     *
     * @return string
     * @throws \LogicException When shortOption is not set.
     */
    public function getShortOption()
    {
        if (!isset($this->shortOption)) {
            throw new \LogicException('ShortOption is not set.');
        }

        return $this->shortOption;
    }

    /**
     * Set the character for the short option.
     *
     * @param string $shortOption
     * @return Option
     * @throws \InvalidArgumentException When $shortOption is not of type string.
     * @throws \InvalidArgumentException When $shortOption has an invalid length.
     */
    private function setShortOption($shortOption)
    {
        if (!is_string($shortOption)) {
            throw new \InvalidArgumentException(
                'Invalid shortOption supplied: ' . export($shortOption, true)
            );
        } elseif (strlen($shortOption) !== 1) {
            throw new \InvalidArgumentException(
                'Length of the short option must be 1, supplied: '
                . var_export($shortOption, true)
            );
        }

        $this->shortOption = $shortOption;

        return $this;
    }

    /**
     * Get the value of the option.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value of the option.
     *
     * @param mixed $value
     * @return Option
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
