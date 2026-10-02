<?php

namespace System25\T3sports\Dfb;

use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Utility\Logger;
use Sys25\RnBase\Utility\Strings;
use System25\T3sports\Model\Competition;
use System25\T3sports\Model\Repository\TeamRepository;
use System25\T3sports\Utility\ServiceRegistry;
use tx_rnbase;

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

class Synchronizer
{
    public const TABLE_GAMES = 'tx_cfcleague_games';

    public const TABLE_TEAMS = 'tx_cfcleague_teams';

    public const TABLE_STADIUMS = 'tx_cfcleague_stadiums';

    public const TABLE_COMPETITION = 'tx_cfcleague_competition';

    /**
     * Key ist DFB-ID, value ist T3-UID.
     */
    private $teamMap = [];

    /**
     * Key ist DFB-ID, value ist T3-UID.
     */
    private $matchMap = [];

    private $stats = [];

    private $pageUid = 0;

    private $teamRepo;

    public function __construct(?TeamRepository $teamRepo = null)
    {
        $this->teamRepo = $teamRepo ?: new TeamRepository();
    }

    public function process(\TYPO3\CMS\Core\Resource\File $file, Competition $competition)
    {
        $fileContent = $this->removeBOM($file->getContents());
        // There are some annoying null bytes...
        $fileContent = str_replace("\0", '', $fileContent);
        $fileContent = mb_convert_encoding($fileContent, 'UTF-8', 'UTF-8, ISO-8859-1');
        $lines = explode("\n", $fileContent);
        $headers = array_shift($lines);
        $headers = str_getcsv($this->prepareHeaderLine($headers), "\t");
        /** @var CsvStructure $structure */
        $structure = tx_rnbase::makeInstance(CsvStructure::class, $headers);
        $start = microtime(true);

        $this->pageUid = $competition->getPid();
        $this->initMatches($competition);

        $info = [
            'match' => ['new' => 0, 'updated' => 0, 'skipped' => 0],
            'team' => ['new' => 0, 'updated' => 0],
        ];
        $data = [
            self::TABLE_TEAMS => [],
            self::TABLE_STADIUMS => [],
            self::TABLE_GAMES => [],
            self::TABLE_COMPETITION => [],
        ];

        $cnt = 0;
        foreach ($lines as $line) {
            if (!$line) {
                continue;
            }
            $matchData = str_getcsv($line, "\t");
            if ($this->handleMatch($data, $competition, $matchData, $structure, $info)) {
                if (0 == $cnt % 50) {
                    // Speichern
                    $this->persist($data);
                    // Wettbewerb neu laden, da ggf. neue Teams drin stehen
                    $competition->reset();
                }
                ++$cnt;
            } else {
                ++$info['match']['skipped'];
            }
        }
        // Die restlichen Spiele speichern
        $this->persist($data);
        $this->stats['total']['time'] = intval(microtime(true) - $start).'s';
        $this->stats['total']['matches'] = $cnt;

        Logger::info('Update match schedule finished!', 'cfc_league', [
            'stats' => $this->stats,
            'info' => $info,
        ]);

        return $info;
    }

    public function getStats()
    {
        return $this->stats;
    }

