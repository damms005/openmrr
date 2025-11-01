<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Advertiser;
use App\Services\PolarService;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Throwable;

final class AdvertiserCheckoutSuccess extends Component implements HasForms
{
    use InteractsWithForms;

    #[Locked]
    public string $checkoutSessionId = '';

    public ?string $title = null;

    public ?string $description = null;

    public ?string $linkUrl = null;

    public ?string $imageUrl = null;

    public bool $sessionValid = false;

    public function mount(PolarService $polarService): void
    {
        $checkoutId = request()->query('checkout_id');

        if (! $this->isValidCheckoutIdFormat($checkoutId)) {
            Notification::make()
                ->title('Invalid payment session')
                ->body('The payment session is invalid. Please contact support to resolve this.')
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(route('home'), navigate: true);

            return;
        }

        try {
            if (! $polarService->validateCheckout($checkoutId)) {
                Notification::make()
                    ->title('Invalid payment session')
                    ->body('The payment session is invalid or not completed. Please contact support to resolve this.')
                    ->danger()
                    ->persistent()
                    ->send();

                $this->redirect(route('home'), navigate: true);

                return;
            }
        } catch (Throwable $th) {
            report($th);

            Notification::make()
                ->title('Unable to verify payment')
                ->body('We could not verify your payment session. Please contact support.')
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(route('home'), navigate: true);

            return;
        }

        $this->checkoutSessionId = $checkoutId;
        $this->sessionValid = true;
    }

    public function createAdvertiser(): void
    {
        $data = $this->form->getState();

        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'linkUrl' => 'required|url',
            'imageUrl' => 'nullable|url',
        ]);

        try {
            Advertiser::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'link_url' => $data['linkUrl'],
                'image_url' => $data['imageUrl'],
                'position' => 'sidebar',
                'active_till' => now()->addMonth(),
            ]);

            Notification::make()
                ->title('Advertiser slot created')
                ->body('Your advertising slot is now active!')
                ->success()
                ->send();

            $this->redirect(route('home'), navigate: true);
        } catch (Throwable $th) {
            report($th);

            Notification::make()
                ->title('Failed to create advertiser slot')
                ->body('We could not create your advertiser slot. Please contact support.')
                ->danger()
                ->persistent()
                ->send();
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.advertiser-checkout-success');
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(1)
                ->schema([
                    TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('description')
                        ->label('Description')
                        ->required()
                        ->maxLength(1000)
                        ->rows(4),
                    TextInput::make('linkUrl')
                        ->label('Link URL')
                        ->url()
                        ->required(),
                    TextInput::make('imageUrl')
                        ->label('Image URL')
                        ->url(),
                ]),
        ];
    }

    private function isValidCheckoutIdFormat(?string $checkoutId): bool
    {
        if (empty($checkoutId)) {
            return false;
        }

        return preg_match('/^[a-zA-Z0-9_-]{10,}$/', $checkoutId) === 1;
    }
}
