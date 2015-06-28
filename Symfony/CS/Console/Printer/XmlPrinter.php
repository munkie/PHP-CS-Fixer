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

class XmlPrinter implements PrinterInterface
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
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $filesXML = $dom->createElement('files');
        $dom->appendChild($filesXML);

        $i = 1;

        foreach ($changed as $file => $fixResult) {
            $fileXML = $dom->createElement('file');
            $fileXML->setAttribute('id', $i++);
            $fileXML->setAttribute('name', $file);
            $filesXML->appendChild($fileXML);

            if ($printAppliedFixers) {
                $appliedFixersXML = $dom->createElement('applied_fixers');
                $fileXML->appendChild($appliedFixersXML);

                foreach ($fixResult['appliedFixers'] as $appliedFixer) {
                    $appliedFixerXML = $dom->createElement('applied_fixer');
                    $appliedFixerXML->setAttribute('name', $appliedFixer);
                    $appliedFixersXML->appendChild($appliedFixerXML);
                }
            }

            if ($printDiff) {
                $diffXML = $dom->createElement('diff');
                $diffXML->appendChild($dom->createCDATASection($fixResult['diff']));
                $fileXML->appendChild($diffXML);
            }
        }

        $timeXML = $dom->createElement('time');
        $memoryXML = $dom->createElement('memory');
        $dom->appendChild($timeXML);
        $dom->appendChild($memoryXML);

        $memoryXML->setAttribute('value', round($fixEvent->getMemory() / 1024 / 1024, 3));
        $memoryXML->setAttribute('unit', 'MB');

        $timeXML->setAttribute('unit', 's');
        $timeTotalXML = $dom->createElement('total');
        $timeTotalXML->setAttribute('value', round($fixEvent->getDuration() / 1000, 3));
        $timeXML->appendChild($timeTotalXML);

        if (null !== $fixFileEvents) {
            $timeFilesXML = $dom->createElement('files');
            $timeXML->appendChild($timeFilesXML);
            $eventCounter = 1;

            foreach ($fixFileEvents as $file => $event) {
                if ('__section__' === $file) {
                    continue;
                }

                $timeFileXML = $dom->createElement('file');
                $timeFilesXML->appendChild($timeFileXML);
                $timeFileXML->setAttribute('id', $eventCounter++);
                $timeFileXML->setAttribute('name', $file);
                $timeFileXML->setAttribute('value', round($event->getDuration() / 1000, 3));
            }
        }

        $dom->formatOutput = true;

        return $dom->saveXML();
    }
}