    /**
     * @param array $data
     * @param Competition $competition
     * @param array $matchData
     * @param CsvStructure $structure
     * @param array $info
     *
     * @return bool true if line was processed
     */
    protected function handleMatch(array &$data, Competition $competition, array $matchData, CsvStructure $structure, array &$info)
    {
        $extCompId = $structure->getCompetitionId($matchData);
        if ($competition->getExtId() && $competition->getExtId() != $extCompId) {
            // Wrong competition, skip match
            return false;
        } elseif (!$competition->getExtId()) {
            if (empty($this->matchMap)) {
                // Wettbewerb zuordnen
                $competition->setProperty('extid', $extCompId);
                ServiceRegistry::getCompetitionService()->persist($competition);
            } else {
                // Automatische Zuordnung nicht mehr möglich
                return false;
            }
        }
        // sync match
        $extMatchId = $structure->getMatchId($matchData);

        $matchUid = 'NEW_'.$extMatchId;
        if (array_key_exists($extMatchId, $this->matchMap)) {
            $matchUid = $this->matchMap[$extMatchId];
            ++$info['match']['updated'];
        } else {
            ++$info['match']['new'];
        }

        $blobFields = [
            'assists', 'players_home', 'players_guest',
            'substitutes_home', 'substitutes_guest',
            'players_home_stat', 'players_guest_stat',
            'substitutes_home_stat', 'substitutes_guest_stat',
            'scorer_guest_stat', 'scorer_home_stat', 'game_report',
        ];
        foreach ($blobFields as $field) {
            $data[self::TABLE_GAMES][$matchUid][$field] = '';
        }

        $data[self::TABLE_GAMES][$matchUid]['pid'] = $this->pageUid;
        $data[self::TABLE_GAMES][$matchUid]['extid'] = $extMatchId;
        $data[self::TABLE_GAMES][$matchUid]['competition'] = $competition->getUid();
        $data[self::TABLE_GAMES][$matchUid]['round'] = $structure->getRound($matchData);
        $data[self::TABLE_GAMES][$matchUid]['round_name'] = $structure->getRound($matchData).'. Spieltag';
        // Es muss ein lokaler Timestamp gesetzt werden
        $data[self::TABLE_GAMES][$matchUid]['date'] = $structure->getKickoffDate($matchData);
        $data[self::TABLE_GAMES][$matchUid]['stadium'] = $structure->getStadium($matchData);
        $data[self::TABLE_GAMES][$matchUid]['home'] = $this->findTeam($structure->getHome($matchData), $data, $competition, $info);
        $data[self::TABLE_GAMES][$matchUid]['guest'] = $this->findTeam($structure->getGuest($matchData), $data, $competition, $info);

        // Ergebnis und Status werden nur gesetzt, wenn sie in der Datei enthalten sind.
        // Sonst würden manuell gepflegte Ergebnisse überschrieben.
        $result = $structure->getResult($matchData);
        if (null !== $result) {
            $data[self::TABLE_GAMES][$matchUid] = array_merge(
                $data[self::TABLE_GAMES][$matchUid],
                $this->buildResultData($competition, $result, $structure->getHalftimeResult($matchData))
            );
        }
        $status = $structure->getStatus($matchData);
        if (null !== $status) {
            $data[self::TABLE_GAMES][$matchUid]['status'] = $status;
        }

        return true;
    }

    /**
     * Verteilt End- und Halbzeitergebnis auf die Spielabschnitte des Wettbewerbs.
     *
     * @param Competition $competition
     * @param int[] $result [home, guest]
     * @param int[]|null $halftime [home, guest]
     *
     * @return array
     */
    protected function buildResultData(Competition $competition, array $result, ?array $halftime)
    {
        $matchParts = $competition->getMatchParts();
        $resultData = [];
        for ($i = 1; $i <= $matchParts; ++$i) {
            $resultData['goals_home_'.$i] = 0;
            $resultData['goals_guest_'.$i] = 0;
        }
        // Halbzeitergebnis ist nur bei mehreren Spielabschnitten sinnvoll
        $hasHalftime = null !== $halftime && $matchParts > 1;
        if ($hasHalftime) {
            $resultData['goals_home_1'] = $halftime[0];
            $resultData['goals_guest_1'] = $halftime[1];
        }

        if ($competition->isAddPartResults()) {
            // Die Teilergebnisse werden addiert. Der Rest kommt in den zweiten Abschnitt.
            $part = $hasHalftime ? 2 : 1;
            $resultData['goals_home_'.$part] = $result[0] - ($hasHalftime ? $halftime[0] : 0);
            $resultData['goals_guest_'.$part] = $result[1] - ($hasHalftime ? $halftime[1] : 0);
        } else {
            // Das Endergebnis steht im letzten Abschnitt
            $resultData['goals_home_'.$matchParts] = $result[0];
            $resultData['goals_guest_'.$matchParts] = $result[1];
        }

        return $resultData;
    }

    protected function findTeam($extTeam, array &$data, Competition $competition, array &$info)
    {
        $extTeamId = $this->buildKey($extTeam);
        $uid = 'NEW_'.$extTeamId;
        if (!array_key_exists($extTeamId, $this->teamMap)) {
            // Das Team ist noch nicht im Cache, also in der DB suchen
            $fields = [];
            $fields['TEAM.EXTID'][OP_EQ_NOCASE] = $extTeamId;
            $fields['TEAM.PID'][OP_EQ_INT] = $competition->getPid();

            $options = ['what' => 'uid'];
            /** @var \Sys25\RnBase\Domain\Collection\BaseCollection $ret */
            $ret = $this->teamRepo->search($fields, $options);
            if (!$ret->isEmpty()) {
                $this->teamMap[$extTeamId] = $ret->first()['uid'];
                $uid = $this->teamMap[$extTeamId];
            } else {
                // In uid steht jetzt NEW_
                // Team anlegen, falls es noch nicht in der Data-Map liegt
                if (!array_key_exists($uid, $data[self::TABLE_TEAMS])) {
                    $data[self::TABLE_TEAMS][$uid] = $this->loadTeamData($extTeamId, $extTeam);
                    ++$info['team']['new'];
                }
            }
            // Sicherstellen, daß das Team im Wettbewerb ist
            $this->addTeamToCompetition($uid, $data, $competition);
        } else {
            $uid = $this->teamMap[$extTeamId];
        }

        return $uid;
    }

