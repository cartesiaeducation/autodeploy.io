<?php

namespace AppBundle\Util;

use AppBundle\Entity\Authentification;
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version1X;
use Symfony\Component\Process\Process;

/**
 * Class ProcessManager.
 */
class ProcessManager
{
    /**
     * @var string
     */
    private $cachePath;

    /**
     * @var string
     */
    private $nodeUrl;

    /**
     * Constructor.
     *
     * @param string $cachePath
     * @param string $nodeUrl
     */
    public function __construct($cachePath, $nodeUrl)
    {
        $this->cachePath = $cachePath;
        $this->nodeUrl   = $nodeUrl;
    }

    /**
     * @param $command
     * @param Authentification|null $authentification
     * @param int                   $timeout
     * @param bool|false            $live
     * @param null                  $liveCallback
     *
     * @return string
     *
     * @throws \Exception
     */
    public function execute($command, Authentification $authentification = null, $timeout = 0, $live = false, $liveCallback = null)
    {
        $repositoryKey = null;
        if (null !== $authentification) {
            $repositoryKey = $this->getSSHKeyPath(uniqid());
            $command       = sprintf('%s && ssh-agent sh -c \'ssh-add %s; %s\'',
                $this->getCommandPrefix($authentification, $repositoryKey),
                $repositoryKey,
                $command
            );
        }

        $process = new Process($command);
        $process->setTimeout($timeout);
        $process->run();

        if (!$process->isSuccessful()) {
            $errorMessage = $command . ' - ' . $process->getErrorOutput() ?: $process->getExitCodeText();
            echo $errorMessage . "\n";
            throw new \Exception($errorMessage);
        }

        if (null !== $authentification && null !== $repositoryKey) {
            @unlink($repositoryKey);
        }

        return $process->getOutput();
    }

    /**
     * @param $command
     * @param Authentification $authRepository
     * @param Authentification $authServer
     * @param int              $timeout
     * @param bool|false       $live
     * @param null             $liveCallback
     *
     * @return string
     *
     * @throws \Exception
     */
    public function executeDeploy($command, $channelId, Authentification $authRepository, Authentification $authServer, $timeout = 0, $live = false, $liveCallback = null)
    {
        $repositoryKey = $this->getSSHKeyPath(uniqid());
        $serverKey     = $this->getSSHKeyPath(uniqid());

        $command = sprintf('%s && %s && ssh-agent sh -c \'ssh-add %s; ssh-add %s; %s\'',
            $this->getCommandPrefix($authRepository,  $repositoryKey),
            $this->getCommandPrefix($authServer, $serverKey),
            $repositoryKey,
            $serverKey,
            $command
        );

        $process = new Process($command);
        $process->setTimeout($timeout);

        $client = new Client(new Version1X($this->nodeUrl, [
            'context' => [
                'http' => [
                    'headers' => 'Origin: https://www.autodeploy.io',
                ],
                'ssl' => [
                    'headers'           => 'Origin: https://www.autodeploy.io',
                    'verify_peer'       => false,
                    'allow_self_signed' => true,
                ],
            ],
        ]));

        try {
            $client->initialize();
        } catch (\Exception $e) {
            echo $e->getMessage() . $this->nodeUrl . "\n";
        }

        $process->run(function ($type, $buffer) use ($client, $channelId) {
            try {
                if (!empty($buffer)
                    && false === mb_strpos($buffer, 'ssh-add ')
                    && false === mb_strpos($buffer, 'Identity added')) {
                    $client->emit('history_print', [
                        'queueId' => $channelId,
                        'line'    => $buffer,
                    ]);
                }
            } catch (\Exception $e) {
                echo $e->getMessage() . "\n";
            }
        });
        try {
            $client->emit('history_print_refresh', [
                'queueId' => $channelId,
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }

        if (!$process->isSuccessful()) {
            $errorMessage = $command . ' - ' . $process->getErrorOutput() ?: $process->getExitCodeText();
            echo $errorMessage . "\n";
            throw new \Exception($errorMessage);
        }

        @unlink($repositoryKey);
        @unlink($serverKey);

        return $process->getOutput();
    }

    /**
     * @param $file
     *
     * @return string
     */
    protected function getSSHKeyPath($file)
    {
        return sprintf('%s/.ssh/%s', $this->cachePath, $file);
    }

    /**
     * @param Authentification $authentification
     * @param $path
     *
     * @return string
     */
    protected function getCommandPrefix(Authentification $authentification, $path)
    {
        return sprintf('echo "%s" > %s && chmod 600 %s', $authentification->getSsh(), $path, $path);
    }
}
