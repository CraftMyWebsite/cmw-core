<?php

use CMW\Utils\EnvBuilder;

class cliBuilder{

    private EnvBuilder $envBuilder;

    public function __construct()
    {
        $this->envBuilder = new EnvBuilder();

        $this->loadLang();
    }

    private function loadLang(): void
    {
        require_once("app/cli/utils/lang/" . $this->envBuilder->getValue("LOCALE") . ".php");
    }

    public function emptyArgs(): void
    {
        $this->say(CLI_EMPTY_ARGS);
    }

    /* UTILS FUNCTIONS */

    public function say(string $content): void
    {
        echo "\n$content\n";
    }

    public function read(): string|false
    {
        return readline("> ");
    }

}
