<?php

namespace App\Livewire\Customer\Course;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\LessonProgress;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Renderless;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
class LessonShow extends Component
{
    public const INTRO_SECONDS = 9;

    public const OUTRO_SECONDS = 10;

    private const VIDEO_PLAYED_SESSION_KEY = 'course_video_played';

    public CourseLesson $lesson;

    public bool $skipIntroOutro = false;

    public function mount(CourseLesson $lesson): void
    {
        $this->lesson = $lesson->load('module.course');

        abort_unless($this->lesson->is_published || $this->isAdmin, 404);

        if (! $this->lesson->is_free && ! $this->hasPurchased && ! $this->isAdmin) {
            abort(403, 'You need Pro access to view this lesson.');
        }

        $this->skipIntroOutro = session()->has(self::VIDEO_PLAYED_SESSION_KEY);
    }

    #[Renderless]
    public function markVideoPlayed(): void
    {
        session()->put(self::VIDEO_PLAYED_SESSION_KEY, true);
    }

    #[Computed]
    public function isAdmin(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    #[Computed]
    public function course(): Course
    {
        return $this->lesson->module->course->load(['modules' => function ($query) {
            $query->when(! $this->isAdmin, fn ($query) => $query->where('is_published', true))
                ->orderBy('sort_order')
                ->with(['lessons' => function ($query) {
                    $query->when(! $this->isAdmin, fn ($query) => $query->where('is_published', true))->orderBy('sort_order');
                }]);
        }]);
    }

    #[Computed]
    public function hasPurchased(): bool
    {
        $product = Product::where('slug', 'nativephp-masterclass')->first();

        return $product && $product->isOwnedBy(auth()->user());
    }

    /**
     * @return Collection<int, CourseLesson>
     */
    #[Computed]
    public function moduleLessons(): Collection
    {
        return $this->lesson->module->lessons()
            ->when(! $this->isAdmin, fn ($query) => $query->where('is_published', true))
            ->get();
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
        return view('livewire.customer.course.lesson-show', [
            'introSkipSeconds' => self::INTRO_SECONDS,
            'outroSkipSeconds' => self::OUTRO_SECONDS,
        ])->title($this->lesson->title);
    }

    private function orderedLessons()
    {
        return $this->course->modules->flatMap(fn ($m) => $m->lessons);
    }
}
