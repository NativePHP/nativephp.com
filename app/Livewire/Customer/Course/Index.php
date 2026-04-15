<?php

namespace App\Livewire\Customer\Course;

use App\Models\Course;
use App\Models\LessonProgress;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Course')]
class Index extends Component
{
    #[Computed]
    public function course(): ?Course
    {
        return Course::where('is_published', true)
            ->with(['modules' => function ($query) {
                $query->where('is_published', true)
                    ->orderBy('sort_order')
                    ->with(['lessons' => function ($query) {
                        $query->where('is_published', true)->orderBy('sort_order');
                    }]);
            }])
            ->first();
    }

    #[Computed]
    public function hasPurchased(): bool
    {
        $product = Product::where('slug', 'nativephp-masterclass')->first();

        return $product && $product->isOwnedBy(auth()->user());
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
    public function totalLessons(): int
    {
        if (! $this->course) {
            return 0;
        }

        return $this->course->modules->sum(fn ($module) => $module->lessons->count());
    }

    #[Computed]
    public function completedCount(): int
    {
        return count($this->completedLessonIds);
    }
}
