<?php

namespace App\Services;

use Intervention\Image\Geometry\Factories\CircleFactory;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\Geometry\Point;
use Intervention\Image\Geometry\Rectangle;
use Intervention\Image\Interfaces\SizeInterface;
use SimonHamp\TheOg\Layout\TextBox;

/**
 * A "pill" feature: text centered on a stadium-shaped (fully rounded) background.
 *
 * Intervention has no rounded-rectangle primitive, so the pill is composed from a
 * centre rectangle plus a circle at each end.
 */
class PillBox extends TextBox
{
    protected string $backgroundColor = '#eef1f6';

    protected int $paddingX = 24;

    protected int $pillHeight = 56;

    public function backgroundColor(string $color): self
    {
        $this->backgroundColor = $color;

        return $this;
    }

    public function paddingX(int $padding): self
    {
        $this->paddingX = $padding;

        return $this;
    }

    public function pillHeight(int $height): self
    {
        $this->pillHeight = $height;

        return $this;
    }

    public function dimensions(): SizeInterface
    {
        return new Rectangle($this->textWidth() + ($this->paddingX * 2), $this->pillHeight);
    }

    public function render(): void
    {
        $position = $this->calculatePosition();
        $width = $this->dimensions()->width();
        $height = $this->dimensions()->height();
        $radius = intval(floor($height / 2));
        $x = $position->x();
        $y = $position->y();

        $this->canvas()->drawRectangle($x + $radius, $y, function (RectangleFactory $rectangle) use ($width, $height, $radius): void {
            $rectangle->size(max(0, $width - ($radius * 2)), $height);
            $rectangle->background($this->backgroundColor);
        });

        foreach ([$x + $radius, $x + $width - $radius] as $centerX) {
            $this->canvas()->drawCircle($centerX, $y + $radius, function (CircleFactory $circle) use ($height): void {
                $circle->diameter($height);
                $circle->background($this->backgroundColor);
            });
        }

        $modifier = $this->modifier($this->text);
        $modifier->position = new Point($x + intval(floor($width / 2)), $y + intval(floor($height / 2)));

        $this->canvas()->modify($modifier);
    }

    protected function textWidth(): int
    {
        return $this->canvas()->driver()
            ->fontProcessor()
            ->boxSize($this->text, $this->interventionFontInstance())
            ->width();
    }
}
