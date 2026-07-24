<?php

namespace App\Livewire\Customer\Course;

use App\Models\Course;
use App\Models\LessonProgress;
use App\Models\Product;
use App\Models\ProductPrice;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Course')]
class Index extends Component
{
    #[Computed]
    public function isAdmin(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    #[Computed]
    public function course(): ?Course
    {
        return Course::query()
            ->when(! $this->isAdmin, fn ($query) => $query->where('is_published', true))
            ->with(['modules' => function ($query) {
                $query->when(! $this->isAdmin, fn ($query) => $query->where('is_published', true))
                    ->orderBy('sort_order')
                    ->with(['lessons' => fn ($query) => $query->orderBy('sort_order')]);
            }])
            ->first();
    }

    #[Computed]
    public function masterclass(): ?Product
    {
        return Product::where('slug', 'nativephp-masterclass')->first();
    }

    #[Computed]
    public function hasPurchased(): bool
    {
        return $this->masterclass && $this->masterclass->isOwnedBy(auth()->user());
    }

    #[Computed]
    public function priceIncreaseAt(): string
    {
        return config('services.stripe.course_price_increase_at');
    }

    #[Computed]
    public function priceIncreased(): bool
    {
        return now()->gte($this->priceIncreaseAt());
    }

    #[Computed]
    public function bestPrice(): ?ProductPrice
    {
        return $this->masterclass?->getBestPriceForUser(auth()->user());
    }

    #[Computed]
    public function regularPrice(): ?ProductPrice
    {
        return $this->masterclass?->getRegularPrice();
    }

    #[Computed]
    public function currentPrice(): string
    {
        return $this->bestPrice?->discountedDisplayAmount() ?? ($this->priceIncreased() ? '299' : '199');
    }

    #[Computed]
    public function hasDiscount(): bool
    {
        return $this->bestPrice && $this->regularPrice
            && $this->bestPrice->discountedAmount() < $this->regularPrice->amount;
    }

    #[Computed]
    public function completedLessonIds(): array
    {
        return LessonProgress::where('user_id', auth()->id())
            ->whereNotNull('completed_at')
            ->pluck('course_lesson_id')
            ->all();
    }

    #[Computed]
    public function hasVideoLessons(): bool
    {
        return (bool) $this->course?->modules
            ->flatMap(fn ($module) => $module->lessons)
            ->contains(fn ($lesson) => filled($lesson->vimeo_id) && ($lesson->is_published || $this->isAdmin));
    }

    #[Computed]
    public function totalLessons(): int
    {
        if (! $this->course) {
            return 0;
        }

        return $this->course->modules->sum(
            fn ($module) => $module->lessons->filter(fn ($lesson) => $lesson->is_published)->count()
        );
    }

    #[Computed]
    public function completedCount(): int
    {
        return count($this->completedLessonIds);
    }
}
