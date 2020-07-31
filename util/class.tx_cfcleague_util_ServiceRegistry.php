<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009-2015 Rene Nitzsche (rene@system25.de)
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

tx_rnbase::load('tx_rnbase_util_Misc');

class tx_cfcleague_util_ServiceRegistry
{
    /**
     * Liefert den Competition-Service.
     *
     * @return tx_cfcleague_services_Competition
     */
    public static function getCompetitionService()
    {
        return tx_rnbase_util_Misc::getService('t3sports_srv', 'competition');
    }

    /**
     * Liefert den Match-Service.
     *
     * @return tx_cfcleague_services_Match
     */
    public static function getMatchService()
    {
        return tx_rnbase_util_Misc::getService('t3sports_srv', 'match');
    }

    /**
     * Liefert den Stadium-Service.
     *
     * @return tx_cfcleague_services_Stadiums
     */
    public static function getStadiumService()
    {
        return tx_rnbase_util_Misc::getService('t3sports_srv', 'stadiums');
    }

    /**
     * Liefert den Stadium-Service.
     *
     * @return tx_cfcleague_services_Stadiums
     */
    public static function getSaisonService()
    {
        return tx_rnbase_util_Misc::getService('t3sports_srv', 'saison');
    }

    /**
     * Liefert den Profile-Service.
     *
     * @return tx_cfcleague_services_Profiles
     */
    public static function getProfileService()
    {
        return tx_rnbase_util_Misc::getService('t3sports_srv', 'profiles');
    }

    /**
     * Return den Team-Service.
     *
     * @return tx_cfcleague_services_Teams
     */
    public static function getTeamService()
    {
        return tx_rnbase_util_Misc::getService('t3sports_srv', 'teams');
    }

    /**
     * Returns Group-Service.
     *
     * @return tx_cfcleague_services_Group
     */
    public static function getGroupService()
    {
        return tx_rnbase_util_Misc::getService('t3sports_srv', 'group');
    }
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_ServiceRegistry.php']) {
    include_once $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cfc_league/util/class.tx_cfcleague_util_ServiceRegistry.php'];
}
