<?php

namespace System25\T3sports\Module\Utility;

use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Utility\Strings;
use System25\T3sports\Model\Team;
use System25\T3sports\Model\TeamNoteType;
use System25\T3sports\Utility\ServiceRegistry;
use Sys25\RnBase\Configuration\Processor;

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

/**
 * Position bzw. Funktion einer Person in einem Team. Da eine Person in jedem Team
 * eine andere Position haben kann, wird diese als Team-Notiz gespeichert.
 *
 * Der Notiz-Typ wird über die Extension-Konfiguration "positionNoteType" festgelegt.
 * Ist dort nichts gesetzt, wird der Notiz-Typ mit dem Marker "POSITION" verwendet.
 */
class TeamPositionNotes
{
    public const DEFAULT_MARKER = 'POSITION';

    public const TABLE = 'tx_cfcleague_team_notes';

    /** @var TeamNoteType|null|false false = noch nicht ermittelt */
    private $noteType = false;

    /**
     * Liefert den Notiz-Typ für die Position oder null, wenn keiner konfiguriert ist.
     *
     * @return TeamNoteType|null
     */
    public function getNoteType()
    {
        if (false !== $this->noteType) {
            return $this->noteType;
        }
        $this->noteType = null;
        $typeUid = (int) Processor::getExtensionCfgValue('cfc_league', 'positionNoteType');
        foreach (ServiceRegistry::getTeamService()->getNoteTypes() as $type) {
            if ($typeUid > 0 ? $type->getUid() == $typeUid : 0 == strcasecmp(trim((string) $type->getMarker()), self::DEFAULT_MARKER)) {
                $this->noteType = $type;
                break;
            }
        }

        return $this->noteType;
    }

    public function isAvailable(): bool
    {
        return null !== $this->getNoteType();
    }

    /**
     * Liefert die Positionen aller Personen im Team.
     *
     * @return string[] key ist die UID der Person
     */
    public function getPositions(Team $team): array
    {
        $positions = [];
        foreach ($this->findNotes($team) as $profileUid => $note) {
            $positions[$profileUid] = (string) $note->getProperty('comment');
        }

        return $positions;
    }

    /**
     * Setzt die Position für die angegebenen Personen im Team. Vorhandene Einträge
     * werden aktualisiert.
     *
     * @param int[] $profileUids
     */
    public function setPosition(Team $team, array $profileUids, string $position)
    {
        $position = trim($position);
        $type = $this->getNoteType();
        if ('' === $position || null === $type || empty($profileUids)) {
            return;
        }

        $notes = $this->findNotes($team);
        $data = [];
        $i = 0;
        foreach (array_unique(array_map('intval', $profileUids)) as $profileUid) {
            if ($profileUid <= 0) {
                continue;
            }
            if (isset($notes[$profileUid])) {
                $data[self::TABLE][$notes[$profileUid]->getUid()] = ['comment' => $position];
            } else {
                $data[self::TABLE]['NEW'.(++$i)] = [
                    'pid' => (int) $team->getProperty('pid'),
                    'team' => $team->getUid(),
                    'player' => $profileUid,
                    'type' => $type->getUid(),
                    'mediatype' => 0,
                    'comment' => $position,
                ];
            }
        }
        if (empty($data)) {
            return;
        }
        $tce = Connection::getInstance()->getTCEmain($data);
        $tce->process_datamap();
    }

    /**
     * Liefert die UIDs aller Personen im Team, unabhängig von der Funktion.
     *
     * @return int[]
     */
    public static function getMemberUids(Team $team): array
    {
        $uids = [];
        foreach (['players', 'coaches', 'supporters'] as $column) {
            $uids = array_merge($uids, Strings::intExplode(',', (string) $team->getProperty($column)));
        }

        return array_values(array_unique(array_filter($uids)));
    }

    /**
     * @return \System25\T3sports\Model\TeamNote[] key ist die UID der Person
     */
    private function findNotes(Team $team): array
    {
        $type = $this->getNoteType();
        if (null === $type) {
            return [];
        }
        $notes = [];
        foreach (ServiceRegistry::getTeamService()->getTeamNotes($team, $type) as $note) {
            $notes[(int) $note->getProperty('player')] = $note;
        }

        return $notes;
    }
}
