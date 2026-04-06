<?php

// config.php - Lógica pura para leer el TXT
$nodos = [];
$archivo_txt = __DIR__ . "/equipos.txt";

if (file_exists($archivo_txt)) {
    $file = fopen($archivo_txt, "r");
    $temp_data = [];
    $id_counter = 1;

    // Leemos línea por línea
    while (($line = fgetcsv($file, 1000, ",")) !== FALSE) {
        if (empty($line) || !isset($line[0])) continue;
        
        $nombre = trim($line[0]);
        if ($nombre == "") continue;

        $temp_data[$nombre] = [
            'id'     => $id_counter,
            'label'  => $nombre,
            'ip'     => isset($line[1]) ? trim($line[1]) : '',
            'level'  => isset($line[2]) ? (int)$line[2] : 0,
            'padre_nombre' => isset($line[3]) ? trim($line[3]) : '',
            'image'  => isset($line[4]) ? trim($line[4]) : 'pc.png',
			'descripcion'  => isset($line[5]) ? trim($line[5]) : 'Sin datos adicionales',
			'type' => isset($line[6]) ? trim($line[6]) : 'icmp'
        ];
        $id_counter++;
    }
    fclose($file);

    // Relacionamos padres con hijos
    foreach ($temp_data as $nombre => $data) {
        $parent_id = null;
        if (!empty($data['padre_nombre']) && isset($temp_data[$data['padre_nombre']])) {
            $parent_id = $temp_data[$data['padre_nombre']]['id'];
        }
        
        $nodos[] = [
            'id'     => $data['id'],
            'label'  => $data['label'],
            'ip'     => $data['ip'],
            'level'  => $data['level'],
            'parent' => $parent_id,
            'image'  => $data['image'],
			'title'  => 'Detalles:\n' . $data['descripcion'],
			'type'   => strtolower(trim($data['type']))
		];
    }
}
// NO pongas etiquetas HTML acá, este archivo es solo para ser "incluido".