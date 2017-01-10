<?php

namespace AppBundle\Services\Capistrano\Wizard;

/**
 * Class GemfileGenerator.
 */
class GemfileGenerator implements WizardGeneratorInterface
{
    /**
     * @var array
     */
    protected $plugins;

    /**
     * @param array $plugins
     */
    public function __construct(array $plugins)
    {
        $this->plugins = $plugins;
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
        $plugins = $this->generatePlugins();

        return <<<EOF
source "https://rubygems.org"

gem "capistrano"
{$plugins}
gem "highline"
EOF;
    }

    /**
     * @return string
     */
    protected function generatePlugins()
    {
        $plugins = '';
        foreach ($this->plugins as $plugin) {
            $plugins .= sprintf("gem \"%s\"\n", str_replace('/', '-', $plugin));
        }

        return $plugins;
    }
}
