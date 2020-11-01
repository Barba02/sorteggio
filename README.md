# Sorteggio Interrogati
Progetto PHP per l'estrazione degli interrogati personalizzando le regole di estrazione in modo più user-friendly possibile.

### Persone
| Ruolo  | Cognome Nome    |
| ------ | --------------- |
| Autore | Barbieri Filippo|
| ???    | Roin Giovanni   |

### File System
*	`include/`*
	*	`config.inc.php`
*	*`out/`*
	*	`turni-?.txt`
*	`index.php`
*	`rules.json`

### Premessa
Per definire le regole di estrazione, modificare `rules.json`.
Dopo l'estrazione il risultato sarà un file dentro la cartella *`out/`*

### Regole di Estrazione
In `rules.json` si possono personalizzare:
*	il nome della materia;
*	le giornate di interrogazione e quanti studenti per giornata;
*	le eccezioni di estrazione come gli studenti al primo turno dell'interrogazione precedente, chi ha altri impegni e in che data, ecc.;
*	L'elenco degli studenti da estrarre.

| Elemento       | Significato                                                       |
| -------------- | ----------------------------------------------------------------- |
| `subject`      | Nome della materia                                                |
| `exceptions`   | Eccezioni di estrazione                                           |
| `& > lastBegs` | Studenti al primo turno della precedente interrogazione           |
| `& > lastSubs` | Studenti sostituti al primo turno della precedente interrogazione |
| `& > studying` | Studenti già occupati con altre materie                           |
| `& > others`   | Studenti che per altri motivi non possono essere estratti         |
| `students`     | Elenco degli studenti estraibili                                  |