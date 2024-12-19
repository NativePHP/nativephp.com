---
title: Instalación
order: 100
---

# Requisitos

1. PHP 8.1+
2. Laravel 10 o superior
3. Node 20+
4. Windows 10+ / macOS 12+ / Linux

## PHP y Node

La mejor experiencia de desarrollo para NativePHP es tener PHP y Node ejecutándose directamente en tu máquina de desarrollo.

Si estás usando Mac o Windows, la forma más sencilla de tener PHP y Node funcionando en tu sistema es con
[Laravel Herd](https://herd.laravel.com). ¡Es rápido y gratis!

Ten en cuenta que, aunque es posible desarrollar y ejecutar tu aplicación desde un entorno virtualizado o contenedor,
puedes encontrarte con más problemas inesperados y tener más pasos manuales para crear compilaciones funcionales.

## Laravel

NativePHP está diseñado para funcionar mejor con Laravel. Puedes instalarlo en una aplicación Laravel existente, o
[comenzar una nueva](https://laravel.com/docs/installation).

## Instalar un runtime de NativePHP

```shell
composer require nativephp/electron
```

El runtime de Tauri estará disponible pronto.

## Ejecutar el instalador de NativePHP

```shell
php artisan native:install
```

El instalador de NativePHP se encarga de publicar el proveedor de servicios de NativePHP, que inicializa las dependencias necesarias para que tu aplicación funcione con el runtime que estás usando: Electron o Tauri.

También publica el archivo de configuración de NativePHP en `config/nativephp.php`.

Agrega el script `composer native:dev` a tu `composer.json`, que puedes modificar según tus necesidades.

Finalmente, instala cualquier otra dependencia necesaria para el runtime específico que estés usando, por ejemplo, para Electron instala las dependencias de NPM.

**Siempre que configures NativePHP en una nueva máquina o en CI, debes ejecutar el instalador para asegurarte de que todas las dependencias necesarias estén en su lugar para construir tu aplicación.**

## Iniciar el servidor de desarrollo

**¡Atención!** Antes de iniciar tu aplicación en un contexto nativo, intenta ejecutarla en el navegador. Puedes encontrarte con excepciones que necesitan ser solucionadas antes de que puedas ejecutar tu aplicación de forma nativa, y pueden ser más difíciles de detectar al hacerlo.

Una vez que estés listo:

```shell
php artisan native:serve
```

¡Y eso es todo! Ahora deberías ver tu aplicación Laravel ejecutándose en una ventana de escritorio nativa. 🎉
