<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    #[Computed]
    public function totalProjects(): int
    {
        return count(config('landing-projects', []));
    }

    #[Computed]
    public function totalAllProjects(): int
    {
        $projectModel = $this->resolveEloquentModelKey('project');

        return match (true) {
            ($n = $this->countEloquentIfExists($projectModel)) !== null => $n,
            is_numeric(config('landing.stats.projects_in_evaluation')) => (int) config('landing.stats.projects_in_evaluation'),
            default => max($this->totalProjects(), 0),
        };
    }

    #[Computed]
    public function totalAllDevelopers(): int
    {
        $developerModel = $this->resolveEloquentModelKey('developer');

        return match (true) {
            ($n = $this->countEloquentIfExists($developerModel)) !== null => $n,
            is_numeric(config('landing.stats.developers_involved')) => (int) config('landing.stats.developers_involved'),
            default => 0,
        };
    }

    private function resolveEloquentModelKey(string $key): string
    {
        $class = config("landing.eloquent_models.{$key}");

        return is_string($class) ? $class : '';
    }

    private function countEloquentIfExists(string $class): ?int
    {
        if (! class_exists($class) || ! is_subclass_of($class, Model::class)) {
            return null;
        }

        return $class::query()->count();
    }
};
