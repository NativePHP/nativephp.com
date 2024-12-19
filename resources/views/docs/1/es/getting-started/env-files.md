---
title: Archivos de Entorno
order: 400
---

# Archivos de Entorno

Cuando NativePHP empaqueta tu aplicación, copiará todo el directorio de tu aplicación en el paquete, incluyendo tu
archivo `.env`.

**Esto significa que tu archivo `.env` será accesible para cualquiera que tenga acceso a tu paquete de aplicación.**

Por lo tanto, debes tener cuidado de no incluir información sensible en tu archivo `.env`, como claves API o contraseñas.
Esto es bastante diferente a una aplicación web tradicional desplegada en un servidor que controlas.

Si necesitas realizar operaciones sensibles, como acceder a una API o base de datos, debes hacerlo utilizando una
API separada que crees específicamente para tu aplicación. Luego puedes llamar a _esta_ API desde tu aplicación y
hacer que realice las operaciones sensibles en tu nombre.

Consulta [Seguridad](/docs/digging-deeper/security) para más consejos.

## Eliminando datos sensibles de tus archivos de entorno

Hay ciertas variables de entorno que NativePHP utiliza internamente, por ejemplo, para configurar el actualizador de tu aplicación o el servicio de notarización de Apple.

Estas variables de entorno se eliminan automáticamente de tu archivo `.env` cuando tu aplicación se empaqueta, por lo que
no necesitas preocuparte por su exposición.

Si deseas eliminar otras variables de entorno de tu archivo `.env`, puedes hacerlo agregándolas a la opción de configuración
`cleanup_env_keys` en tu archivo de configuración `nativephp.php`:

```php
    /**
     * A list of environment keys that should be removed from the
     * .env file when the application is bundled for production.
     * You may use wildcards to match multiple keys.
     */
    'cleanup_env_keys' => [
        'AWS_*',
        'DO_SPACES_*',
        '*_SECRET',
        'NATIVEPHP_UPDATER_PATH',
        'NATIVEPHP_APPLE_ID',
        'NATIVEPHP_APPLE_ID_PASS',
        'NATIVEPHP_APPLE_TEAM_ID',
    ],
```
