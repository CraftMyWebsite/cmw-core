<?php

namespace CMW\Cli\Builder\AI\Copilot;

use CMW\Cli\CliBuilder;
use CMW\Manager\Env\EnvManager;

require_once ('App/Cli/CliBuilder.php');

class AICopilotContextBuilder extends CliBuilder
{
    public function __construct()
    {
        parent::__construct();
        $this->init();
    }

    private function init(): void
    {
        $this->sayLn("Téléchargement de la documentation FR/Technical depuis GitHub...");

        $baseDir = EnvManager::getInstance()->getValue("DIR") . '.copilot/';
        $targetDir = $baseDir . 'Technical';

        $tmpRepo = sys_get_temp_dir() . '/cmw-doc';
        if (!is_dir($tmpRepo . '/.git')) {
            shell_exec("git clone --depth=1 https://github.com/CraftMyWebsite/cmw-doc.git $tmpRepo");
        } else {
            shell_exec("cd $tmpRepo && git pull");
        }

        if (!is_dir($baseDir) && !mkdir($baseDir, 0777, true) && !is_dir($baseDir)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $baseDir));
        }

        if (is_dir($targetDir)) {
            $this->sayLn("Suppression de l'ancien dossier Technical...");
            shell_exec("rm -rf " . escapeshellarg($targetDir));
        }
        shell_exec("cp -r " . escapeshellarg($tmpRepo . "/FR/Technical") . " " . escapeshellarg($targetDir));

        $this->sayLn("Documentation copiée dans .copilot/Technical !");
    }
}
