<?php

namespace AppBundle\Services\Capistrano;

use AppBundle\Entity\Authentification;
use AppBundle\Util\ProcessManager;

/**
 * Class CapistranoManager.
 */
class CapistranoManager
{
    /**
     * @var ProcessManager
     */
    protected $processManager;

    /**
     * @var bool
     */
    protected $sudoer;

    /**
     * @param ProcessManager $processManager
     */
    public function __construct(ProcessManager $processManager, $sudoer = true)
    {
        $this->processManager = $processManager;
        $this->sudoer         = $sudoer;
    }

    /**
     * @param string $localPath
     *
     * @throws \Exception
     */
    public function setup($localPath)
    {
        $this->checkCapfileExist($localPath);
        if (!$this->checkGemfileExist($localPath)) {
            $this->initGemfile($localPath);
        }
    }

    /**
     * @param string $localPath
     *
     * @throws \Exception
     */
    public function install($localPath)
    {
        $this->processManager->execute(sprintf('cd "%s" && %s bundle install', $localPath, $this->sudoer ? 'sudo' : ''));
    }

    /**
     * @param string $localPath
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getTasks($localPath)
    {
        $output = $this->processManager->execute(sprintf('cd "%s" && bundle exec cap -T', $localPath));
        $lines  = explode("\n", trim($output));

        $task = [];
        foreach ($lines as $line) {
            $line = explode(' #', $line);

            if (!empty($line) && !empty($line[0]) && !empty($line[1])) {
                $task[] = [
                    'name'        => trim(str_replace('cap ', '', $line[0])),
                    'command'     => trim($line[0]),
                    'description' => trim($line[1]),
                ];
            }
        }

        return $task;
    }

    /**
     * @param string $localPath
     *
     * @return string
     */
    private function getCapfilePath($localPath)
    {
        return sprintf('%s/Capfile', $localPath);
    }

    /**
     * @param string $localPath
     *
     * @return string
     */
    private function getGemfilePath($localPath)
    {
        return sprintf('%s/Gemfile', $localPath);
    }

    /**
     * @param string $localPath
     *
     * @throws \Exception
     */
    private function checkCapfileExist($localPath)
    {
        if (!file_exists($this->getCapfilePath($localPath))) {
            throw new \Exception('Capfile missing in root project directory');
        };
    }

    /**
     * @param string $localPath
     *
     * @return bool
     */
    private function checkGemfileExist($localPath)
    {
        return file_exists($this->getGemfilePath($localPath));
    }

    /**
     * @param string $localPath
     */
    private function initGemfile($localPath)
    {
        $version = $this->determineCapistranoVersion($localPath);

        switch ($version) {
            case 2:
                $content = "source 'https://rubygems.org'
gem 'capistrano', '= 2.15.6'
gem 'railsless-deploy', '>= 1.1.3'
gem 'net-ssh', '= 2.9.0'
gem 'capistrano-ext'
gem 'capistrano-slack', '= 1.3.2'
gem 'capistrano_colors'
gem 'colored'\n";
                break;

            default:
                $content = "source 'https://rubygems.org'

gem 'capistrano'\n";
                break;
        }

        @file_put_contents($this->getGemfilePath($localPath), $content);
    }

    /**
     * @param string $localPath
     *
     * @return int
     */
    private function determineCapistranoVersion($localPath)
    {
        if ($content = @file_get_contents($this->getCapfilePath($localPath))) {
            if (false !== mb_strpos($content, 'railsless-deploy')) {
                return 2;
            }
        }

        return 3;
    }

    /**
     * @param $command
     * @param $environment
     * @param $localPath
     * @param Authentification $authRepository
     * @param Authentification $authServer
     *
     * @return string
     *
     * @throws \Exception
     */
    public function run($command, $environment, $localPath, Authentification $authRepository, Authentification $authServer, $channelId = null)
    {
        if (0 !== mb_strpos($command, 'cap ')) {
            throw new \Exception('Invalid task name');
        }

        $command = sprintf('cap "%s" "%s"', $environment, mb_substr($command, 4));

        return $this->processManager->executeDeploy(sprintf('cd "%s" && bundle exec %s', $localPath, $command), $channelId, $authRepository, $authServer);
    }
}