    /**
     * Stellt sicher, daß das Team im Wettbewerb gespeichert wird.
     * Hier gibt es aber noch ein Todo: es wird nicht geprüft, ob die neue ID schon
     * in den TCE-Data liegt. Dadurch wird so mehrfach hinzugefügt. Das hat aber praktisch
     * keine Auswirkung, da die TCE das selbst korrigiert. Das könnte sich zukünftig aber
     * ändern...
     *
     * @param mixed $teamUid
     * @param array $data
     * @param Competition $competition
     */
    protected function addTeamToCompetition($teamUid, &$data, $competition)
    {
        $add = true;
        if ($competition->getProperty('teams')) {
            $teamUids = array_flip(Strings::trimExplode(',', $competition->getProperty('teams')));
            $add = !array_key_exists($teamUid, $teamUids);
        }
        if (!$add) {
            return;
        }
        // Das geht bestimmt auch kürzer...
        // Das Team in den Wettbewerb legen
        if (isset($data[self::TABLE_COMPETITION][$competition->getUid()]['teams'])) {
            $data[self::TABLE_COMPETITION][$competition->getUid()]['teams'] .= ','.$teamUid;
        } else {
            // Das erste Team
            if ($competition->getProperty('teams')) {
                $data[self::TABLE_COMPETITION][$competition->getUid()]['teams'] = $competition->getProperty('teams');
                $data[self::TABLE_COMPETITION][$competition->getUid()]['teams'] .= ','.$teamUid;
            } else {
                $data[self::TABLE_COMPETITION][$competition->getUid()]['teams'] = $teamUid;
            }
        }
    }

    protected function loadTeamData($extTeamId, $teamName)
    {
        return [
            'pid' => $this->pageUid,
            'extid' => $extTeamId,
            'name' => $teamName,
            'short_name' => $teamName,
        ];
    }

    protected function buildKey($string)
    {
        return str_replace(' ', '_', strtolower($string));
    }

    protected function persist(&$data)
    {
        $start = microtime(true);

        $tce = Connection::getInstance()->getTCEmain($data);
        $tce->process_datamap();
        $this->stats['chunks'][]['time'] = intval(microtime(true) - $start).'s';
        $this->stats['chunks'][]['matches'] = count($data[self::TABLE_GAMES]);

        $data[self::TABLE_TEAMS] = [];
        $data[self::TABLE_STADIUMS] = [];
        $data[self::TABLE_GAMES] = [];
        $data[self::TABLE_COMPETITION] = [];
    }

    /**
     * Sorgt dafür, daß die Header eindeutig sind. Der DFB liefert manche Spaltennamen doppelt...
     *
     * @param string $headers
     *
     * @return string
     */
    protected function prepareHeaderLine($headers)
    {
        $headers = str_replace(
            CsvStructure::COL_MATCH_DATE."\t".CsvStructure::COL_TIME,
            CsvStructure::COL_MATCH_DATE."\t".CsvStructure::COL_MATCH_TIME,
            $headers
        );

        return $headers;
    }

    protected function removeBOM($data)
    {
        if (false !== strpos($data, "\xef\xbb\xbf")) {
            $data = substr($data, 3);
        }
        // strip off BOM (LE UTF-16)
        elseif (false !== strpos($data, "\xff\xfe")) {
            $data = substr($data, 2);
        }
        // strip off BOM (BE UTF-16)
        elseif (false !== strpos($data, "\xfe\xff")) {
            $data = substr($data, 2);
        }

        return $data;
    }

    /**
     * Lädt die vorhandenen Spiele des Wettbewerbs in die matchMap.
     *
     * @param Competition $competition
     */
    protected function initMatches(Competition $competition)
    {
        $fields = $options = [];
        /* @var $matchSrv \tx_cfcleague_services_Match */
        $matchSrv = ServiceRegistry::getMatchService();
        $fields['MATCH.COMPETITION'][OP_EQ_INT] = $competition->getUid();
        $options['what'] = 'uid,extid';
        $options['orderby'] = 'uid asc';
        $options['callback'] = function ($record) {
            $this->matchMap[$record['extid']] = $record['uid'];
        };
        $matchSrv->search($fields, $options);
    }
}
