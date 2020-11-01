<?php
  setlocale(LC_ALL, 'it_IT.utf8');

  /** Definizione: Percorso di rules.json */
  define('RULES_PATH', realpath(__DIR__ . '/../rules.json'));
  /** Definizione: Percorso di out/ */
  define('OUTPUT_PATH', realpath(__DIR__ . '/../out'));

  /** Regole di estrazione */
  $rules = file_get_contents(RULES_PATH);
  $rules = json_decode($rules, true);

  /**
   * Prepara un file dedicato alla scrittura del
   * risultato dell'estrazione.
   * 
   * @return resource|false
   */
  function prepare() {
    # Inclusione di $rules
    global $rules;

    # Nome della materia
    $subject = $rules['subject'];
    # Primo segmento della materia
    # utlizzato nella composizione del nome
    $first = strtolower(explode(' ', $subject)[0]);

    # Composizione del nome
    $name = OUTPUT_PATH . "/turni-$first.txt";

    # Istanziazione del file
    return fopen($name, 'w+');
  };

  /**
   * Estrae casualmente uno studente
   * dall'elenco.
   * 
   * @return string
   */
  function randomStud() {
    # Inclusione di $rules
    global $rules;

    # Estrazione casuale
    return $rules['students'][rand(0, count($rules['students']) - 1)];
  };

  /**
   * Formatta una stringa rappresentante
   * una data, in formato [D d/M] (n).
   * 
   * @param string $string
   * 
   * @return string
   */
  function format($string, $n) {
    # Istanziazione della Data
    $date = date_create($string);

    # Verifica che la data sia
    # stata istanziata
    if ($date === false) {
      return '';
    }

    # Formattazione es Thu
    $day = date_format($date, 'D');
    # Formattazione es 25/12
    $num = date_format($date, 'd/m');

    return "[$day $num] ($n)";
  };

  /**
   * Restituisce il numero del giorno
   * nell'anno partendo da una stringa
   * rappresentante una data.
   * 
   * @param string $string
   * 
   * @return integer
   */
  function stringToDay($string) {
    # Istanziazione della data
    $date = date_create($string);

    # Verifica che la data sia
    # stata istanziata
    if ($date === false) {
      return 0;
    }

    # Formatta la data
    return (int) date_format($date, 'z');
  };

  /**
   * Restituisce gli studenti che non possono
   * essere estratti in determinati giorni.
   * 
   * @return mixed
   */
  function getExcept() {
    # Inclusione di $rules
    global $rules;

    # Elenco di studenti già impegnati
    $exc = [];

    # Verifica se ogni turno collide con
    # altre interrogazioni
    foreach ($rules['rounds'] as $ir => $round) {
      # Giorno dell'anno
      $day = stringToDay($round['date']);

      foreach ($rules['exceptions']['studying'] as $ie => $engage) {
        $otr = stringToDay($engage['date']);

        if ($day > $otr - 3 and $day < $otr + 3) {
          # Tutti gli studenti in elenco non possono essere estratti
          # nella data indicata
          array_push($exc, $engage);
        }
      }

      # Verifica se ogni turno collide con 
      # altri impegni
      foreach ($rules['exceptions']['others'] as $io => $other) {
        if ($round['date'] === $other['date']) {
          array_push($exc, $other);
        }
      }
    }

    # Ordinamento crescente
    sort($exc);

    return $exc;
  }

  /**
   * Estrae gli interrogati e scrive il risultato
   * su file.
   * 
   * @param resource $file
   */
  function execute($file, $verbose = false) {
    # Inclusione di $rules
    global $rules;

    # Elenco di studenti con impegni
    # in determinate date
    $exc = getExcept();
    # Elenco risultato dell'estrazione
    $res = [];

    if ($verbose) {
      echo 'Interrogazione di ' . $rules['subject'] . "<br /><br />";
    }

    fwrite($file, 'Interrogazione di ' . $rules['subject'] . "\n\n");

    # Estrazione per ogni turno
    foreach ($rules['rounds'] as $ir => $round) {
      # Numero degli estratti del turno
      $num = 0;

      # Stringa da stampare in output
      $fmt = format($round['date'], $ir + 1) . ': ';

      # Estrazione di un turno
      while ($num < $round['students']) {
        $random = randomStud();

        # Se l'estratto è già presente, non può essere
        # resinserito nell'elenco
        if (in_array($random, $res)) {
          continue;
        }

        # Se l'estratto si trovava in prima posizione (primo interrogato)
        # o in seconda (primo sostituto) non può essere inserito nei primi due
        # turni di interrogazione
        if ($ir === 0 and in_array($random, $rules['exceptions']['lastBegs']) or
            $ir === 1 and in_array($random, $rules['exceptions']['lastSubs'])) {
          continue;
        }

        # Verifica se nella data del turnoc
        # l'estratto ha altre interrogazioni
        foreach ($exc as $ie => $except) {
          if ($round['date'] === $except['date'] and
              in_array($random, $except['students'])) {
            continue;
          }
        }

        # Formattazione dei nomi
        $fmt .= str_pad($random, 12) . '| ';

        # Aggiunta dell'estratto nell'elenco
        array_push($res, $random);
        $num ++;
      }

      # Output
      if ($verbose) {
        echo "$fmt<br />";
      }

      fwrite($file, "$fmt\n");
    }

    # Chiusura
    fclose($file);

    if ($verbose) {
      echo "Finito.<br />";
    }
  };
