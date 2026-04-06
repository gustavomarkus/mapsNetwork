# 🗺️ Monitor de Servicios (Mapa + TXT)

![PHP](https://img.shields.io/badge/PHP-7.x-blue)
![Status](https://img.shields.io/badge/status-active-success)
![License](https://img.shields.io/badge/license-internal-lightgrey)

Sistema simple de monitoreo de servicios HTTP/HTTPS con visualización en mapa.

Permite verificar disponibilidad de endpoints, guardar su estado en archivos `.txt` y mostrarlo en un dashboard visual tipo semáforo.

---

## 🚀 Características

- ✅ Chequeo automático de servicios (cURL) o ping segun tipo de host
- 📊 Clasificación de estado:
  - 🟢 **UP** → responde correctamente (200–399)
  - 🟡 **WARNING** → respuestas inesperadas
  - 🔴 **DOWN** → error o sin respuesta
- 📁 Persistencia en archivos `.txt` (liviano y simple)
- 🗺️ Visualización tipo mapa/dashboard
- 📧 Soporte para alertas (opcional)

---

## 📁 Estructura del proyecto
/proyecto
│
├── index.php #  Visualización tipo mapa
├── ping_status.php # Check estado, si es tipo icmp-> ping, si es tipo http-> curl
├── config.php # Configuración general
├── icons # carpeta con los iconos de red
├── equipos.txt # configuracion de las relaciones con los equipos
    Ej.: Internet,IP_GATEWAY,0,,cloud_a.png,Gateway Claro
        SDWanClaro,IP_SDWAN,1,Internet,sdwan.png
        Firewall,IP_FIREWALL,1,Internet,firewall.png
        CentralVoIP,IP_VOIP,2,Firewall,voip.png
        WSFEv1,https://servicios1.afip.gob.ar/wsfev1/service.asmx?WSDL,2,Firewall,afip.png,ServiciosAfip,http

    donde la estructura es la siguiente:
    nombre_host,IP,nodo,depende_de,icono,descripcion,tipo
      
