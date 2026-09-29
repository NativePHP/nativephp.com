<?php

namespace App\Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;

class ImageCropper extends Field
{
    protected string $view = 'filament.forms.components.image-cropper';

    protected string|Closure|null $imageUrl = null;

    protected int|Closure $targetWidth = 1200;

    protected int|Closure $targetHeight = 630;

    protected bool|Closure $hasPendingImage = false;

    /**
     * The URL of the image the crop selection is made on.
     */
    public function imageUrl(string|Closure|null $url): static
    {
        $this->imageUrl = $url;

        return $this;
    }

    /**
     * The dimensions of the image that will be generated from the crop selection.
     */
    public function targetDimensions(int|Closure $width, int|Closure $height): static
    {
        $this->targetWidth = $width;
        $this->targetHeight = $height;

        return $this;
    }

    /**
     * Whether the source image has been changed but not saved yet, meaning the
     * crop selection can't be made until the record is saved.
     */
    public function hasPendingImage(bool|Closure $condition): static
    {
        $this->hasPendingImage = $condition;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->evaluate($this->imageUrl);
    }

    public function getTargetWidth(): int
    {
        return $this->evaluate($this->targetWidth);
    }

    public function getTargetHeight(): int
    {
        return $this->evaluate($this->targetHeight);
    }

    public function getHasPendingImage(): bool
    {
        return (bool) $this->evaluate($this->hasPendingImage);
    }
}
