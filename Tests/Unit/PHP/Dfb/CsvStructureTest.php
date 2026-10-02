<?php

namespace System25\T3sports\Tests\Dfb;

use Sys25\RnBase\Testing\BaseTestCase;
use System25\T3sports\Dfb\CsvStructure;

/***************************************************************
*  Copyright notice
*
*  (c) 2026 Rene Nitzsche (rene@system25.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

class CsvStructureTest extends BaseTestCase
{
    /**
     * @group unit
     */
    public function testResultColumnsMissing()
    {
        $structure = new CsvStructure(['Spieldatum', 'Spielkennung']);
        $line = ['01.10.2026', 'ABC'];

        $this->assertNull($structure->getResult($line));
        $this->assertNull($structure->getHalftimeResult($line));
        $this->assertNull($structure->getStatus($line));
    }

    /**
     * @dataProvider getResultLines
     * @group unit
     */
    public function testResult($result, $halftime, $status, $expResult, $expHalftime, $expStatus)
    {
        $structure = new CsvStructure(['Spielkennung', 'Ergebnis', 'Halbzeit', 'Spielstatus']);
        $line = ['ABC', $result, $halftime, $status];

        $this->assertSame($expResult, $structure->getResult($line));
        $this->assertSame($expHalftime, $structure->getHalftimeResult($line));
        $this->assertSame($expStatus, $structure->getStatus($line));
    }

    public function getResultLines()
    {
        return [
            'no result' => ['', '', '', null, null, null],
            'result only' => ['2:1', '', '', [2, 1], null, 2],
            'result with halftime' => ['2:1', '1:0', '', [2, 1], [1, 0], 2],
            'halftime in brackets' => ['3 : 2 (0:2)', '', '', [3, 2], [0, 2], 2],
            'dash separator' => ['4-0', '2-0', '', [4, 0], [2, 0], 2],
            'numeric status' => ['1:1', '0:0', '1', [1, 1], [0, 0], 1],
            'text status' => ['', '', 'verlegt', null, null, -10],
            'text status case' => ['', '', 'Abgesagt', null, null, -1],
            'unknown status with result' => ['0:3', '', 'foo', [0, 3], null, 2],
        ];
    }

    /**
     * @group unit
     */
    public function testFirstColumnIsFound()
    {
        // Spalte mit Index 0 wurde früher nicht erkannt
        $structure = new CsvStructure(['Ergebnis', 'Spielkennung']);
        $this->assertSame([5, 0], $structure->getResult(['5:0', 'ABC']));
    }
}
