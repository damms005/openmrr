<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Rfc;
use App\Models\Startup;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View as IlluminateView;
use Livewire\Component;

final class VerifiedCommentsTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public Startup $startup;

    public bool $showComments = false;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => $this->startup->rfcs()
                ->whereNotNull('response')
                ->latest())
            ->columns([
                View::make('livewire.verified-comment-row'),
            ])
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10);
    }

    public function viewCommentsAction(): Action
    {
        return Action::make('viewComments')
            ->label('Unlock Comments')
            ->button()
            ->icon(Heroicon::LockOpen)
            ->modalHeading('Verify API Key')
            ->modalDescription('Enter the API key for this startup to view verified comments.')
            ->modalWidth(Width::Large)
            ->schema([
                TextInput::make('api_key')
                    ->label('API Key')
                    ->type('password')
                    ->extraInputAttributes([
                        'autocomplete' => 'off',
                        'data-1p-ignore' => 'true',
                    ])
                    ->required()
                    ->placeholder('Enter API key'),
            ])
            ->action(function (array $data): void {
                if ($data['api_key'] !== $this->startup->decrypted_api_key) {
                    Notification::make()
                        ->title('Invalid API Key')
                        ->body('The API key you provided does not match this startup.')
                        ->danger()
                        ->send();

                    return;
                }

                $this->showComments = true;
            });
    }

    public function copyCommentLinkAction(): Action
    {
        return Action::make('copyCommentLink')
            ->label('Copy Link')
            ->icon(Heroicon::Clipboard)
            ->action(function (array $arguments): void {
                $rfc = Rfc::find($arguments['rfc']);

                if (! $rfc) {
                    return;
                }

                $url = route('rfc.comment.view', $rfc->uuid);

                $this->js(
                    <<<JS
                        navigator.clipboard.writeText('$url');
                    JS
                );

                Notification::make()
                    ->title('Link copied!')
                    ->body('Comment link copied to clipboard.')
                    ->success()
                    ->send();
            });
    }

    public function render(): IlluminateView
    {
        return view('livewire.verified-comments-table');
    }

    protected function getActions(): array
    {
        return [
            $this->viewCommentsAction(),
        ];
    }
}
