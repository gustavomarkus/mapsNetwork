# 🗺️ Monitor de Servicios (Mapa + TXT)

![PHP](https://img.shields.io/badge/PHP-7.x-blue)
![Status](https://img.shields.io/badge/status-active-success)
![License](https://img.shields.io/badge/license-internal-lightgrey)

Sistema simple de monitoreo de servicios HTTP/HTTPS con visualización en mapa.

Permite verificar disponibilidad de endpoints, guardar su estado en archivos `.txt` y mostrarlo en un dashboard visual tipo semáforo.

---

## 🚀 Características

- ✅ Chequeo automático de servicios (cURL)
- 📊 Clasificación de estado:
  - 🟢 **UP** → responde correctamente (200–399)
  - 🟡 **WARNING** → respuestas inesperadas
  - 🔴 **DOWN** → error o sin respuesta
- 📁 Persistencia en archivos `.txt` (liviano y simple)
- 🗺️ Visualización tipo mapa/dashboard
- ⏱️ Integración con cron
- 📧 Soporte para alertas (opcional)

---

## 📁 Estructura del proyecto

