<?php

namespace AppBundle\Services\Capistrano;

use AppBundle\Util\ProcessManager;

/**
 * Class WizardArchive.
 */
class WizardArchive
{
    /**
     * @var array
     */
    protected $filesToWrite;

    /**
     * @var string
     */
    protected $targetDir;

    /**
     * @var string
     */
    protected $targetPath;

    /**
     * @var ProcessManager
     */
    protected $processManager;

    /**
     * Constructor.
     */
    public function __construct(ProcessManager $processManager)
    {
        $this->filesToWrite   = [];
        $this->processManager = $processManager;
        $this->setTargets();
    }

    /**
     * Set targets.
     */
    protected function setTargets()
    {
        $this->targetDir  = sys_get_temp_dir() . '/' . uniqid() . '/';
        $this->targetPath = $this->targetDir . 'config.zip';
        @mkdir($this->targetDir);
    }

    /**
     * Queues content to write to file.
     *
     * @param string $file    Full filename, relative to $targetDir
     * @param string $content Content to write to file
     *
     * @return bool
     */
    public function queueToFile($file, $content)
    {
        if (empty($this->filesToWrite[$file])) {
            $this->filesToWrite[$file] = '';
        }

        $this->filesToWrite[$file] .= trim($content) . "\n";
    }

    /**
     * Write queued contents to files.
     */
    public function write()
    {
        if (empty($this->filesToWrite)) {
            throw new \RuntimeException('File queue is empty');
        }

        foreach ($this->filesToWrite as $file => $content) {
            $this->writeFile(
                $this->targetDir . $file,
                $content
            );
        }
    }

    /**
     * @param string $path
     * @param string $content
     * @param int    $flag
     *
     * @return bool
     */
    protected function writeFile($path, $content, $flag = LOCK_EX)
    {
        $dirname = pathinfo($path, PATHINFO_DIRNAME);

        if (!file_exists($dirname)) {
            @mkdir($dirname, 0777, true);
        }

        return (bool) file_put_contents($path, $content, $flag);
    }

    /**
     * @return string
     */
    public function zip()
    {
        $info   = pathinfo($this->targetDir);
        $folder = $info['filename'];

        // ignore .git folders/files
        $exec = sprintf(
            'cd "%s" && cd ../ && zip -r "%s" "%s" -x */.git[!a]\*',
            $this->targetDir,
            $this->targetDir . 'config.zip',
            $folder
        );

        $this->processManager->execute($exec);

        return $this->targetPath;
    }
}
