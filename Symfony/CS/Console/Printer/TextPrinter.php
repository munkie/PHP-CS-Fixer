<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Console\Printer;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\StopwatchEvent;
use Symfony\CS\Console\OutputAwareInterface;

class TextPrinter implements PrinterInterface, OutputAwareInterface
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param array            $changed
     * @param bool             $printDiff
     * @param bool             $printAppliedFixers
     * @param StopwatchEvent   $fixEvent
     * @param StopwatchEvent[] $fixFileEvents
     *
     * @return string
     */
    public function printFixes(
        array $changed,
        $printDiff,
        $printAppliedFixers,
        StopwatchEvent $fixEvent,
        array $fixFileEvents = null
    ) {
        $i = 1;

        foreach ($changed as $file => $fixResult) {
            $this->output->write(sprintf('%4d) %s', $i++, $file));

            if ($printAppliedFixers) {
                $this->output->write(sprintf(' (<comment>%s</comment>)', implode(', ', $fixResult['appliedFixers'])));
            }

            if ($printDiff) {
                $this->output->writeln('');
                $this->output->writeln('<comment>      ---------- begin diff ----------</comment>');
                $this->output->writeln($fixResult['diff']);
                $this->output->writeln('<comment>      ---------- end diff ----------</comment>');
            }

            $this->output->writeln('');
        }

        if (null !== $fixFileEvents) {
            $this->output->writeln('Fixing time per file:');

            foreach ($fixFileEvents as $file => $event) {
                if ('__section__' === $file) {
                    continue;
                }

                $this->output->writeln(sprintf('[%.3f s] %s', $event->getDuration() / 1000, $file));
            }

            $this->output->writeln('');
        }

        $this->output->writeln(
            sprintf(
                'Fixed all files in %.3f seconds, %.3f MB memory used',
                $fixEvent->getDuration() / 1000,
                $fixEvent->getMemory() / 1024 / 1024
            )
        );

        return '';
    }
}
