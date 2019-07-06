.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _knownProblems:

Known problems
==============

Keine Spielklassen
~~~~~~~~~~~~~~~~~~~~~~~~

Die Verwaltung kennt derzeit keine Spielklassen. Dies ist für den ursprünglichen Verwendungszweck einer Vereinsverwaltung auch nicht zwingend notwendig. Das Datenbankmodell kann aber prinzipiell um die Spielklassen erweitert werden.

Teamnotizen vom selben Typ pro Person
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Eine Person kann einer Mannschaft zwar als Spieler, Trainer und Betreuer gleichzeitig hinzugefügt werden, in Kombination mit Notiz-Typen gibt es aber eine Limitierung auf einen Eintrag je Noitz-Typ. Ist eine Person Spieler und Trainer und man nutzt Notiz-Typen für eine saison- und mannschaftsgebundene Dokumentantion von Positionen, so kann der Person innerhalb der Mannschaft nur eine Person zugewiesen werden. Anders lässt es das Datenmodell aktuell nicht zu. 