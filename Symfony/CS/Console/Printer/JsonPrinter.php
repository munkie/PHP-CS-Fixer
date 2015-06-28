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

use Symfony\Component\Stopwatch\StopwatchEvent;

class JsonPrinter implements PrinterInterface
{
    /**
     * @param array                 $changed
     * @param bool                  $printDiff
     * @param bool                  $printAppliedFixers
     * @param StopwatchEvent        $fixEvent
     * @param StopwatchEvent[]|null $fixFileEvents
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
        $jFiles = array();

        foreach ($changed as $file => $fixResult) {
            $jfile = array('name' => $file);

            if ($printAppliedFixers) {
                $jfile['appliedFixers'] = $fixResult['appliedFixers'];
            }

            if ($printDiff) {
                $jfile['diff'] = $fixResult['diff'];
            }

            $jFiles[] = $jfile;
        }

        $json = array(
            'files' => $jFiles,
            'memory' => round($fixEvent->getMemory() / 1024 / 1024, 3),
            'time' => array(
                'total' => round($fixEvent->getDuration() / 1000, 3),
            ),
        );

        if (null !== $fixFileEvents) {
            $jFileTime = array();

            foreach ($fixFileEvents as $file => $event) {
                if ('__section__' === $file) {
                    continue;
                }

                $jFileTime[$file] = round($event->getDuration() / 1000, 3);
            }

            $json['time']['files'] = $jFileTime;
        }

        return json_encode($json);
    }
}
