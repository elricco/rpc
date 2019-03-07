# Print Configurator

Beim installieren des AddOns werden Tabellen in den YForm Table Manager importiert.
Diese sind anschließend mit Daten zu befüllen.

Beispieldaten finden sich im Ordner `weissnochnicht` des AddOns.

Ebenfalls werden Assets installiert. Damit der verwendete Slider [noUiSlider](https://refreshless.com/nouislider/)
genutzt werden kann, muss die `print_configurator.css` in das Template eingebunden werden.

Ebenfalls benötigt wird die `print_configurator.js` - diese stellt den Datenaustausch per `rex_api` sicher. Ob es für
jedes Modul eine eigene JavaScript Datei gibt, ist noch nicht sicher.

Anschließend müssen vier Module angelegt werden. Der Input sowie Output der jeweiligen Module befindet sich im Ordner
`modules`. Diese Module müssen dann über eine Strecke von vier Artikeln verteilt werden. z.B. `Calculator`, `Optionen`,
`Zahlung und Lieferung`,`Zusammenfassung`. Die Einstellung der Module ist simpel, da nur jeweils der nächste, respektiv,
der vorhergehende Artikel zu verlinken ist.
