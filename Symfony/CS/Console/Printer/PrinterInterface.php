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

interface PrinterInterface
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
    );
}
