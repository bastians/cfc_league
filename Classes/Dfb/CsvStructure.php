<?php

namespace System25\T3sports\Dfb;

use Sys25\RnBase\Utility\Dates;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2023 Rene Nitzsche (rene@system25.de)
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

class CsvStructure
{
    public const COL_DATE = 'Datum';

    public const COL_TIME = 'Uhrzeit';

    public const COL_SAISON = 'Saison';

    public const COL_UNION = 'Verband';

    public const COL_AGE_GROUP_ID = 'MannschaftsartID';

    public const COL_AGE_GROUP = 'Mannschaftsart';

    public const COL_LEAGUE_ID = 'SpielklasseID';

    public const COL_LEAGUE_TYPE = 'Spielklasse';

    public const COL_AREA_ID = 'SpielgebietID';

    public const COL_AREA = 'Spielgebiet';

    public const COL_MATCHTABLE = 'Rahmenspielplan';

    public const COL_LEAGUE_NUMBER = 'Staffelnummer';

    public const COL_LEAGUE = 'Staffel';

    public const COL_LEAGUE_IDENT = 'Staffelkennung';

    public const COL_LEAGUE_CHIEF = 'Staffelleiter';

    public const COL_MATCH_DATE = 'Spieldatum';

    public const COL_MATCH_TIME = 'Anstosszeit'; // Changed from "Uhrzeit"

    public const COL_MATCH_WEEKDAY = 'Wochentag';

    public const COL_MATCH_ROUND = 'Spieltag';

    public const COL_MATCH_KEYDAY = 'Schlüsseltag';

    public const COL_MATCH_HOME = 'Heimmannschaft';

    public const COL_MATCH_GUEST = 'Gastmannschaft';

    public const COL_MATCH_ID = 'Spielkennung';

    public const COL_MATCH_VALID = 'freigegeben';

    public const COL_STADIUM = 'Spielstätte';

    public const COL_REFEREE = 'Spielleitung';

    public const COL_ASSIST_1 = 'Assistent 1';

    public const COL_ASSIST_2 = 'Assistent 2';

    public const COL_POSTPONE_WEEKDAY = 'verlegtWochentag';

    public const COL_POSTPONE_DATE = 'verlegtSpieldatum';

    public const COL_POSTPONE_TIME = 'verlegtUhrzeit';

    /** Endergebnis, z.B. "2:1" oder "2:1 (1:0)" */
    public const COL_RESULT = 'Ergebnis';

    /** Halbzeitergebnis, z.B. "1:0" */
    public const COL_RESULT_HALFTIME = 'Halbzeit';

    /** Spielstatus, entweder numerisch (TCA-Wert) oder als Text */
    public const COL_STATUS = 'Spielstatus';

    public const STATUS_SCHEDULED = 0;

    public const STATUS_RUNNING = 1;

    public const STATUS_FINISHED = 2;

    public const STATUS_INVALID = -1;

    public const STATUS_RESCHEDULED = -10;

    /** Textwerte für den Spielstatus. Key ist der Text in Kleinbuchstaben. */
    private const STATUS_MAP = [
        'geplant' => self::STATUS_SCHEDULED,
        'angesetzt' => self::STATUS_SCHEDULED,
        'scheduled' => self::STATUS_SCHEDULED,
        'läuft' => self::STATUS_RUNNING,
        'laeuft' => self::STATUS_RUNNING,
        'live' => self::STATUS_RUNNING,
        'running' => self::STATUS_RUNNING,
        'beendet' => self::STATUS_FINISHED,
        'abgeschlossen' => self::STATUS_FINISHED,
        'gespielt' => self::STATUS_FINISHED,
        'finished' => self::STATUS_FINISHED,
        'ungültig' => self::STATUS_INVALID,
        'ungueltig' => self::STATUS_INVALID,
        'abgesagt' => self::STATUS_INVALID,
        'annulliert' => self::STATUS_INVALID,
        'invalid' => self::STATUS_INVALID,
        'verlegt' => self::STATUS_RESCHEDULED,
        'rescheduled' => self::STATUS_RESCHEDULED,
    ];

    /** Spalte in CSV-Datei */
    public const DATA_COL = 'data_col';

    /** Pflichtfeld */
    public const DATA_REQUIRED = 'data_required';

    protected $structure = [];

    public function __construct(array $headers)
    {
        $this->init($headers);
    }

