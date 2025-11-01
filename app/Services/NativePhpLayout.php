<?php

namespace App\Services;

use Intervention\Image\Colors\Rgb\Color;
use SimonHamp\TheOg\BorderPosition;
use SimonHamp\TheOg\Layout\Layouts\Standard;
use SimonHamp\TheOg\Layout\PictureBox;
use SimonHamp\TheOg\Layout\Position;
use SimonHamp\TheOg\Layout\TextBox;

class NativePhpLayout extends Standard
{
    protected BorderPosition $borderPosition = BorderPosition::All;

    protected int $borderWidth = 2;

    protected int $height = 630;

    protected int $padding = 40;

    protected int $width = 1200;

    public function features(): void
    {
        $this->addFeature((new TextBox)
            ->name('title')
            ->text($this->title())
            ->color($this->config->theme->getTitleColor())
            ->font($this->config->theme->getTitleFont())
            ->size(60)
            ->box($this->mountArea()->box->width(), 400)
            ->position(
                x: 0,
                y: 0,
                relativeTo: function () {
                    if ($url = $this->getFeature('url')) {
                        return $url->anchor(Position::BottomLeft)->moveY(25);
                    }

                    return $this->mountArea()->anchor()->moveY(20);
                }
            )
        );

        if ($description = $this->description()) {
            $this->addFeature((new TextBox)
                ->name('description')
                ->text($description)
                ->color($this->config->theme->getDescriptionColor())
                ->font($this->config->theme->getDescriptionFont())
                ->size(40)
                ->box($this->mountArea()->box->width(), 240)
                ->position(
                    x: 0,
                    y: 50,
                    relativeTo: fn () => $this->getFeature('title')->anchor(Position::BottomLeft),
                )
            );
        }

        if ($url = $this->url()) {
            $this->addFeature((new TextBox)
                ->name('url')
                ->text($url)
                ->color(new Color(0, 170, 166))
                ->font($this->config->theme->getUrlFont())
                ->size(28)
                ->box($this->mountArea()->box->width(), 45)
                ->position(
                    x: 0,
                    y: 20,
                    relativeTo: fn () => $this->mountArea()->anchor(),
                )
            );
        }

        if ($watermark = $this->watermark()) {
            $this->addFeature((new PictureBox)
                ->path($watermark->path())
                ->box(200, 200) // Doubled from 100x100 to 200x200
                ->position(
                    x: 0,
                    y: 0,
                    relativeTo: fn () => $this->mountArea()->anchor(Position::BottomRight),
                    anchor: Position::BottomRight
                )
            );
        }
    }

    public function url(): string
    {
        if ($url = parent::url()) {
            return strtoupper($url);
        }

        return '';
    }
}
