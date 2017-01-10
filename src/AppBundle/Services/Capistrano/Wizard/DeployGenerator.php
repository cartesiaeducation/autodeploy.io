<?php

namespace AppBundle\Services\Capistrano\Wizard;

use AppBundle\Form\Model\Capistrano\Wizard;
use Gedmo\Sluggable\Util\Urlizer;

/**
 * Class DeployGenerator.
 */
class DeployGenerator extends AbstractGenerator implements WizardGeneratorInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @param Wizard $options
     */
    public function __construct(Wizard $options)
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
    protected function generateLinkedDirs()
    {
        return sprintf('{%s}', implode(' ', $this->options->files->linkedDirs));
    }

    /**
     * @return string
     */
    protected function generateLinkedFiles()
    {
        return sprintf('{%s}', implode(' ', $this->options->files->linkedFiles));
    }

    /**
     * @return string
     */
    protected function generatePluginConf()
    {
        $conf = '';

        if (in_array('capistrano/composer', $this->options->setup->plugins)) {
            $conf .= "after 'deploy:starting', 'composer:install_executable'\n";
        }

        if (in_array('capistrano/npm', $this->options->setup->plugins)) {
            $conf .= "after 'deploy:updated', 'npm:install'\n";
        }

        if (in_array('capistrano/bower', $this->options->setup->plugins)) {
            $conf .= "after 'deploy:updated', 'bower:install'\n";
        }

        return $conf;
    }

    /**
     * @return string
     */
    protected function generateProjectName()
    {
        return Urlizer::urlize($this->options->name);
    }

    /**
     * @return string
     */
    protected function generateFiles()
    {
        $config = '';
        if (count($this->options->files->linkedFiles)) {
            $linkedFiles = $this->generateLinkedFiles();
            $config .= <<<EOF

{$this->generateValue('linked_files', '%w' . $linkedFiles, false)}
EOF;
        }

        if (count($this->options->files->linkedDirs)) {
            $linkedDirs = $this->generateLinkedDirs();
            $config .= <<<EOF

{$this->generateValue('linked_dirs', '%w' . $linkedDirs, false)}
EOF;
        };

        return $config;
    }
    /**
     * @return string
     */
    protected function getTemplate()
    {
        $pluginConf  = $this->generatePluginConf();
        $projectName = $this->generateProjectName();
        $files       = $this->generateFiles();

        return <<<EOF
# config valid only for current version of Capistrano
lock '3.4.0'

{$this->generateValue('application', $projectName)}
{$this->generateValue('repo_url', $this->options->repositoryUrl)}
#{$this->generateValue('repo_tree', $this->options->repositoryTree)}
{$files}
{$this->generateValue('keep_releases', $this->options->setup->keepReleases, true)}

# Default value for :scm is :git
{$this->generateValue('scm', $this->options->scm)}

# Default value for :format is :pretty
# set :format, :pretty

# Default value for :pty is false
# set :pty, true

{$pluginConf}
EOF;
    }
}
