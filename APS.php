<!-- APS - Atividade Prática Supervisionada -->
<!-- CC4P30 -->
<!-- Integrantes: {
    Jennifer Santos Oliveira
    Igor Assis de Oliveira 
    João Carlos Pereira Alves
} -->

<?php 

$open = fopen('metadados_fotos_APS_20212.csv', 'r');
$open_bubble = fopen('bubble_sort_file_size.csv', 'w');
$open_quick = fopen('quick_sort_file_size.csv', 'w');
$open_selection = fopen('selection_sort_file_size.csv', 'w');

fputcsv($open_bubble, array("id","file_name","satellite","file_size","date_time","latitude","longitude"));
fputcsv($open_quick, array("id","file_name","satellite","file_size","date_time","latitude","longitude"));
fputcsv($open_selection, array("id","file_name","satellite","file_size","date_time","latitude","longitude"));

$index = ',';
$campo = '"';
$metadados_list = [];

$bubble_elapsed_time;
$quick_elapsed_time;
$selection_elapsed_time;
$method_filtereds = [];

if ($open) {
    $cabecalho = fgetcsv($open, 0, $index, $campo);

    while (!feof($open)) { 
        global $registro;
        $linha = fgetcsv($open, 0, $index, $campo);
        if (!$linha) {
            continue;
        }

        $metadados_list[] = array_combine($cabecalho, $linha);
    }
    fclose($open);
}

// Quick Sort

function quick_sort($array, $on) {
    global $open_quick;
    $tempo = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        foreach ($sortable_array as $k => $v) {
            fputcsv($open_quick, $array[$k]);
        }
    }
    return;
}

// Bubble Sort

function bubble_Sort($array) {
    do {
        $swapped = false;
		for( $i = 0, $c = count( $array ) - 1; $i < $c; $i++ ) {
			if( $array[$i] > $array[$i + 1] ) {
				list( $array[$i + 1]["file_size"], $array[$i]["file_size"] ) =
						array( $array[$i], $array[$i + 1] );
				$swapped = true;
			}
		}
	} while( $swapped );
    return $array;
}

// Selection Sort

function selection_Sort($sort_array) {
    for ($i = 0; $i < count($sort_array) - 1; $i++) {
        $min = $i;  
        for ($j = $i + 1; $j < count($sort_array); $j++) {
            if ($sort_array[$j]["file_size"] < $sort_array[$min]["file_size"]) {
              $min = $j;
            } 
        $temp = $sort_array[$i];
        $sort_array[$i] = $sort_array[$min];
        $sort_array[$min] = $temp;
    }
    return $sort_array;
    }
} 

function Order_by_Quick() {
    global $metadados_list;
    global $quick_elapsed_time;
    global $open_quick;
    
    $tempo = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
    quick_sort($metadados_list, 'file_size');
    $quick_elapsed_time = $tempo;
    fclose($open_quick);
}

function Order_by_Bubble() {
    global $metadados_list;
    global $bubble_elapsed_time;
    global $open_bubble;

    $tempo = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
    foreach (bubble_Sort($metadados_list) as $line) {
        fputcsv($open_bubble, $line);
    }
    $bubble_elapsed_time = $tempo;
    fclose($open_bubble);
}

function Order_by_selection() {
    global $metadados_list;
    global $selection_elapsed_time;
    global $open_selection;

    $tempo = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
    foreach (selection_Sort($metadados_list) as $line) {
        fputcsv($open_selection, $line);
    }
    $selection_elapsed_time = $tempo;
    fclose($open_selection);
}

Order_by_Bubble();
$method_filtereds[] = (object) ['name' => 'bubble', 'value' => $bubble_elapsed_time];

Order_by_Quick();
$method_filtereds[] = (object) ['name' => 'quick', 'value' => $quick_elapsed_time - $bubble_elapsed_time];

Order_by_selection();
$method_filtereds[] = (object) ['name' => 'selection', 'value' => $selection_elapsed_time - $quick_elapsed_time - $bubble_elapsed_time];

printf("\n\033[0;32mBUBBLE_SORT: %0.02f segundos\033[0m", number_format($bubble_elapsed_time, 2, '.', ' '));
printf("\n\033[0;34mQUICK_SORT: %0.02f segundos\033[0m", number_format($quick_elapsed_time, 2, '.', ' '));
printf("\n\033[1;33mSELECTION_SORT: %0.02f segundos\033[0m", number_format($selection_elapsed_time, 2, '.', ' '));

?>