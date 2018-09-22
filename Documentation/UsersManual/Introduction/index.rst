.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _introduction:

Einführung
================
Um die Extension sinnvoll einsetzen zu können, sollte man sich zunächst etwas mit dem zu Grunde liegenden Datenmodell vertraut machen. Dabei wird in diesem Dokument der Einfachheit halber, immer von der Verwaltung von Fußball-Ligen ausgegangen.

Wenn man Informationen über Fußballspiele sammelt, und diese dann in Relation zu einander führt, erhält man recht schnell ein doch recht komplexes Datenmodell. Denken wir nur daran, das Vereine über Jahre hinweg in verschiedenen Wettbewerben spielen, jedes Jahr neue Spieler rekrutieren und andere abgeben und natürlich auch in verschiedenen Altersgruppen mit Mannschaften antreten. Für jeden Wettbewerb existieren Spielpläne, die nach unterschiedlichen Regeln erzeugt werden müssen und wenn man letztendlich bei einem konkreten Spiel angekommen ist, setzt noch eine zusätzliche Datenflut ein. Da müssen die Aufstellungen der Teams, die Trainer und Schiedsrichter gesetzt werden. Neben den Ergebnissen wollen die gelben und roten Karten dokumentiert sein, ganz zu schweigen von Auswechslungen, Chancen, Eckbällen, Torschützen... Die Liste läßt sich sicher noch endlos fortführen. Aber bevor große Ängste, ob der notwendigen Arbeit aufkommen, die meisten dieser Daten können, müssen aber nicht gepflegt werden.

Die Aufgabe bei der Entwicklung bestand nun darin, ein Datenbankmodell zu entwickeln, das einerseits flexibel genug ist, all diese Daten aufzunehmen, welches anderseits aber nicht ins Uferlose ausarten sollte. Die folgende Grafik gibt einen Überblick über das so entstandene Modell. Bis auf Wettbewerbstyp und Spielrunde existiert für alle aufgeführten Entitäten eine entsprechende Tabelle in der Datenbank.

|img-UsersManual-databaseModell|

Die einzelnen Datenklassen sollen jetzt noch kurz vorgestellt werden.

*Saison*
Da wir die Daten über längere Zeiträume hinweg erfassen und auswerten wollen, wird natürlich ein Datentyp für diesen zeitlichen Aspekt benötigt. Die Saison ist letztendlich aber nur eine Klammer über verschiedene Wettbewerbe die später für Ordnung bei Auswertungen aber auch den Dateneingaben sorgt.

*Altersgruppe*
Auch die Altersgruppe wurde eingeführt, um zusätzliche Ordnung in die Daten zu bringen. Damit wird es möglich, daß ein Verein mit mehreren Teams in einer Spielzeit antreten kann. Dabei ist der Begriff “Altergruppe” nur bedingt richtig, da beispielsweise auch Zweit- oder Drittmannschaften im Männerbereich als eigene Altergruppe angelegt werden müssen. Selbiges gilt auch für den Spielbetrieb im Frauenbereich.

*Verein und Team*
Mit diesen beiden Datentypen wurde die folgende Annahme abgebildet: Jeder Verein stellt in jeder Saison ein oder mehrere neue Teams auf und läßt es in Wettbewerben antreten. Die Mannschaft ändert sich also jedes Jahr, der Verein bleibt aber der selbe. Daher nimmt der Verein quasi die Stammdaten auf, während das Team die variablen Daten abbildet. Es ist sehr wichtig, daß vor jeder neuen Saison neue Team- Datensätze angelegt werden. Dies soll das folgende Beispiel verdeutlichen:

Im Team der Saison 2005/06 stehen 18 Spieler im Kader, darunter Heiner Hinkebein, der Topstürmer der Mannschaft. In der Saison 2006/07 wird Hinkebein an einen anderen Club verkauft. Der Liga-Administrator könnte jetzt auf die Idee kommen, sich Arbeit zu ersparen und in der neuen Saison einfach das schon vorhandene Team aus der Vorsaison zu verwenden und Hinkebein aus dem Kader zu löschen. Großer Fehler! Zwar würde beispielsweise im Frontend in der Teamübersicht für 2006/07 Hinkebein nicht mehr aufgeführt, allerdings wäre er nun auch 2005/06 nicht mehr zu sehen. Und das war definitiv nicht gewollt. Natürlich wäre er in den Jahr auch aus allen Statistiken entfernt worden, von den Inkonsistenzen in der Datenbank mal ganz zu schweigen.

Daher: Sämtliche Teams müssen in jeder Saison neu angelegt werden! Einem Kopieren des Vorjahresteam steht natürlich nichts im Wege.

