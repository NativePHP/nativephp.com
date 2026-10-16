<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Which edge of a captured screenshot documents the component being shown.
 * `Top`/`Bottom` keep only a strip nearest that edge — matching every
 * existing top-bar/bottom-nav image in public/img/docs, which are cropped
 * tight rather than full-screen. `Full` keeps the whole screenshot, for
 * components (like the side nav) that need their full height visible.
 */
enum DocsScreenshotCrop: string
{
    case Top = 'top';
    case Bottom = 'bottom';
    case Full = 'full';
}
