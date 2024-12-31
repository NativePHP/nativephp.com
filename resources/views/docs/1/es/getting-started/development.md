---
title: Desarrollo
order: 300
---

# Desarrollo

```shell
php artisan native:serve
```

NativePHP no es prescriptivo sobre cómo desarrollar tu aplicación. Puedes construirla de la manera en que te sientas más cómodo y familiar, tal como si estuvieras construyendo una aplicación web tradicional.

La única diferencia radica en el ciclo de retroalimentación. En lugar de cambiar y actualizar tu navegador, necesitarás estar sirviendo tu aplicación usando `php artisan native:serve` y refrescando (y en algunos casos reiniciando) tu aplicación para ver los cambios.

Esto se conoce como 'ejecutar una compilación de desarrollo'.

## ¿Qué hace el comando `native:serve`?

El comando `native:serve` ejecuta los comandos de 'compilación de depuración' de Electron/Tauri, que construyen tu aplicación con varias opciones de depuración configuradas para facilitar la depuración, como permitirte mostrar las herramientas de desarrollo en la vista web incrustada.

También mantiene la conexión con la terminal abierta para que puedas ver e inspeccionar la salida útil de tu aplicación, como los registros, en tiempo real.

Estas compilaciones no están firmadas y no están destinadas a la distribución. No pasan por varias optimizaciones que típicamente se realizan cuando [construyes tu aplicación para producción](/docs/publishing) y, por lo tanto, exponen más sobre el funcionamiento interno del código de lo que normalmente querrías compartir con tus usuarios.

Una parte importante del proceso de compilación, incluso para compilaciones de depuración, implica _copiar_ el código de tu aplicación en el entorno de compilación del runtime. Esto significa que los cambios que realices en el código de tu aplicación _no_ se reflejarán en tu aplicación en ejecución hasta que la reinicies.

Puedes detener el comando `native:serve` presionando `Ctrl-C` en tu teclado en la ventana de la terminal donde se está ejecutando. También se terminará cuando cierres tu aplicación.

## Recarga en caliente

La recarga en caliente es una característica increíble para ver automáticamente los cambios en tu aplicación durante el desarrollo. NativePHP
soporta la recarga en caliente de ciertos archivos dentro de su núcleo y tu aplicación, pero _no_ observa todo tu código fuente en busca de cambios. Depende de ti determinar cómo quieres abordar esto.

Si estás usando Vite, la recarga en caliente funcionará dentro de tu aplicación siempre y cuando hayas iniciado tu servidor de desarrollo de Vite y
[hayas incluido la etiqueta de script de Vite](https://laravel.com/docs/vite#loading-your-scripts-and-styles) en tus vistas
(idealmente en el archivo de diseño principal de tu aplicación).

Puedes hacer esto fácilmente en Blade usando la directiva `@@vite`.

Luego, en una sesión de terminal separada a tu `php artisan native:serve`, desde la carpeta raíz de tu aplicación, ejecuta:

```shell
npm run dev
```

Ahora los cambios que realices en los archivos de tu código fuente causarán una recarga en caliente en tu aplicación en ejecución.

Qué archivos desencadenan recargas dependerá de tu configuración de Vite.

## `composer native:dev`

Puedes encontrar conveniente el script `native:dev`. Por defecto, está configurado para ejecutar tanto `native:serve` como `npm run dev` de manera concurrente en un solo comando:

```shell
composer native:dev
```

Puedes modificar este script según tus necesidades. Simplemente edita el comando en la sección de scripts de tu `composer.json`.

## Primera ejecución

Cuando tu aplicación se ejecuta por primera vez, ocurren varias cosas.

NativePHP:

1. Creará la carpeta `appdata` - dónde se crea depende de la plataforma en la que estés desarrollando. En desarrollo, se nombra según tu `APP_NAME`.
2. Creará una base de datos SQLite `nativephp.sqlite` en tu carpeta `database`.
3. Migrará esta base de datos.

La estructura de `appdata` es idéntica a la creada por las compilaciones _de producción_ de tu aplicación, pero cuando se ejecuta en desarrollo, la base de datos creada allí _no_ se migra.

**Si cambias tu `APP_NAME`, se creará una nueva carpeta `appdata`. No se eliminarán archivos anteriores.**

## Ejecuciones subsecuentes

En desarrollo, tu aplicación no ejecutará migraciones de la base de datos `nativephp.sqlite` por ti. Debes hacerlo
manualmente:

```shell
php artisan native:migrate
```

Para más detalles, consulta la sección de [Bases de Datos](/docs/digging-deeper/databases).

## Icono de la Aplicación

Los comandos `native:serve` y `native:build` buscan los siguientes archivos de iconos al construir tu aplicación:

- `public/icon.png` - tu icono principal, utilizado en el Escritorio, Dock y el conmutador de aplicaciones.
- `public/IconTemplate.png` - utilizado en la Barra de Menú en pantallas no retina.
- `public/IconTemplate@2x.png` - utilizado en la Barra de Menú en pantallas retina.

Si alguno de estos archivos existe, serán movidos a la ubicación relevante para ser utilizados como los iconos de tu aplicación.
Simplemente necesitas seguir la convención de nombres.

Tu icono principal debe tener al menos 512x512 píxeles.
