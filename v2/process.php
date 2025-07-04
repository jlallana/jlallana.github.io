<?php

class DB {
    private SQLite3 $db;
    public function __construct() {
        $this->db = new SQLite3('votos.sqlite');
    }

    private function query(string $sql, array $params = []): Generator {
        $stmt = $this->db->prepare($sql);

        foreach ($params as $index => $value) {
            $stmt->bindValue($index + 1, $value);
        }

        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            yield $row;
        }
    }

    private function getVotosOrdered($clave): Generator {
        if(!$clave) {
            $claveSQL = '';
        } else {
            $claveSQL = "$clave ,";
        }
        $sql = "SELECT $claveSQL orden, SUM(votos) AS votos FROM votos 
        GROUP BY $claveSQL orden
        ORDER BY $claveSQL orden";
        foreach ($this->query($sql) as $row) {
            yield $row;
        }
    }

    public function obtenerVotosAgrupados($columna_clave): Generator {
        $mesa = null;
        $votos = array_fill(1, 21, 0);

        if($columna_clave) {

            foreach ($this->getVotosOrdered($columna_clave) as $row) {
                $votos[$row['orden']] = $row['votos'];
        
                if($mesa === null) {
                    $mesa = $row[$columna_clave];
                } elseif ($mesa !== $row[$columna_clave]) {
                    yield [$columna_clave => $mesa, 'votos' => $votos];
                    $mesa = $row[$columna_clave];
                    $votos = array_fill(1, 21, 0);
                }
            }
            
            yield [$columna_clave => $mesa, 'votos' => $votos];
        } else {
            foreach ($this->getVotosOrdered($columna_clave) as $row) {
                $votos[$row['orden']] = $row['votos'];
    
            }
            yield ['votos' => $votos ];
        }
    }

    public function obtener_hijos($clave) {
        foreach($this->query("SELECT $clave FROM votos GROUP BY $clave") as $row) {
            yield $row[$clave];
        }
    }

    public function obtener_hijos_para($clave, $para, $valor) {
        foreach($this->query("SELECT $clave FROM votos WHERE $para = ?  GROUP BY $clave", [$valor]) as $row) {
            yield $row[$clave];
        }
    }
}

$database = new DB();

$resultado = [];
$baseurl = '/v2/salida';

$fuerzas = [
    1 => 'Movimiento de Integración y Desarrollo',
    2 => 'Unión del Centro Democrático',
    3 => 'Coalición Cívica – Afirmación para una República Igualitaria (ARI)',
    4 => 'Frente Patriota Federal',
    5 => 'La Libertad Avanza',
    6 => 'El Movimiento',
    7 => 'Seamos Libres',
    8 => 'La Izquierda en la Ciudad',
    9 => 'Movimiento Plural',
    10 => 'Alianza "Es Ahora Buenos Aires"',
    11 => 'Alianza "Principios y Valores"',
    12 => 'Alianza "Confluencia - Por La Igualdad y la Soberanía"',
    13 => 'Alianza "Volvamos Buenos Aires"',
    14 => 'Alianza "Evolución"',
    15 => 'Alianza "Buenos Aires Primero"',
    16 => 'Alianza "Unión Porteña Libertaria"',
    17 => 'Alianza "Frente de Izquierda y de Trabajadores- Unidad"',
    18 => 'Votos en blanco',
    19 => 'Votos impugnados',
    20 => 'Votos nulos',
    21 => 'Votos recurridos'
];

$todas_claves = [
    '' => 'seccion',
    'mesa' => '',
    'establecimiento' => 'mesa',
    'seccion' => 'establecimiento'
];


function slugify(string $text): string {
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text;
}

foreach($todas_claves as $columna_clave => $hijos) {

    foreach ($database->obtenerVotosAgrupados($columna_clave) as $fila) {
        $document = new XMLWriter();

        if(!$columna_clave) {
            $outputDir = 'salida';
        } else {
            $slug = slugify($fila[$columna_clave].'');
        
            $outputDir = "salida/$columna_clave/$slug";
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0777, true);
            }
        }

        $document->openUri(uri: "$outputDir/index.html");
        $document->startDocument('1.0', 'UTF-8');
        $document->setIndent(true);
        $document->startElement('html');

        $document->startElement('head');

        $document->startElement('link');
        $document->writeAttribute('href', $baseurl.'/style.css');
        $document->writeAttribute('rel', 'stylesheet');
        $document->writeAttribute('type', 'text/css');
        $document->endElement();

        $document->endElement();
        $document->startElement('body');

        
        if($columna_clave) {
            $document->startElement('h1');
            $document->text("$columna_clave {$fila[$columna_clave]}");
            $document->endElement();
        } else {
            $document->startElement('h1');
            $document->text('Resultados Generales');
            $document->endElement();
        }


        if($hijos) {
            $document->writeElement('h2', "POR $hijos");


            if(!$columna_clave) {
                foreach($database->obtener_hijos($hijos) as $id_hijo) {
                    $document->startElement("a");
                    $document->writeAttribute('href', $baseurl.'/'. $hijos.'/'. slugify($id_hijo));
                    $document->text($id_hijo);
                    $document->endElement();
                }
            } else {
                foreach($database->obtener_hijos_para($hijos, $columna_clave, $fila[$columna_clave]) as $id_hijo) {
                    $document->startElement("a");
                    $document->writeAttribute('href', $baseurl.'/'. $hijos.'/'. slugify($id_hijo));
                    $document->text($id_hijo);
                    $document->endElement();
                }
            }

            


        }


        $document->writeElement('h2', 'VOTOS VALIDADOS POR FUERZA POLITICA');
        
        $document->startElement('table');

        $document->startElement('thead');
        $document->startElement('tr');
        $document->startElement('th');
        $document->writeAttribute('colspan', '2');
        $document->text('AGRUPACIÓN');
        $document->endElement();

        $document->startElement('th');
        $document->text('%');
        $document->endElement();

        $document->startElement('th');
        $document->text('VOTOS');
        $document->endElement();
        $document->endElement();
        $document->endElement();
        
        
        $document->startElement('tbody');

        $totalVotos = 0;
        $maxVotos = 0;
        foreach($fila['votos'] as  $votos) {
            $totalVotos += $votos;
            if($votos > $maxVotos) {
                $maxVotos = $votos;
            }
        }


        foreach($fila['votos'] as $orden => $votos) {
            $document->startElement('tr');
            
            $document->startElement('td');
            $document->text($orden);
            $document->endElement();

            $document->startElement('td');
            $document->text($fuerzas[$orden]);
            $document->endElement();


            $document->startElement('td');
            if ($totalVotos > 0) {
                $percentage = round(($votos / $totalVotos) * 100, 2);
                $document->text($percentage . '%');
            } else {
                $document->text('0%');
            }
            $document->endElement();

            $document->startElement('td');
            $document->text($votos);
            $document->endElement();

            $document->startElement('td');
            $document->startElement('progress');
            $document->writeAttribute('value', $votos);
            $document->writeAttribute('max', $maxVotos);
            $document->endElement();
            $document->endElement();
            

            $document->endElement();

            
        }

        $document->endElement();
        $document->endDocument();
    }
}