<?php
include 'config.php';
header('Content-Type: application/json');

function check_http($url) {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_NOBODY, true);

    curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $time     = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
    $error    = curl_errno($ch);

    curl_close($ch);

    // 🔥 LÓGICA DE ESTADO
    if ($error != 0 || $httpCode == 0) {
        $status = 'down'; // 🔴 no conecta
    }
    elseif ($httpCode >= 500) {
        $status = 'down'; // 🟠 AFIP roto funcionalmente
    }
    elseif ($httpCode >= 200 && $httpCode < 400) {
        $status = 'up'; // 🟢 OK
    }
    else {
        $status = 'warning'; // 🟡 otros casos
    }

    return [
        'code'   => $httpCode,
        'time'   => $time,
        'status' => $status
    ];
}


$resultados = [];

foreach ($nodos as $n) {
	
	$ip = trim($n['ip']);
    $type = isset($n['type']) ? strtolower(trim($n['type'])) : 'icmp';

    // =========================
    // SIN IP
    // =========================
    if (empty($ip) || strtoupper($ip) === 'N/A' || $ip === '0.0.0.0') {
        $resultados[] = [
            'id'     => $n['id'],
            'online' => true,
            'no_ip'  => true,
            'color'  => '#555555'
        ];
        continue;
    }

	// =========================
	// 🌐 HTTP (AFIP)
	// =========================
	
	if ($type === 'http') {

		$http = check_http($ip);

		$status    = $http['status'];
		$httpCode  = $http['code'];

		echo $ip . " => " . $httpCode . " => " . $status . "<br>";

		// 🎯 Definición de online REAL
		$is_online = ($status === 'up');

		// 🎨 Colores según estado
		switch ($status) {
			case 'up':
				$color = '#00FF00'; // 🟢
				break;

			case 'error':
				$color = '#FFA500'; // 🟠 AFIP roto
				break;

			case 'warning':
				$color = '#FFFF00'; // 🟡
				break;

			case 'down':
			default:
				$color = '#FF0000'; // 🔴
				break;
		}

		$resultados[] = [
			'id'        => $n['id'],
			'online'    => $is_online,
			'no_ip'     => false,
			'color'     => $color,
			'http_code' => $httpCode,
			'status'    => $status
		];

		continue;
	}
    // =========================
    // 🖧 PING NORMAL
    // =========================
    $output = [];
    $status = -1;

    exec("ping -c 1 -W 1 " . escapeshellarg($ip), $output, $status);

    $is_online = ($status === 0);

    $resultados[] = [
        'id'     => $n['id'],
        'online' => $is_online,
        'no_ip'  => false,
        'color'  => ($is_online ? '#00FF00' : '#FF0000')
    ];
}
echo json_encode($resultados);


