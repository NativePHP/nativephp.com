<?php

use App\Models\Plugin;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Move heading anchor links from before heading text to after,
     * and update classes for hover animation.
     */
    public function up(): void
    {
        $oldPattern = '/(<h([1-3])\s+id="([^"]+)">)<a\s+href="#[^"]+"\s+class="mr-2\s+no-underline\s+font-medium"\s+style="border-bottom:\s*0\s*!important;">\s*<span\s+class="\s*text-gray-600\s+dark:text-gray-400\s+hover:text-\[#00aaa6\]">#<\/span><\/a>(.*?)(<\/h\2>)/s';

        $newReplacement = '$1$4<a href="#$3" class="heading-anchor ml-2 no-underline font-medium" style="border-bottom: 0 !important;"><span class="text-gray-600 dark:text-gray-400 hover:text-[#00aaa6]">#</span></a>$5';

        Plugin::query()
            ->whereNotNull('readme_html')
            ->each(function (Plugin $plugin) use ($oldPattern, $newReplacement) {
                $updated = preg_replace($oldPattern, $newReplacement, $plugin->readme_html);

                if ($updated !== $plugin->readme_html) {
                    $plugin->updateQuietly(['readme_html' => $updated]);
                }
            });

        Plugin::query()
            ->whereNotNull('license_html')
            ->each(function (Plugin $plugin) use ($oldPattern, $newReplacement) {
                $updated = preg_replace($oldPattern, $newReplacement, $plugin->license_html);

                if ($updated !== $plugin->license_html) {
                    $plugin->updateQuietly(['license_html' => $updated]);
                }
            });
    }

    public function down(): void
    {
        $newPattern = '/(<h([1-3])\s+id="([^"]+)">)(.*?)<a\s+href="#[^"]+"\s+class="heading-anchor\s+ml-2\s+no-underline\s+font-medium"\s+style="border-bottom:\s*0\s*!important;">\s*<span\s+class="text-gray-600\s+dark:text-gray-400\s+hover:text-\[#00aaa6\]">#<\/span><\/a>(<\/h\2>)/s';

        $oldReplacement = '$1<a href="#$3" class="mr-2 no-underline font-medium" style="border-bottom: 0 !important;"><span class=" text-gray-600 dark:text-gray-400 hover:text-[#00aaa6]">#</span></a>$4$5';

        Plugin::query()
            ->whereNotNull('readme_html')
            ->each(function (Plugin $plugin) use ($newPattern, $oldReplacement) {
                $updated = preg_replace($newPattern, $oldReplacement, $plugin->readme_html);

                if ($updated !== $plugin->readme_html) {
                    $plugin->updateQuietly(['readme_html' => $updated]);
                }
            });

        Plugin::query()
            ->whereNotNull('license_html')
            ->each(function (Plugin $plugin) use ($newPattern, $oldReplacement) {
                $updated = preg_replace($newPattern, $oldReplacement, $plugin->license_html);

                if ($updated !== $plugin->license_html) {
                    $plugin->updateQuietly(['license_html' => $updated]);
                }
            });
    }
};
