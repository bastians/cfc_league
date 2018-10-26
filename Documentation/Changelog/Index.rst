.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. _changelog:

Changelog
============

.. only:: html

	v1.2.0 (11.07.2018)
	-------------------

	-  Support for TYPO3 4.5 LTS dropped
	-  Modifications for TYPO3 8.7
	-  Many Bugfixes for TYPO3 7.6
	-  Support for PHP 7.x
	-  Code cleanup
	-  digedag/cfc_league_fe#41 New feature: Match fixture sync with DFBnet
	-  digedag/cfc_league_fe#25 Support for handball, but not yet finished
	-  Many thanks to Mario Näther for contributions!

	v1.1.1 (04.01.2017)
	-------------------

	-  composer.json added
	-  Icons and language files moved to Resources folder

	v1.1.0 (23.12.2015)
	-------------------

	-  Modifications for TYPO3 7.6
	-  #23 Ticker-Module updated by Mario Näther
	-  BE modules refactored

	v1.0.2 (03.02.2015)
	-------------------

	-  Many bugfixes for TYPO3 6.2
	-  Up to 60 players allowed as team member
	-  Bugfix for match status in ticker form

	v1.0.1 (06.09.2014)
	-------------------

	-  Team schedule view in BE for TYPO3 6.2 fixed

	v1.0.0 (26.04.2014)
	-------------------

	-  Support for TYPO3 6.2
	-  Team formations extensible by configuration
	-  Changes to apply code conventions

	v0.9.1 (01.06.2013)
	-------------------

	-  Support for TYPO3 6.x with FAL

	v0.9.0 (12.01.2013)
	-------------------

	-  Refactoring in BE module
	-  New method isSetBased in ISport
	-  Displaying set results field in BE module

	v0.8.4 (08.12.2012)
	-------------------

	-  #59: Make it possible to create matches for second half of saison
	only
	-  new image fields for agegroups
	-  models_Match: new method loadMatchNotes()
	-  srv_Profile: new method loadProfiles()
	-  Competition: Tournament field added but still not used

	v0.8.3 (14.01.2012)
	-------------------

	-  Competition record: sports is now an update field

	v0.8.2 (21.12.2011) (not released)
	----------------------------------

	-  New field in competition for sports selection
	-  Avoid t3lib_SpriteManager for TYPO4 4.3 and older.
	-  TCA config for point_system changed from radio to select, since radio
	can’t handle itemsprocfunc.
	-  tx_cfcleague_search_Match: ignore deleted and hidden competitions and
	teams

	v0.8.1 (17.10.2010)
	-------------------

	-  New hook in merge profiles
	-  #46: Remove profiles from team
	-  #47: It is possible to calculate end result from results of match
	parts.
	-  Some new fields for clubs
	-  #19: New BE form to manually create matches
	-  #19: New team mode to edit matches in BE
	-  New video field if extension rgmediaimages is installed
	-  Avoid deletion of profiles with references to other records
	-  New BE module to manage clubs and stadiums

	v0.8.0 (21.10.2010)
	-------------------

	-  BE module refactoring

	v0.7.7 (26.09.2010)
	-------------------

	-  #39: Sort order of clubs can be changed to name

	v0.7.6 (16.09.2010)
	-------------------

	-  New method getInstance in tx_cfcleague_models_Profile
	-  Register new match notes with
	tx_cfcleague_util_Misc::registerMatchNote()

	v0.7.5 (13.09.2010) (not released)
	----------------------------------

	-  Models and services extended for statistics integration
	-  Requirement of lib/div removed

	v0.7.4 (03.09.2010)
	-------------------

	-  Quick input field for liveticker in TYPO3 4.4 works again
	-  TeamNotes for coaches and supporters possible
	-  Bugfix tx_cfcleague_models_Competition::getGroup()

	v0.7.3 (17.07.2010)
	-------------------

	-  BE modul CSS styles fixed for TYPO3 4.4
	-  Matches: New field sets for set results

	v0.7.2 (04.07.2010)
	-------------------

	-  Quick input field