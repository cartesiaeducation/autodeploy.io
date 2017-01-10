<?php

namespace AppBundle\Services\Capistrano\Wizard;

use AppBundle\Form\Model\Capistrano\Environments;
use AppBundle\Form\Model\Capistrano\Setup;
use AppBundle\Form\Model\Capistrano\Wizard;

/**
 * Class EnvironmentGenerator.
 */
class EnvironmentGenerator extends AbstractGenerator implements WizardGeneratorInterface
{
    /**
     * @var Wizard
     */
    protected $wizard;

    /**
     * @var Environments
     */
    protected $env;

    /**
     * @param Wizard       $wizard
     * @param Environments $env
     */
    public function __construct(Wizard $wizard, Environments $env)
    {
        $this->wizard = $wizard;
        $this->env    = $env;
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
    protected function generatePluginConf()
    {
        $conf = '';

        if (in_array('capistrano/composer', $this->wizard->setup->plugins)) {
            $conf .= "set :composer_install_flags, '--prefer-dist --no-interaction --optimize-autoloader'\n";
            $conf .= 'SSHKit.config.command_map[:composer] = "php #{shared_path.join("composer.phar")}"' . "\n";
        }

        if (in_array('capistrano/symfony', $this->wizard->setup->plugins)) {
            if ($this->env->env === 'prod') {
                $conf .= 'set :controllers_to_clear, ["app_*.php"]' . "\n";
            } else {
                $conf .= 'set :controllers_to_clear, []' . "\n";
            }
        }

        return $conf;
    }

    /**
     * @return string
     */
    protected function getTemplate()
    {
        $pluginConf = $this->generatePluginConf();

        $template = <<<EOF
{$this->generateValue('stage', $this->env->env)}
{$this->generateValue('branch', $this->env->branch)}
{$this->generateValue('deploy_to', $this->env->deployTo)}
{$this->generateValue('tmp_dir', $this->env->tmp)}
{$this->generateValue('log_level', $this->env->logLevel)}

{$pluginConf}

EOF;

        foreach ($this->env->servers as $server) {
            $template .= <<<EOF
server '{$server->host}', user: '{$server->user}', port: {$server->port}, roles: %w{app db web}
EOF;
        }

        return $template;
    }
}
