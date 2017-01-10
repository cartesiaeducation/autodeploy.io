<?php

namespace AppBundle\Services\Capistrano\Wizard;

use AppBundle\Form\Model\Capistrano\Setup;

/**
 * Class CapfileGenerator.
 */
class CapfileGenerator implements WizardGeneratorInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @param Setup $options
     */
    public function __construct(Setup $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        return $this->getTemplate();
    }

    /**
     * @return string
     */
    protected function getTemplate()
    {
        $directory = trim($this->options->directory);
        if ('/' === mb_substr($this->options->directory, mb_strlen($this->options->directory) - 1)) {
            $directory = mb_substr($this->options->directory, 0, -1);
        }

        $plugins = $this->generatePlugins();

        return <<<EOF
set :deploy_config_path, "{$directory}/config/deploy.rb"
set :stage_config_path, "{$directory}/config/deploy/"

# Load DSL and set up stages
require 'capistrano/setup'

# Include default deployment tasks
require 'capistrano/deploy'
{$plugins}

Dir.glob('{$directory}/tasks/*.cap').each { |r| import r }
EOF;
    }

    /**
     * @return string
     */
    protected function generatePlugins()
    {
        $plugins = '';
        foreach ($this->options->plugins as $plugin) {
            $plugins .= sprintf("require '%s'\n", $plugin);
        }

        return $plugins;
    }
}
