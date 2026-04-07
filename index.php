<!DOCTYPE html>
<html>
<head>
    <title>NOC Empresa - Mapa de Red</title>
    <script type="text/javascript" src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
    <style>
			.container {
				display: flex;
				height: 100vh;
				width: 100%;
				gap: 30px;              /* más separación */
				padding: 20px;          /* 🔥 aire contra bordes */
				box-sizing: border-box;
			}

			.panel-externos {
				width: 28%;
				border: none;
				background: #111;
			}

			#network-map {
				width: 72%;
				height: 100%;
			}
			
			.header-monitor {
			position: absolute;
			top: 20px;
			left: 20px;
			z-index: 10;
			color: #00ff00; /* Color verde terminal */
			font-family: 'Courier New', Courier, monospace;
			background: rgba(0, 0, 0, 0.7);
			padding: 15px;
			border-left: 4px solid #00ff00;
			pointer-events: none; /* Para que no moleste si querés arrastrar el mapa debajo */
		}

		.titulo {
			font-size: 20px;
			font-weight: bold;
			letter-spacing: 2px;
			margin-bottom: 5px;
		}

		.fecha {
			font-size: 16px;
			color: #ffffff;
			opacity: 0.8;
		}
        body { margin: 0; background: #1a1a1a; color: white; }
        #network-map { height: 100vh; width: 100%; }
		/* Esto ayuda a que el tooltip respete el formato */
		.vis-tooltip {
			position: absolute;
			visibility: hidden;
			padding: 10px;
			white-space: nowrap;
			font-family: 'Courier New', monospace;
			font-size: 14px;
			background-color: #1a1a1a !important; /* Tu gris oscuro */
			color: #ffffff !important;
			border: 1px solid #00ff00 !important; /* Borde verde */
			border-radius: 4px;
			z-index: 100;
		}
    </style>
</head>
<?php
 //<div id="network-map"></div>
 
 
 
?>
<body>
	<div class="header-monitor">
		<div class="titulo">NOC - MONITOREO DE INFRAESTRUCTURA CUYOPLACAS</div>
		<div id="reloj" class="fecha">Cargando fecha...</div>
	</div>
    

	<div class="container">
		<div id="network-map"></div>
	</div>

    <script>
        var nodes = new vis.DataSet(<?php 
            include 'config.php';
            $js_nodes = [];
            foreach($nodos as $n) {
                $js_nodes[] = [
                    'id' => $n['id'],
                    'label' => $n['label'] . "\n" . $n['ip'],
                    'level' => $n['level'],
                    'shape' => 'image',
                    'image' => 'icons/' . $n['image'],
					'title' => $n['title'] 
                ];
            }
            echo json_encode($js_nodes);
        ?>);

        var edges = new vis.DataSet(<?php 
            $js_edges = [];
            foreach($nodos as $n) {
                if($n['parent'] !== null) {
                    $js_edges[] = ['from' => $n['parent'], 'to' => $n['id']];
                }
            }
            echo json_encode($js_edges);
        ?>);

        var container = document.getElementById('network-map');
        var options = {
			layout: { 
				hierarchical: { 
					enabled: true,
					direction: 'LR', 
					sortMethod: 'hubsize', 
					shakeTowards: "roots",
					levelSeparation: 300,
					nodeSpacing: 100
				} 
			},
			physics: {
			  enabled: false
			},
			tooltip: {
				delay: 300,
				fontColor: '#ffffff',
				fontSize: 14,
				fontFace: 'Courier New',
				color: {
					border: '#00ff00',
					background: '#1a1a1a'
				}
			},
			interaction: {
				hover: true, // Requerido para que el tooltip aparezca al pasar el mouse
			},
			nodes: {
				shape: 'image',
				size: 20,
				margin: 10,
				// CONFIGURACIÓN DE SOMBRA INICIAL
				shadow: {
					enabled: true,
					color: 'rgba(0,0,0,0.5)', // Sombra negra suave por defecto
					size: 20,
					x: 0,
					y: 0
				},
				font: {
					color: '#ffffff',
					size: 14,
					vadjust: 5 // Baja el texto para que no tape el resplandor
				},
				shapeProperties: {
					useImageSize: false,
					interpolation: true
				}
			},
			edges: {
				width: 2,
				smooth: { type: 'cubicBezier', forceDirection: 'horizontal', roundness: 0.5 }
			},
			// ESTILO DEL TOOLTIP
			interaction: { hover: true },
			manipulation: { enabled: false },
			tooltip: {
				delay: 300,
				fontColor: '#ffffff',
				fontSize: 14,
				fontFace: 'Courier New',
				color: {
					border: '#00ff00',
					background: '#1a1a1a' // Combinando con tu fondo oscuro
				}
			},
			physics: false
		};
        var network = new vis.Network(container, {nodes: nodes, edges: edges}, options);

        // El motor de PING (AJAX)
        function checkStatus() {
			fetch('ping_status.php')
				.then(res => res.json())
				.then(data => {
					data.forEach(item => {
						// Configuración predeterminada para equipos ONLINE (Sutil y Limpio)
						let estadoColor = '#00FF00'; // Verde sutil
						let brilloSize = 15;        // Resplandor pequeño
						let opacidadImagen = 1;     // Imagen nítida
						let bordeAncho = 0;         // Sin borde extra
						let colorTexto = '#ffffff'; // Texto blanco
						if (item.no_ip) {
							colorFinal = '#888888'; // Gris para equipos no administrables
							brilloFinal = 5;        // Casi sin brillo
						}
						// --- CONFIGURACIÓN AGRESIVA PARA EQUIPOS OFFLINE (Caídos) ---
						if (!item.online) {
							estadoColor = '#FF0000'; // Rojo Alerta saturado
							brilloSize = 60;          // Super resplandor (4 veces más grande)
							opacidadImagen = 0.5;      // Icono traslúcido para que resalte la luz trasera
							bordeAncho = 6;           // Borde grueso y sólido alrededor del icono
							colorTexto = '#ff4444';    // Texto rojo suave para la etiqueta
						}

						// Actualizamos el nodo con el "Aura de Alerta"
						nodes.update({
							id: item.id,
							opacity: opacidadImagen,
							
							// 1. Super Resplandor Rojo Trasero
							shadow: {
								enabled: true,
								color: estadoColor, 
								size: brilloSize, 
								x: 0,
								y: 0
							},
							
							// 2. Borde Grueso y Sólido (como un anillo rojo)
							color: {
								border: estadoColor, // Rojo Alerta
								background: '#1a1a1a', // Tu color de fondo oscuro
								highlight: { // Color si hacés clic
									border: estadoColor,
									background: '#330000' // Fondo rojo muy oscuro al seleccionar
								}
							},
							
							// 3. Texto en Rojo
							font: {
								color: colorTexto,
								size: item.online ? 14 : 16, // Agrandamos el texto si está caído
								//vadjust: item.online ? 45 : 45 // Bajamos el texto más si está caído
							},
							
							// 4. Forzamos el ancho del borde
							borderWidth: bordeAncho,
							borderWidthSelected: bordeAncho + 2 // Aún más grueso si se selecciona
						});
					});
				})
				.catch(err => console.error("Error visualizando alerta:", err));
		}
        setInterval(checkStatus, 30000); // Cada 30 seg
        checkStatus();
		function actualizarReloj() {
			const ahora = new Date();
			// Formato local para Mendoza (Argentina)
			const opciones = { 
				day: '2-digit', month: '2-digit', year: 'numeric', 
				hour: '2-digit', minute: '2-digit', second: '2-digit' 
			};
			document.getElementById('reloj').innerHTML = ahora.toLocaleDateString('es-AR', opciones);
		}

		// Actualizar cada segundo
		setInterval(actualizarReloj, 1000);
		actualizarReloj();
    </script>
</body>
</html>
