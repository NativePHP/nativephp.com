@php
    $networks = [
        ['href' => $githubLink, 'title' => 'Source code of NativePHP', 'icon' => 'github', 'size' => 'size-[1.2rem]'],
        ['href' => $openCollectiveLink, 'title' => 'NativePHP on Open Collective', 'icon' => 'opencollective', 'size' => 'size-[1.2rem]'],
        ['href' => $discordLink, 'title' => 'Go to discord server', 'icon' => 'discord', 'size' => 'size-[1.25rem]'],
        ['href' => 'https://youtube.com/@NativePHPOfficial', 'title' => 'NativePHP on YouTube', 'icon' => 'youtube', 'size' => 'size-[1.5rem]'],
        ['href' => 'https://www.twitch.tv/nativephp', 'title' => 'NativePHP on Twitch', 'icon' => 'twitch', 'size' => 'size-[1.15rem]'],
        ['href' => 'https://dev-null.sh/@nativephp', 'title' => 'NativePHP on /dev/null', 'icon' => 'devnull', 'size' => 'size-[1.1rem]'],
        ['href' => 'https://x.com/nativephp', 'title' => 'NativePHP on X', 'icon' => 'twitter', 'size' => 'size-[1.1rem]'],
        ['href' => $bskyLink, 'title' => 'NativePHP on Bluesky', 'icon' => 'bluesky', 'size' => 'size-[1.2rem]'],
        ['href' => 'https://www.threads.com/@nativephp', 'title' => 'NativePHP on Threads', 'icon' => 'threads', 'size' => 'size-[1.15rem]'],
        ['href' => 'https://www.facebook.com/profile.php?id=61578672170369', 'title' => 'NativePHP on Facebook', 'icon' => 'facebook', 'size' => 'size-[1.2rem]'],
        ['href' => 'https://www.instagram.com/nativephp/', 'title' => 'NativePHP on Instagram', 'icon' => 'instagram', 'size' => 'size-[1.2rem]'],
        ['href' => 'https://www.tiktok.com/@nativephp', 'title' => 'NativePHP on TikTok', 'icon' => 'tiktok', 'size' => 'size-[1.15rem]'],
        ['href' => 'https://www.linkedin.com/company/nativephp/', 'title' => 'NativePHP on LinkedIn', 'icon' => 'linkedin', 'size' => 'size-[1.2rem]'],
    ];
@endphp

@foreach ($networks as $network)
    <div>
        <a
            href="{{ $network['href'] }}"
            title="{{ $network['title'] }}"
            aria-label="{{ $network['title'] }}"
            class="group inline-grid size-8 place-items-center"
        >
            <x-dynamic-component
                :component="'icons.' . $network['icon']"
                class="{{ $network['size'] }} text-gray-500 transition duration-200 group-hover:text-violet-400 dark:text-gray-400"
            />
        </a>
    </div>
@endforeach
