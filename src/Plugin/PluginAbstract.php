<?php
/**
 * Represents an abstract of a Nagios plugin.
 *
 * @package HotelsNL
 * @subpackage Nagios
 */

namespace HotelsNL\Nagios\Plugin;

use \HotelsNL\Nagios\Response\CliResponse;
use \HotelsNL\Nagios\Response\ResponseInterface;
use \HotelsNL\Nagios\Plugin\Option\Option;

/**
 * The abstract class of a Nagios plugin.
 */
abstract class PluginAbstract implements PluginInterface
{
    /**
     * The minimum verbosity level.
     *
     * @return int VERBOSITY_LEVEL_MINIMUM
     */
    const VERBOSITY_LEVEL_MINIMUM = 0;

    /**
     * The maximum verbosity level.
     *
     * @return int VERBOSITY_LEVEL_MAXIMUM
     */
    const VERBOSITY_LEVEL_MAXIMUM = 3;

    /**
     * The options registered for this plugin.
     *
     * @var Option[] $options
     */
    private $options = array();

    /**
     * The aliases for registered options.
     *
     * @var string[] $optionAliases
     */
    private $optionAliases = array();

    /**
     * The level of verbosity, range 0 - 3.
     *
     * @var int $verbosityLevel
     */
    private $verbosityLevel = 0;

    /**
     * Initialize a new PluginAbstract.
     */
    final public function __construct()
    {
        $this->registerOptions();
    }

    /**
     * Execute the Nagios plugin and return a result.
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->parseOptions();

        $this->handleOptions();

        if ($this->getOption('help')->getValue() !== false) {
            $rv = new CliResponse(
                ResponseInterface::EXIT_STATUS_SUCCESS,
                $this->constructHelpDocumentation()
            );
        } elseif ($this->getOption('version')->getValue() !== false) {
            $rv = new CliResponse(
                ResponseInterface::EXIT_STATUS_SUCCESS,
                $this->constructVersionInformation()
            );
        } else {
            $pluginResponse = $this->executePlugin();

            if (!($pluginResponse instanceof ResponseInterface)) {
                throw new \RuntimeException(
                    'Expected the plugin to return a ResponseInterface, got: '
                    . get_class($pluginResponse)
                );
            }

            $rv = $pluginResponse;
        }

        return $rv;
    }

    /**
     * Execute the plugin, returns the response of the check.
     *
     * @return ResponseInterface
     */
    abstract protected function executePlugin();

    /**
     * Handle the options internally.
     */
    private function handleOptions()
    {
        // Handle verbosity.
        $verboseOption = $this->getOption('verbose');

        if (is_int($verboseOption->getValue())) {
            $this->setVerbosityLevel($verboseOption->getValue());
        }

        //
    }

    /**
     * Parse the command line options.
     *
     * @return void
     */
    private function parseOptions()
    {
        $shortOptions = '';
        $longOptions = array();

        foreach ($this->getOptions() as $option) {
            $shortOptions .= $option->getShortOption();
            $longOption = $option->getLongOption();

            if (!empty($longOption)) {
                array_push($longOptions, $longOption);
            }
        }

        foreach (getopt($shortOptions, $longOptions) as $option => $value) {
            $option = $this->getOption($option);

            if (is_array($value)) {
                $value = count($value);
            } elseif ($option->getMode() === Option::MODE_NO_VALUE) {
                $value = 1;
            }

            $option->setValue($value);
        }
    }

    /**
     * Construct the help documentation.
     *
     * @return string
     */
    private function constructHelpDocumentation()
    {
        $options = array();

        foreach ($this->getOptions() as $option) {
            $shortOption = $option->getShortOption();
            $longOption = $option->getLongOption();

            $parameters = "-{$shortOption}";

            if (!empty($longOption)) {
                $parameters .= " | --{$longOption}";
            }

            $options[$parameters] = $option->getDescription();
        }

        $rv = $this->constructPluginHelpDocumentation() . "\n";
        $rv .= "\n";
        $rv .= "OPTIONS:\n";

        $optionLength = max(array_map('strlen', array_keys($options)));

        // Print the options.
        foreach ($options as $parameters => $description) {
            $spaceFill = str_repeat(' ', $optionLength + 3 - strlen($parameters));

            $rv .= "{$parameters}{$spaceFill}{$description}\n";
        }

        return $rv;
    }

    /**
     * Construct the help documentation for the plugin.
     *
     * MUST contains a brief explanation about the plugin for the help section.
     *
     * @return string
     */
    abstract protected function constructPluginHelpDocumentation();

    /**
     * Get the help documentation.
     *
     * @return string
     */
    public function getHelp()
    {
        return $this->constructHelpDocumentation();
    }

    /**
     * Construct the information about the version of this plugin.
     *
     * @return string
     */
    private function constructVersionInformation()
    {
        $scriptName = array_pop(explode('/', $_SERVER['SCRIPT_FILENAME']));

        return "{$scriptName}, version: {$this->getVersion()}\n";
    }

    /**
     * Register an option.
     *
     * @param string $shortOption
     * @param string $description
     * @param int $mode
     * @param string $longOption
     * @return static
     * @throws \InvalidArgumentException When $shortOption is already registered.
     * @throws \InvalidArgumentException When $longOption is already registered.
     */
    protected function registerOption(
        $shortOption,
        $description,
        $mode,
        $longOption = ''
    ) {
        if (array_key_exists($shortOption, $this->getOptions())) {
            throw new \InvalidArgumentException(
                "Short option '{$shortOption}' is already registered."
            );
        } elseif (array_key_exists($longOption, $this->getOptionAliases())) {
            throw new \InvalidArgumentException(
                "Long option '{$longOption}' is already registered."
            );
        }

        $this->options[$shortOption] = new Option(
            $shortOption,
            $description,
            $mode,
            $longOption
        );

        if (!empty($longOption)) {
            $this->registerOptionAlias($longOption, $shortOption);
        }

        return $this;
    }

