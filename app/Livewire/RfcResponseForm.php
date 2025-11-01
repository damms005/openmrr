<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Rfc;
use Exception;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

final class RfcResponseForm extends Component implements HasForms
{
    use InteractsWithForms;

    public Rfc $rfc;

    public ?array $data = [];

    public bool $isSubmitted = false;

    public function mount(Rfc $rfc): void
    {
        $this->rfc = $rfc;

        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Textarea::make('response')
                    ->label('Your Comment')
                    ->required()
                    ->minLength(10)
                    ->maxLength(500)
                    ->rows(8)
                    ->placeholder('Please share your experience with ' . $this->rfc->startup->name),
            ])
            ->statePath('data');
    }

    public function submitResponse(): void
    {
        $data = $this->form->getState();

        try {
            $this->rfc->update(['response' => $data['response']]);
            $this->isSubmitted = true;

            Notification::make()
                ->title('Thank you!')
                ->body('Your response has been submitted successfully.')
                ->success()
                ->send();

            $this->redirect(route('home', $this->rfc));
        } catch (Exception) {
            Notification::make()
                ->title('Error')
                ->body('Failed to submit your response. Please try again.')
                ->danger()
                ->send();
        }
    }
}
