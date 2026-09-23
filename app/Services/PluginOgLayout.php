<?php

namespace App\Services;

use Intervention\Image\Colors\Rgb\Color;
use SimonHamp\TheOg\Layout\Position;

class PluginOgLayout extends NativePhpLayout
{
    protected const PILL_GAP = 16;

    public function __construct(
        protected ?string $version = null,
        protected ?string $mobileVersion = null,
        protected ?string $iosVersion = null,
        protected ?string $androidVersion = null,
    ) {}

    public function features(): void
    {
        parent::features();

        $pills = $this->pills();

        if ($pills === []) {
            return;
        }

        $anchor = $this->getFeature('description') ?? $this->getFeature('title');

        foreach (array_values($pills) as $index => $label) {
            $pill = (new PillBox)
                ->name("pill_{$index}")
                ->text($label)
                ->color(new Color(51, 65, 85))
                ->backgroundColor('#eef1f6')
                ->font($this->config->theme->getDescriptionFont())
                ->size(28)
                ->hAlign('center')
                ->vAlign('middle')
                ->box($this->mountArea()->box->width(), 56);

            if ($index === 0) {
                $pill->position(
                    x: 0,
                    y: 44,
                    relativeTo: fn () => $anchor->anchor(Position::BottomLeft),
                );
            } else {
                $previous = 'pill_'.($index - 1);

                $pill->position(
                    x: self::PILL_GAP,
                    y: 0,
                    relativeTo: fn () => $this->getFeature($previous)->anchor(Position::TopRight),
                );
            }

            $this->addFeature($pill);
        }
    }

    /**
     * The plugin detail labels to render as pills, omitting any unknown values.
     *
     * @return list<string>
     */
    public function pills(): array
    {
        $pills = [];

        if ($this->version) {
            $pills[] = 'v'.ltrim($this->version, 'v');
        }

        if ($this->mobileVersion) {
            $pills[] = 'NativePHP Mobile '.$this->mobileVersion;
        }

        if ($this->iosVersion) {
            $pills[] = 'iOS '.$this->asMinimum($this->iosVersion);
        }

        if ($this->androidVersion) {
            $pills[] = 'Android '.$this->asMinimum($this->androidVersion);
        }

        return $pills;
    }

    /**
     * Present a bare version number as a minimum (e.g. "18.2" becomes "18.2+").
     */
    protected function asMinimum(string $version): string
    {
        return str_ends_with($version, '+') ? $version : $version.'+';
    }
}