    /**
     * Register the alias of an option.
     *
     * @param string $alias
     * @param string $shortOption
     * @throws \InvalidArgumentException When $alias is empty.
     * @throws \InvalidArgumentException When $alias is already registered.
     * @throws \InvalidArgumentException When $shortOption is not registered.
     * @return static
     */
    private function registerOptionAlias($alias, $shortOption)
    {
        if (empty($alias)) {
            throw new \InvalidArgumentException(
                'Unable to register option alias, empty alias supplied.'
            );
        } elseif (array_key_exists($alias, $this->getOptionAliases())) {
            throw new \InvalidArgumentException(
                'Unable to register option alias, alias is already registered.'
            );
        } elseif (!array_key_exists($shortOption, $this->getOptions())) {
            throw new \InvalidArgumentException(
                'Unable to register option alias, short option does not exist.'
            );
        }

        $this->optionAliases[$alias] = $shortOption;

        return $this;
    }

    /**
     * Register the options for the plugin.
     *
     * @return static
     */
    private function registerOptions()
    {
        // Version.
        $this->registerOption(
            'V',
            'Get the version of this plugin.',
            Option::MODE_NO_VALUE,
            'version'
        );

        // Help.
        $this->registerOption(
            'h',
            'Get the help documentation of this plugin.',
            Option::MODE_NO_VALUE,
            'help'
        );

        // Timeout.
        $this->registerOption(
            't',
            'The timeout which this plugin uses for calls.',
            Option::MODE_REQUIRE_VALUE,
            'timeout'
        );

        // Warning.
        $this->registerOption(
            'w',
            'Override the default threshold for warnings.',
            Option::MODE_REQUIRE_VALUE,
            'warning'
        );

        // Critical.
        $this->registerOption(
            'c',
            'Override the default threshold for criticals.',
            Option::MODE_REQUIRE_VALUE,
            'critical'
        );

        // Hostname.
        $this->registerOption(
            'H',
            'The hostname which the plugin performs checks for.',
            Option::MODE_REQUIRE_VALUE,
            'hostname'
        );

        // Verbosity.
        $this->registerOption(
            'v',
            'The verbosity level (max -vvvv).',
            Option::MODE_NO_VALUE,
            'verbose'
        );

        $this->registerPluginOptions();

        return $this;
    }

    /**
     * Register the options of the plugin itself.
     *
     * This function is used in the concrete plugin to register options specific
     * for the plugin by using registerOption().
     *
     * @return void
     */
    abstract protected function registerPluginOptions();

    /**
     * Get an option by its short name or long name.
     *
     * @param string $option
     * @return Option
     * @throws \InvalidArgumentException When $option does not exist.
     */
    protected function getOption($option)
    {
        $shortOption = $option;

        // Long option supplied, get the short option.
        if (strlen($option) > 1) {
            $aliases = $this->getOptionAliases();

            if (array_key_exists($option, $aliases)) {
                $shortOption = $aliases[$option];
            }
        }

        $options = $this->getOptions();

        if (!array_key_exists($shortOption, $options)) {
            throw new \InvalidArgumentException(
                'Unable to get option, unregistered option supplied: '
                . var_export($option, true)
            );
        }

        return $options[$shortOption];
    }

    /**
     * Get the options registered for this plugin.
     *
     * @return Option[]
     * @throws \LogicException When options is not set.
     */
    private function getOptions()
    {
        if (!isset($this->options)) {
            throw new \LogicException('Options is not set.');
        }

        return $this->options;
    }

    /**
     * Set the options registered for this plugin.
     *
     * @param Option[] $options
     * @return static
     * @throws \InvalidArgumentException When $option is not an instance of
     *  Option.
     */
    private function setOptions(array $options)
    {
        foreach ($options as $option) {
            if (!($option instanceof Option)) {
                throw new \InvalidArgumentException(
                    'Expected option to be an instance of Option, got: '
                    . var_export($option, true)
                );
            }
        }

        $this->options = $options;

        return $this;
    }

    /**
     * Get the aliases for registered options.
     *
     * @return string[]
     * @throws \LogicException When optionAliases is not set.
     */
    private function getOptionAliases()
    {
        if (!isset($this->optionAliases)) {
            throw new \LogicException('OptionAliases is not set.');
        }

        return $this->optionAliases;
    }

    /**
     * Set the aliases for registered options.
     *
     * @param string[] $optionAliases
     * @return static
     */
    private function setOptionAliases(array $optionAliases)
    {
        $this->optionAliases = $optionAliases;

        return $this;
    }

    /**
     * Get the level of verbosity.
     *
     * @return int
     * @throws \LogicException When verbosityLevel is not set.
     */
    protected function getVerbosityLevel()
    {
        if (!isset($this->verbosityLevel)) {
            throw new \LogicException('VerbosityLevel is not set.');
        }

        return $this->verbosityLevel;
    }

    /**
     * Set the level of verbosity.
     *
     * @param int $verbosityLevel
     * @return static
     * @throws \InvalidArgumentException When $verbosityLevel is invalid.
     */
    private function setVerbosityLevel($verbosityLevel)
    {
        $range = range(
            static::VERBOSITY_LEVEL_MINIMUM,
            static::VERBOSITY_LEVEL_MAXIMUM
        );

        if (!is_int($verbosityLevel) || !in_array($verbosityLevel, $range)) {
            throw new \InvalidArgumentException(
                'Invalid verbosityLevel supplied: '
                . var_export($verbosityLevel, true)
            );
        }

        $this->verbosityLevel = $verbosityLevel;

        return $this;
    }
}
