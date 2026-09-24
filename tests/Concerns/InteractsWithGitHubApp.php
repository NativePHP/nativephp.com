<?php

namespace Tests\Concerns;

trait InteractsWithGitHubApp
{
    /**
     * Configure the GitHub App with a real RSA key so JWTs can be signed, returning the public key.
     */
    protected function configureGitHubApp(): string
    {
        static $keys;

        if ($keys === null) {
            $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
            openssl_pkey_export($key, $privateKey);

            $path = tempnam(sys_get_temp_dir(), 'github-app-key');
            file_put_contents($path, $privateKey);

            $keys = [$path, openssl_pkey_get_details($key)['key']];
        }

        config([
            'services.github_app.app_id' => '12345',
            'services.github_app.client_id' => 'Iv1.testclient',
            'services.github_app.client_secret' => 'test-app-secret',
            'services.github_app.private_key_path' => $keys[0],
            'services.github_app.slug' => 'nativephp-test',
        ]);

        return $keys[1];
    }
}
