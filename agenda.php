<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/library.php';

$agenda = [];
$dates = [];
$timetable = []; 

$json = file_get_contents('http://site.com/events/web/index.php?r=api/events');
$obj = json_decode($json);

$library = new Library();

foreach ($obj->eventos as $evento) {  
    foreach ($evento as $data) {
        if (!$library->in_array_r($data->evento->id, $agenda))   {
            $newDate = date("m/d/Y", strtotime($data->fecha));  
            array_push($agenda, [$newDate, '', $data->hora_ini.' / '.$data->hora_fin, $data->evento->id,
                $data->evento->titulo, $data->evento->descripcion, 
                'http://www.cedermerindades.com/eventos/web/uploads/eventos/'.$data->evento->imagen,
                $data->evento->ubicacion->nombre, $data->evento->ubicacion->direccion, 
                'POINT('.$data->evento->ubicacion->longitud.' '.$data->evento->ubicacion->latitud.')',
                $data->evento->ubicacion->telefono, $data->evento->ubicacion->localidad->municipio->nombre            
            ]);            
        } else {          
                        
            if (!$library->in_array_r($data->evento->id, $dates)) {
                $newDateFin = date("m/d/Y", strtotime($data->fecha));
                array_push($dates,['id' => $data->evento->id, 'date' => $newDateFin]);
            } else {
                $pos = $library->getPosition();
                $newDateFin = date("m/d/Y", strtotime($data->fecha));
                $dates[$pos]['date'] = $newDateFin;
            }  
            
            $hour = $data->hora_ini.' / '.$data->hora_fin;
            if (!$library->in_array_r($data->evento->id, $timetable)) {
                array_push($timetable,['id' => $data->evento->id, 'hours' => $hour]);
            } else if (!$library->in_array_r($hour, $timetable)) {
                array_push($timetable,['id' => $data->evento->id, 'hours' => $hour]);
               
            }

        } 
    }
}

// write end date in array
$i = 0;
foreach ($agenda as $data) {
    if($library->in_array_r($data[3], $dates)) {
        $agenda[$i][1] = $dates[$library->getPosition()]['date'];
    }

    if($library->in_array_r($data[3], $timetable)) {
        $pos = $library->getPosition();
        if($agenda[$i][2] != $timetable[$pos]['hours']) {
            $description = $agenda[$i][5];
            $agenda[$i][5] = $description.' - '.$timetable[$pos]['hours'];
        }
    }  
    $i++;
}

/*echo '<br><br>';
print_r($agenda);
echo '<br><br>';
echo count($agenda);*/

$fp = fopen('eventos.csv', 'w');

foreach ($agenda as $items) {
    fputcsv($fp, $items);
}

fclose($fp);
