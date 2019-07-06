.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _withoutplay:

Spielfrei
=========

Spielfrei
~~~~~~~~~

Wenn eine Liga aus einer ungeraden Anzahl von Mannschaften besteht,
dann hat logischerweise an jedem Spieltag ein Team spielfrei. Seit der
Version 0.2.0 wird dieser Umstand auch von der Ligaverwaltung aktiv
unterstützt. Vom Prinzip her bestreitet eine Mannschaft die spielfrei
hat eine Begegnung gegen ein **Spezialteam**\ **Spielfrei**. Diese
Spiele werden also nicht einfach weggelassen, sondern es wird
tatsächlich ein neuer Spieldatensatz angelegt. Dadurch wird es leichter,
die spielfreie Mannschaft später im Frontend zu verarbeiten.

Ligaerstellung mit ungerader Teamanzahl
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Bei der Erstellung der Liga mit dem Spielplanschlüssel des DFB wird
einfach der nächst höhere Schlüssel verwendet. Bei 11 Teams erfolgt die
Erstellung also mit dem 12er Schlüssel. Der freie Platz muss nun
allerdings mit einem zusätzlichen Team gefüllt werden.

|img-screenshot-withoutPlay-01|

Illustration 5: Der Name des Teams sollte sinnvoll vergeben werden

Der erste Schritt ist also die Erstellung dieser neuen
Mannschaft. Dies kann beispielsweise über das Modul Liste erfolgen. Es
ist egal in welcher Seite dieser Datensatz angelegt wird. Da dieses Team
nur ein Dummy-Datensatz ist, kann man es durchaus in verschiedene
Wettbewerben wiederverwenden, auch in anderen Spielzeiten.

|img-screenshot-withoutPlay-02|

Illustration 6: Nicht vergessen dieses Häkchen zu setzen!

Der Name des Teams ist frei wählbar. Er kann dann später bei Bedarf auch im
Frontend ausgegeben werden. Ganz wichtig ist die Kennzeichnung der
Mannschaft als spielfrei. Dafür gibt es ganz am Ende des Datenblatt ein
entsprechendes neues Flag.

Wie im Tutorial beschrieben, kann man jetzt den neuen Spielplan
erstellen. Das neue Team Spielfrei nimmt dabei den freien Platz ein.

In unteren Ligen gibt es auch die Situation, daß bei geringer
Staffelbesetzung mehrere Teams an einem Spieltag spielfrei haben. Hier
muss man zunächst anhand der Anzahl der Spieltage ermitteln, welcher
Spielplanschlüssel verwendet wurde. Besteht die Staffel zum Beispiel aus
10 Teams und es sind aber 34 Spieltage angesetzt, dann wurde vermutlich
ein 16er Schlüssel verwendet. In diesem Fall müssten 6 Spielfrei-Teams
angelegt werden, um die freien Plätze zu besetzen.
