<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\CreateStartup;
use App\Models\Startup;
use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Livewire\Component;

final class SearchAndCreate extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [
        'startup_id' => null,
    ];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('startup_id')
                    ->hiddenLabel()
                    ->placeholder('Search startups by name or founder...')
                    ->native(false)
                    ->allowHtml()
                    ->searchable()
                    ->options($this->getInitialOptions())
                    ->getSearchResultsUsing(fn(string $search): array => $this->getSearchResults($search))
                    ->getOptionLabelUsing(fn($value): string => $this->getOptionLabel($value))
                    ->live()
                    ->afterStateUpdated(function (?string $state): void {
                        if ($state) {
                            $startup = Startup::find($state);
                            if ($startup) {
                                $this->redirect(route('startup.show', $startup->slug));
                            }
                        }
                    }),
            ])
            ->statePath('data');
    }

    public function createStartupAction(): Action
    {
        return Action::make('createStartup')
            ->label('Add Your Startup')
            ->icon('heroicon-m-plus')
            ->color('primary')
            ->modalHeading('Add Your Startup')
            ->schema([
                TextInput::make('description')
                    ->label('Description (Optional)')
                    ->maxLength(255)
                    ->placeholder("Your startup's short description"),

                TextInput::make('polar_api_key')
                    ->extraInputAttributes([
                        'autocomplete' => 'off',
                        'data-1p-ignore' => 'true',
                    ])
                    ->label('API Key')
                    ->required()
                    ->password(),

                TextInput::make('x_handle')
                    ->label('𝕏 handle (Optional)')
                    ->maxLength(255)
                    ->placeholder('twitter')
                    ->prefix('@')
                    ->rules([
                        fn(): Closure => function (string $attribute, $value, Closure $fail) {
                            if ($value && str_contains($value, '@')) {
                                $fail('No need to add the @ symbol.');
                            }
                        },
                    ]),
            ])
            ->action(function (array $data, CreateStartup $submitAction): void {
                try {
                    $apiKey = $data['polar_api_key'];
                    $xHandle = $data['x_handle'] ?? null;
                    $description = $data['description'] ?? null;

                    $startup = $submitAction->handle($apiKey, $xHandle, $description);

                    $this->redirect(route('startup.show', $startup->slug));
                } catch (Exception $e) {
                    report($e);
                    Notification::make()
                        ->title('Error Creating Startup')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->modalWidth(Width::Large)
            ->rateLimit(5);
    }

    private function getInitialOptions(): array
    {
        return Startup::with('founder')
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->mapWithKeys(function (Startup $startup) {
                return [$startup->id => $this->formatStartupOption($startup)];
            })
            ->toArray();
    }

    private function getOptionLabel($value): string
    {
        $startup = Startup::with('founder')->find($value);
        return $startup ? $this->formatStartupOption($startup) : '';
    }

    private function getSearchResults(string $search): array
    {
        return Startup::with('founder')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('founder', function ($founderQuery) use ($search) {
                        $founderQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('x_handle', 'like', "%{$search}%");
                    });
            })
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->mapWithKeys(function (Startup $startup) {
                return [$startup->id => $this->formatStartupOption($startup)];
            })
            ->toArray();
    }

    private function formatStartupOption(Startup $startup): string
    {
        $description = str($startup->description)->limit(60);

        if ($startup->avatar_url) {
            $avatar = sprintf(
                '<img src="%s" alt="%s" class="h-8 w-8 shrink-0 rounded-lg object-cover">',
                e($startup->avatar_url),
                e($startup->name)
            );
        } else {
            $initials = strtoupper(
                substr(
                    collect(explode(' ', $startup->name))
                        ->map(fn($word) => $word[0] ?? '')
                        ->implode(''),
                    0,
                    2,
                ),
            );
            $avatar = sprintf(
                '<div class="bg-linear-to-br flex h-8 w-8 shrink-0 items-center justify-center rounded-lg from-blue-500 to-purple-600"><span class="text-xs font-bold text-white">%s</span></div>',
                e($initials)
            );
        }

        return sprintf(
            '<div class="flex items-center gap-3 cursor-default">
                %s
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-gray-900 dark:text-white">%s</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 truncate">%s</div>
                </div>
            </div>',
            $avatar,
            e($startup->name),
            e($description),
        );
    }
}