Eine Ausnahme gibt es noch für das Spezialteam “Spielfrei”. Es handelt sich dabei im einen normalen Team-Datensatz, bei dem jedoch das Attribut “spielfrei” markiert ist. Dieses Team wird verwendet, um spielfreie Spieltage für Teams zu kennzeichnen. Wenn eine Mannschaft ein Spiel gegen dieses Team bestreitet, dann hat es automatisch an diesem Spieltag frei. Dieses Spezialteam muss nur einmal angelegt werden und kann dann immer verwendet werden. Bei der Generierung einer Liga mit ungerader Mannschaftsanzahl, nimmt dieses Team den Platz des fehlenden Teams ein.

*Wettbewerb und Wettbewerbstyp*
Jede Mannschaft tritt in einer Saison üblicherweise in verschiedenen Wettbewerben an. Da wären natürlich der normale Ligabetrieb, Pokalwettbewerbe oder auch Hallenturniere. Auch Freundschaftspiele kann man als eigenen “Wettbewerb” betrachten. Der Wettbewerbstyp Liga spielt eine besondere Rolle. Denn für eine Liga wird als Auswertung eine Tabelle benötigt. Auch können Tabellenfahrten oder Spielpläne erstellt werden, die in einem Pokalwettbewerb keinen Sinn ergeben. Der Wettbewerbstyp ist kein zusätzliches Datenobjekt, sondern lediglich ein Attribut im Datentyp Wettbewerb .

*Profile*
Dieser Datentyp bildet Personen ab, die Einfluß auf den Spielbetrieb haben. Zuallererst seien hier natürlich die Spieler genannt, aber auch Trainer, Schiedsrichter oder Funktionäre können angelegt werden. All diese Personen werden im Datentyp “Profile” abgebildet. Wichtig werden dann die Zuordnungen, die wir für die angelegten Profile treffen. So müssen wir zunächst die angelegten Spieler- und Trainerprofile einem Team zu orden. Dieser Vorgang sollte zu Beginn einer Saison stattfinden. Wir stellen somit den eigentlichen Teamkader zusammen. Da diese Zuordnung recht aufwendig ist, bietet das Modul Ligamanagement die Funktion “Spieler anlegen” an. Mit dieser können sehr schnell die Profile erstellt und direkt einem Team zugeordnet werden.

Es ist sehr wichtig, daß jede Person nur einmal im System angelegt wird. Denn ein Spieler kann natürlich in verschiedenen Teams spielen. Er gehört jedes Jahr einem oder auch mehreren Teams an. Letzteres ist beispielsweise der Fall, wenn er in einer Saison Einsätze in der 1. und 2. Mannschaft eines Vereins hat. Nur wenn der Spieler einmal im System vorkommt, können sinnvolle statistische Auswertungen für diesen Spieler erstellt werden.

*Die Spiele*
Das eigentliche Herzstück der Ligaverwaltung sind natürlich die Spiele. Jeder Spieldatensatz ist genau einem Wettbewerb zugeordnet. Da wir im Wettbewerb schon die Teams festgelegt haben, können wir im Spieldatensatz nur noch zwischen den entsprechenden Teams wählen. Ein langes Suchen entfällt also. Trotzdem wäre die Spielplanerstellung für eine Liga natürlich sehr zeitraubend. Da der DFB bei der Erstellung der Spielpläne aber nach einem festen Schlüsselsystem vorgeht, kann dieser Schlüssel zur Erstellung des Spielplans genutzt werden. Der Schlüssel muss im Liga-Datensatz festgelegt werden. Wenn dann die Reihenfolge der Team richtig gesetzt ist, kann der gesamte Spielplan mit einem Mausklick erstellt werden.

In jedem Spiel müssen auch die Startformationen der Teams angegeben werden. Da wir bereits wissen, über welche Spieler die einzelnen Teams verfügen, bekommen wir in dem entsprechenden Formular direkt alle verfügbaren Spieler der Mannschaften angezeigt. Mit elf (bzw. 22) Mausklicks sind dann die Aufstellungen erledigt.

Zusätzlich ist natürlich noch Platz für einen Spielbericht und ein paar Bilder.

*Spielnotizen*
Im Laufe eines Spiels passieren viele interessante Dinge. Es werden Tore geschossen, gelbe und rote Karten verteilt, Spieler ausgewechselt, Elfmeter verschossen, der Trainer auf die Tribüne verwiesen, die Werbebande fällt um.... Die Liste läßt sich je nach Geschmack beliebig fortsetzen. All diese Dinge sind aus zwei Gründen interessant: Erstens können wir sie nutzen, um einen Liveticker des Spiels zu zeigen. Zweitens können wir aber interessante Statistiken mit diesen Informationen erstellen. Wenn wir all diese Informationen auf einen gemeinsamen kleinsten Nenner bringen, dann erhalten wir den Datentyp “Spielnotiz”. Dieser enthält die Spielminute, eine Typ der Aktion, den beteiligten Spieler und einen Kommentar. Je nach Aktion müssen natürlich nicht alle Angaben gemacht werden. Pflichtfelder sind lediglich die Spielminute und der Typ.

Die Typen sind derzeit fest vorgeben, eine spätere Erweiterung ist aber nicht ausgeschlossen.