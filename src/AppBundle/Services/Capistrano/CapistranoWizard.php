<?php

namespace AppBundle\Services\Capistrano;

use AppBundle\Form\Model\Capistrano\Wizard;
use AppBundle\Services\Capistrano\Wizard\CapfileGenerator;
use AppBundle\Services\Capistrano\Wizard\DeployGenerator;
use AppBundle\Services\Capistrano\Wizard\EnvironmentGenerator;
use AppBundle\Services\Capistrano\Wizard\GemfileGenerator;
use AppBundle\Util\ProcessManager;
use Gedmo\Sluggable\Util\Urlizer;

/**
 * Class CapistranoWizard.
 */
class CapistranoWizard
{
    /**
     * @var WizardArchive
     */
    protected $archive;

    /**
     * @var ProcessManager
     */
    protected $processManager;

    /**
     * Constructor.
     */
    public function __construct(ProcessManager $processManager)
    {
        $this->processManager = $processManager;
    }

    /**
     * @return Wizard
     */
    public function create()
    {
        return new Wizard();
    }

    /**
     * @param Wizard $wizard
     *
     * @return string
     */
    public function createArchive(Wizard $wizard)
    {
        $this->archive = new WizardArchive($this->processManager);

        $this->createGemfileIfNeeded($wizard);
        $this->createCapfile($wizard);
        $this->createDeploy($wizard);
        $this->createEnvironments($wizard);

        $this->archive->write();

        return $this->archive->zip();
    }

    /**
     * @param Wizard $wizard
     */
    protected function createGemfileIfNeeded(Wizard $wizard)
    {
        if (!$wizard->options->generateGemfile) {
            return;
        }

        $generator = new GemfileGenerator($wizard->setup->plugins);
        $this->archive->queueToFile('Gemfile', $generator->generate());
    }

    /**
     * @param Wizard $wizard
     */
    protected function createCapfile(Wizard $wizard)
    {
        $generator = new CapfileGenerator($wizard->setup);
        $this->archive->queueToFile('Capfile', $generator->generate());
    }

    /**
     * @param Wizard $wizard
     */
    protected function createDeploy(Wizard $wizard)
    {
        $generator = new DeployGenerator($wizard);
        $this->archive->queueToFile($this->getDirectory($wizard) . '/config/deploy.rb', $generator->generate());
    }

    /**
     * @param Wizard $wizard
     */
    protected function createEnvironments(Wizard $wizard)
    {
        foreach ($wizard->environments as $environment) {
            $environment->env = Urlizer::urlize($environment->env);
            $generator        = new EnvironmentGenerator($wizard, $environment);
            $this->archive->queueToFile($this->getDirectory($wizard) . '/config/deploy/' . $environment->env . '.rb', $generator->generate());
        }
    }

    /**
     * @param Wizard $wizard
     *
     * @return string
     */
    protected function getDirectory(Wizard $wizard)
    {
        $directory = trim($wizard->setup->directory);
        if ('/' === mb_substr($wizard->setup->directory, mb_strlen($wizard->setup->directory) - 1)) {
            $directory = mb_substr($wizard->setup->directory, 0, -1);
        }

        return $directory;
    }
}
