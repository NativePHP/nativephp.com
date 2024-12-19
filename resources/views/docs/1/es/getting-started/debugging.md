---
title: Depuración
order: 350
---

# Cuando las cosas salen mal

Construir aplicaciones nativas es una tarea compleja con muchas partes en movimiento. Habrá errores, fallos y muchos momentos de confusión.

NativePHP trabaja para ocultar gran parte de la complejidad, pero a veces necesitarás ir más allá para descubrir qué es lo que realmente está sucediendo.

**Recuerda que NativePHP es una capa relativamente delgada sobre un océano completo de dependencias y herramientas que son construidas y mantenidas por muchos desarrolladores fuera del equipo de NativePHP.**

Esto significa que, aunque algunos problemas pueden resolverse dentro de NativePHP, también es muy probable que el problema se encuentre en otro lugar.

## Las capas

- Tu aplicación, construida sobre Laravel, utilizando tus instalaciones locales de PHP y Node.
- Las herramientas de desarrollo de NativePHP (`native:serve` y `native:build`) gestionan los procesos de compilación de Electron/Tauri; esto es lo que crea tu Paquete de Aplicación.
- NativePHP mueve la versión adecuada de un binario de PHP compilado estáticamente al paquete de tu aplicación. Cuando tu aplicación se inicia, es esta _versión_ de PHP la que se utiliza para ejecutar tu código PHP, no la versión de PHP de tu sistema.
- Electron y Tauri utilizan conjuntos de herramientas y dependencias específicas de la plataforma. El ecosistema de Electron está mayormente basado en Javascript, el de Tauri está mayormente basado en Rust. Gran parte de esto estará oculto en tu directorio `vendor`.
- El sistema operativo (SO) y su arquitectura (arch) - no puedes construir una aplicación para una arquitectura y distribuirla a un SO/arch diferente. No funcionará. Debes construir tu aplicación para que coincida con la combinación de SO+arch donde deseas que se ejecute.

Aunque no se espera que sepas en profundidad cómo funcionan todas estas capas y cómo encajan, tener algo de familiaridad con lo que está sucediendo te ayudará a encontrar la causa raíz de los problemas y a poder generar tickets significativos con las personas adecuadas.

## Haciendo una investigación

Aquí tienes algunos consejos para la depuración:

### Dos o tres copias
Recuerda que cuando se genera una compilación (de desarrollo o producción), toda tu aplicación Laravel es _copiada_ dentro de la carpeta de compilación.

La copia de la compilación de desarrollo se almacena en `vendor` (¡santa incepción, Batman!).

Las compilaciones de producción se empaquetan en la carpeta `dist`.

Esto significa que hay al menos 2 versiones de tu código que podrían estar ejecutándose dependiendo de lo que estés haciendo: el código en caliente que editas en tu IDE (tu 'entorno de desarrollo') y el código empaquetado que realmente se ejecuta cuando tu aplicación se ejecuta en una compilación de desarrollo o de producción.

Tener una comprensión clara sobre en qué contexto te encuentras cuando ocurren problemas te ayudará a resolver el problema más rápido.

### Salida detallada
Usa `-v`, `-vv` o `-vvv` al ejecutar `native:serve` o `native:build` ya que esto proporcionará más detalles sobre lo que está sucediendo en cada etapa del proceso. 

### Verifica los registros
Los registros generados al ejecutar compilaciones de tu aplicación se almacenan en `{appdata}/storage/logs/`.

Los registros generados al ejecutar comandos de Artisan en tu entorno de desarrollo están en `storage/logs/` (Laravel por defecto).

### Sal de la abstracción
Intenta ejecutar el paso que está fallando _fuera_ del entorno de ejecución. Reduce las capas de abstracción para identificar o descartar complicaciones específicas del entorno.

### Empezar desde cero

#### `dist`
¡No tengas miedo de eliminar compilaciones y empezar de nuevo! La carpeta `dist` en la raíz de tu aplicación a veces puede entrar en un estado inusual y solo necesita ser borrada.

#### AppData
El directorio appdata es donde se almacenan la base de datos de tu aplicación, los registros y otros elementos específicos de la aplicación. Este es un lugar confiable para almacenar datos y archivos que tu aplicación necesita para funcionar _fuera_ del paquete de la aplicación y sin desordenar el directorio de inicio de tu usuario u otras carpetas personales.

Al probar compilaciones de producción, el directorio appdata se creará en tu máquina, permitiéndote imitar completamente la experiencia de un usuario final.

En algunos casos, es posible que necesites borrar esta carpeta y luego volver a ejecutar tu aplicación.

| Plataforma | Ubicación                    |
|----------|--------------------------------|
| macOS    | ~/Library/Application Support  |
| Linux    | $XDG_CONFIG_HOME or ~/.config  |
| Windows  | %APPDATA%                      |

#### Base de datos
Intenta [refrescar completamente](/docs/digging-deeper/databases#refreshing-your-app-database) la base de datos de producción de tu aplicación:

```shell
php artisan native:migrate:fresh
```

#### Procesos
Asegúrate de que no haya procesos residuales. Revisa tu Monitor de Actividad/Administrador de Tareas para encontrar procesos de tu aplicación que puedan quedar colgados después de que una compilación haya fallado, y fuerza su cierre.
### Verifica tu aplicación y PHP
Errores que ocurren durante la ejecución de PHP en la secuencia de arranque de la aplicación pueden causar que la aplicación se bloquee antes de que siquiera comience.

Un error 500 en el código de tu aplicación, por ejemplo, puede evitar que la ventana principal se muestre, pero dejaría el proceso del shell del runtime en ejecución.

Intenta iniciar tu aplicación en un navegador estándar para ver si hay errores al acceder a la URL de entrada. Si estás usando Laravel Herd, por ejemplo, mueve tu entorno de desarrollo de la aplicación a la carpeta raíz de Herd y ve a `http://{nombre-de-tu-carpeta-de-aplicación}.test/` en tu navegador favorito.

También asegúrate de que la versión de PHP en el paquete sea la misma que la que tienes instalada en tu máquina, es decir, si estás ejecutando PHP8.2 en tu máquina, el binario de PHP que se mueve a la carpeta `dist` debería ser PHP8.2 para tu combinación actual de SO+arch.

Verificar esto también probará que el ejecutable en sí es estable:

#### Para compilaciones de desarrollo:
macOS y Linux:
```shell
/path/to/your/app/vendor/nativephp/electron/resources/js/resources/php/php -v
```
Windows:
```
C:\path\to\your\app\vendor\nativephp\electron\resources\js\resources\php\php.exe -v
```

#### Para compilaciones de producción:
macOS:
```shell
/path/to/your/app/dist/{os+arch}/AppName/Contents/Resources/app.asar.unpacked/resources/php/php -v
```

Windows:
```
C:\path\to\your\app\dist\win-unpacked\resources\app.asar.unpacked\resources\php\php.exe -v
```
```

## ¿Aún atascado?
Si has encontrado un error, por favor [abre un issue](https://github.com/nativephp/laravel/issues/new) en GitHub.

También hay [Discussions](https://github.com/orgs/NativePHP/discussions) y
[Discord](https://discord.gg/X62tWNStZK) para chat en vivo.

¡Únete a nosotros! Queremos que tengas éxito.
