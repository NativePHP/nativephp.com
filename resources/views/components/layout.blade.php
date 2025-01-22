<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3"/>
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link
        href="https://fonts.bunny.net/css?family=be-vietnam-pro:700|inter:400,500,600|rubik:400,700"
        rel="stylesheet"
    />
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    @vite(["resources/css/app.css", "resources/js/app.js"])

    <!-- Fathom - beautiful, simple website analytics -->
    <script src="https://cdn.usefathom.com/script.js" data-site="HALHTNZU" defer></script>
    <!-- / Fathom -->
</head>
<body class="min-h-screen font-sans antialiased bg-white dark:bg-gray-900 dark:text-white
@isset($backgroundPattern) background-pattern @endisset
"
      x-data="{ showDocsNavigation: false }"
>

<x-header-banner :hasMenu="$hasMenu ?? false" />
{{ $slot }}
<script src="https://cdn.jsdelivr.net/npm/@docsearch/js@3"></script>
<script type="text/javascript">
    docsearch({
        appId: 'ZNII9QZ8WI',
        apiKey: '9be495a1aaf367b47c873d30a8e7ccf5',
        indexName: 'nativephp',
        insights: true,
        container: '#docsearch',
        debug: false
    });
</script>
</body>
</html>
