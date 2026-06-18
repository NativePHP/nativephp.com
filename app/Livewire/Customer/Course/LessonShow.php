<?php

namespace App\Livewire\Customer\Course;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\LessonProgress;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
class LessonShow extends Component
{
    public CourseLesson $lesson;

    public function mount(CourseLesson $lesson): void
    {
        $this->lesson = $lesson->load('module.course');

        if (! $this->lesson->is_free && ! $this->hasPurchased) {
            abort(403, 'You need Pro access to view this lesson.');
        }
    }

    #[Computed]
    public function course(): Course
    {
        return $this->lesson->module->course->load(['modules' => function ($query) {
            $query->where('is_published', true)
                ->orderBy('sort_order')
                ->with(['lessons' => function ($query) {
                    $query->where('is_published', true)->orderBy('sort_order');
                }]);
        }]);
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
    public function isComplete(): bool
    {
        return in_array($this->lesson->id, $this->completedLessonIds);
    }

    #[Computed]
    public function previousLesson(): ?CourseLesson
    {
        $allLessons = $this->orderedLessons();
        $index = $allLessons->search(fn ($l) => $l->id === $this->lesson->id);

        return $index > 0 ? $allLessons[$index - 1] : null;
    }

    #[Computed]
    public function nextLesson(): ?CourseLesson
    {
        $allLessons = $this->orderedLessons();
        $index = $allLessons->search(fn ($l) => $l->id === $this->lesson->id);

        return $index < $allLessons->count() - 1 ? $allLessons[$index + 1] : null;
    }

    public function toggleComplete(): void
    {
        $progress = LessonProgress::firstOrNew([
            'user_id' => auth()->id(),
            'course_lesson_id' => $this->lesson->id,
        ]);

        if ($progress->completed_at) {
            $progress->completed_at = null;
            $progress->save();
        } else {
            $progress->completed_at = now();
            $progress->save();
        }

        unset($this->completedLessonIds, $this->isComplete);
    }

    public function render()
    {
        return view('livewire.customer.course.lesson-show')
            ->title($this->lesson->title);
    }

    private function orderedLessons()
    {
        return $this->course->modules->flatMap(fn ($m) => $m->lessons);
    }
}