    protected function init(array $headers)
    {
        $this->structure = [
            self::COL_MATCH_DATE => $this->createColData(),
            self::COL_MATCH_TIME => $this->createColData(),
            self::COL_MATCH_HOME => $this->createColData(),
            self::COL_MATCH_GUEST => $this->createColData(),
            self::COL_MATCH_ROUND => $this->createColData(),
            self::COL_MATCH_ID => $this->createColData(),
            self::COL_STADIUM => $this->createColData(),
            self::COL_LEAGUE_IDENT => $this->createColData(),
            self::COL_POSTPONE_DATE => $this->createColData(),
            self::COL_POSTPONE_TIME => $this->createColData(),
            self::COL_RESULT => $this->createColData(false),
            self::COL_RESULT_HALFTIME => $this->createColData(false),
            self::COL_STATUS => $this->createColData(false),
        ];
        foreach ($this->structure as $field => $data) {
            $idx = array_search($field, $headers);
            if (false !== $idx) {
                $this->structure[$field][self::DATA_COL] = $idx;
            }
        }
    }

    public function getMatchId(array $line)
    {
        return $this->getData($line, $this->structure[self::COL_MATCH_ID][self::DATA_COL]);
    }

    public function getCompetitionId($line)
    {
        return $this->getData($line, $this->structure[self::COL_LEAGUE_IDENT][self::DATA_COL]);
    }

    public function getStadium(array $line)
    {
        return $this->getData($line, $this->structure[self::COL_STADIUM][self::DATA_COL]);
    }

    public function getRound(array $line)
    {
        return $this->getData($line, $this->structure[self::COL_MATCH_ROUND][self::DATA_COL]);
    }

    public function getHome(array $line)
    {
        return $this->getData($line, $this->structure[self::COL_MATCH_HOME][self::DATA_COL]);
    }

    public function getGuest(array $line)
    {
        return $this->getData($line, $this->structure[self::COL_MATCH_GUEST][self::DATA_COL]);
    }

    public function getKickoffDate(array $line)
    {
        if ($day = $this->getData($line, $this->structure[self::COL_POSTPONE_DATE][self::DATA_COL])) {
            $time = $this->getData($line, $this->structure[self::COL_POSTPONE_TIME][self::DATA_COL]);
        } else {
            $day = $this->getData($line, $this->structure[self::COL_MATCH_DATE][self::DATA_COL]);
            $time = $this->getData($line, $this->structure[self::COL_MATCH_TIME][self::DATA_COL]);
        }
        $date = Dates::getDateTime($day.' '.$time);

        return $date->getTimestamp();
    }

    /**
     * Liefert das Endergebnis des Spiels. Ist in der Spalte auch das Halbzeitergebnis
     * in Klammern enthalten, z.B. "2:1 (1:0)", dann wird nur das Endergebnis geliefert.
     *
     * @return int[]|null [home, guest] or null if no result is set
     */
    public function getResult(array $line): ?array
    {
        $results = $this->parseResults($this->getData($line, $this->structure[self::COL_RESULT][self::DATA_COL]));

        return $results[0] ?? null;
    }

    /**
     * Liefert das Halbzeitergebnis des Spiels. Wenn keine eigene Spalte vorhanden ist,
     * wird das Ergebnis in Klammern aus der Spalte für das Endergebnis verwendet.
     *
     * @return int[]|null [home, guest] or null if no result is set
     */
    public function getHalftimeResult(array $line): ?array
    {
        $results = $this->parseResults($this->getData($line, $this->structure[self::COL_RESULT_HALFTIME][self::DATA_COL]));
        if (!empty($results)) {
            return $results[0];
        }
        $results = $this->parseResults($this->getData($line, $this->structure[self::COL_RESULT][self::DATA_COL]));

        return $results[1] ?? null;
    }

    /**
     * Liefert den Spielstatus. Wenn kein Status gesetzt ist, aber ein Endergebnis vorliegt,
     * dann wird das Spiel als beendet betrachtet.
     *
     * @return int|null the status value or null if status is unknown
     */
    public function getStatus(array $line): ?int
    {
        $status = trim((string) $this->getData($line, $this->structure[self::COL_STATUS][self::DATA_COL]));
        if ('' !== $status) {
            if (preg_match('/^-?\d+$/', $status)) {
                return (int) $status;
            }
            $status = mb_strtolower($status);
            if (array_key_exists($status, self::STATUS_MAP)) {
                return self::STATUS_MAP[$status];
            }
        }

        return null !== $this->getResult($line) ? self::STATUS_FINISHED : null;
    }

    /**
     * Findet alle Ergebnisse der Form "2:1" oder "2-1" in einem String.
     *
     * @return int[][]
     */
    protected function parseResults($value): array
    {
        $results = [];
        if (null === $value || '' === trim((string) $value)) {
            return $results;
        }
        if (preg_match_all('/(\d+)\s*[:\-]\s*(\d+)/', (string) $value, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $results[] = [(int) $match[1], (int) $match[2]];
            }
        }

        return $results;
    }

    protected function getData($line, $col)
    {
        if (null === $col) {
            return null;
        }

        return $line[$col] ?? null;
    }

    protected function createColData($required = true)
    {
        return [
            self::DATA_REQUIRED => $required,
            self::DATA_COL => null,
        ];
    }
}
