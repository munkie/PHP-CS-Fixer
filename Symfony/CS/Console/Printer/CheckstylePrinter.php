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
use Symfony\CS\ConfigAwareInterface;
use Symfony\CS\ConfigInterface;
use Symfony\CS\Fixer;
use Symfony\CS\FixerInterface;

class CheckstylePrinter implements PrinterInterface, ConfigAwareInterface
{
    /**
     * @var FixerInterface[]
     */
    protected $fixers;

    /**
     * @var array
     */
    protected static $levels = array(
        FixerInterface::NONE_LEVEL => 'None',
        FixerInterface::PSR0_LEVEL => 'PSR0',
        FixerInterface::PSR1_LEVEL => 'PSR1',
        FixerInterface::PSR2_LEVEL => 'PSR2',
        FixerInterface::SYMFONY_LEVEL => 'Symfony',
        FixerInterface::CONTRIB_LEVEL => 'Contrib',
    );

    /**
     * Sets the active config on the fixer.
     *
     * @param ConfigInterface $config
     */
    public function setConfig(ConfigInterface $config)
    {
        foreach ($config->getFixers() as $fixer) {
            $this->fixers[$fixer->getName()] = $fixer;
        }
    }


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
        $checkstyleXML = $dom->createElement('checkstyle');
        $checkstyleXML->setAttribute('version', Fixer::VERSION);
        $dom->appendChild($checkstyleXML);

        foreach ($changed as $file => $fixResult) {
            $fileXML = $dom->createElement('file');
            $fileXML->setAttribute('name', $file);

            foreach ($fixResult['appliedFixers'] as $appliedFixer) {
                $errorXML = $dom->createElement('error');

                $fixer = $this->fixers[$appliedFixer];

                if (isset(static::$levels[$fixer->getLevel()])) {
                    $severity = static::$levels[$fixer->getLevel()];
                } else {
                    $severity = 'None';
                }

                $errorXML->setAttribute('line', 0);
                $errorXML->setAttribute('severity',  $severity);
                $errorXML->setAttribute('message', $fixer->getDescription());
                $errorXML->setAttribute('source', $fixer->getName());

                $fileXML->appendChild($errorXML);
            }

            $checkstyleXML->appendChild($fileXML);
        }

        $dom->formatOutput = true;

        return $dom->saveXML();
    }
}
