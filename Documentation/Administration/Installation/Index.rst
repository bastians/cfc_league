.. include:: ../../Includes.txt

.. _installation:

Installation
============

Die Installation erfolgt wie gewohnt über den Extension Manager. Für den Betrieb der Extension ist die folgende Extension notwendig:

* rn_base
* DAM (optional bei älteren Installationen)

Der Extension Manager von TYPO3 weist ggf. auf fehlende oder nicht aktuelle Extensions hin. Diesen Hinweisen sollte man immer nachkommen. Andernfalls kann es zu schwerwiegenden Problemen kommen. Bis hin zu dem Zustand, dass keine Anmeldung im TYPO3 BE mehr möglich ist!

**Verwendung von T3sports ohne DAM**

Mit der Version 0.8.0 kann T3sports optional auch ohne DAM verwendet werden. Allerdings gelten dann einige Einschränkungen bei der Verwendung von Logos. Die Zuordnung von Logos muss immer direkt im Team-Datensatz erfolgen. Der Fallback auf das Vereinslogo ist nicht möglich. Bei den Team-Notizen ist ebenfalls keine Bildzuordnung möglich.

**Verwendung von T3sports mit FAL**

Mit der Version 0.9.1 wurde T3sports für die Verwendung von TYPO3 6.x angepaßt. Anstatt DAM wird nun FAL vollständig von T3sports unterstützt.

Letzte Version von Git
-----------------------

Man kann die neueste Version von git mit folgendem Befehl abrufen:

.. code-block:: bash

    git clone git@github.com:digedag/cfc_league.git

FAQ
---

**Was bedeutet die Fehlermeldung TYPO3 Fatal Error: Extension key "rn_base" was NOT loaded! (t3lib_extMgm::extPath)?**

Wenn diese Meldung erscheint, dann wurde die entsprechende Extension nicht in TYPO3 installiert. Der Extension-Manager weist bei der Installation immer auf die notwendigen Extensions hin. Diese Hinweise dürfen NIEMALS ignoriert werden! Unter Umständen lassen sich dadurch weder das Backend noch das Frontend ansprechen.
Sollte dieser Fall einmal eintreten, dann müssen in der Datei typo3conf/localconf.php manuell die Extensionkeys von T3sports (cfc_league und cfc_league_fe) wieder entfernt werden. Anschließend im Verzeichnis typo3conf alle Dateien, die mit temp_CACHED beginnen, löschen. Danach sollte das Backend wieder zugänglich sein. Vor dieser Aktion sollte eine Sicherheitskopie der Datei localconf.php angelegt werden!

**Fatal error: Call to undefined method tx_classname::method() in /path/to/typo3conf/ext/cfc_league/file.php on line 123**

Diese Meldung bedeutet, daß eine bestimmte Funktionalität in einer PHP-Datei nicht gefunden wurde. Meistens liegt es daran, daß eine veraltete Version einer abhängigen Extension verwendet wird. Mit dem ExtensionManager sollte überprüft werden, ob neuere Versionen vorliegen. Meistens sind die Extensions rn_base oder cfc_league betroffen.