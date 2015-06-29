<?php

namespace Symfony\CS\Console;

use Symfony\Component\Console\Output\OutputInterface;

interface OutputAwareInterface
{
    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output);
}
