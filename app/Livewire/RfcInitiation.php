<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\SendRfcNotification;
use App\Data\CustomerData;
use App\Models\Startup;
use Exception;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

final class RfcInitiation extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public Startup $startup;

    public array $customers = [];

    public int $currentPage = 1;

    public bool $hasMorePages = false;

    public bool $hasPreviousPage = false;

    public bool $loadingCustomers = false;

    public ?string $errorMessage = null;

    public ?string $providedApiKey = null;

    public bool $isBusinessOwnerVerified = false;

    public function mount(Startup $startup): void
    {
        $this->startup = $startup;
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn() => $this->customers)
            ->columns([
                TextColumn::make('name')
                    ->label('Name'),
                TextColumn::make('email')
                    ->label('Email'),
            ])
            ->headerActions([
                Action::make('verifyBusinessOwner')
                    ->label('Verify Ownership')
                    ->schema([
                        TextInput::make('api_key')
                            ->label('API Key')
                            ->password()
                            ->required(),
                    ])
                    ->visible(fn() => ! $this->isBusinessOwnerVerified)
                    ->action(fn(array $data) => $this->verifyBusinessOwner($data)),
                Action::make('loadCustomers')
                    ->label('Load Customers')
                    ->visible(fn() => $this->isBusinessOwnerVerified && empty($this->customers) && ! $this->loadingCustomers)
                    ->action(fn() => $this->loadCustomers()),
                Action::make('previousPage')
                    ->label('Previous Page')
                    ->icon(Heroicon::ChevronLeft)
                    ->visible(fn() => $this->hasPreviousPage && ! empty($this->customers))
                    ->action(fn() => $this->previousPage()),
                Action::make('nextPage')
                    ->label('Next Page')
                    ->icon(Heroicon::ChevronRight)
                    ->visible(fn() => $this->hasMorePages && ! empty($this->customers))
                    ->action(fn() => $this->nextPage()),
            ])
            ->recordActions([
                Action::make('send')
                    ->icon(Heroicon::PaperAirplane)
                    ->action(fn(array $record) => $this->sendToCustomer($record)),
            ]);
    }

    public function loadCustomers(): void
    {
        $this->loadingCustomers = true;
        $this->errorMessage = null;
        $this->currentPage = 1;

        try {
            $service = $this->startup->getPaymentHandler();
            $data = $service->fetchCustomers($this->startup->decrypted_api_key);
            $this->customers = $data->customers;
            $this->currentPage = $data->currentPage;
            $this->hasMorePages = $data->hasMorePages;
            $this->hasPreviousPage = $data->hasPreviousPage;
        } catch (Exception $e) {
            $this->errorMessage = 'Failed to load customers: ' . $e->getMessage();
        } finally {
            $this->loadingCustomers = false;
        }
    }

    public function nextPage(): void
    {
        $this->currentPage++;
        $this->fetchPage();
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->fetchPage();
        }
    }

    public function sendToCustomer(array $customer): void
    {
        try {
            $customerData = new CustomerData(
                id: $customer['id'],
                name: $customer['name'],
                email: $customer['email'],
            );

            (new SendRfcNotification())->handle($this->startup, $customerData);

            Notification::make()
                ->success()
                ->body('RFC email successfully sent to ' . $customer['name'])
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->error()
                ->body('Failed to send RFC email: ' . $e->getMessage())
                ->send();
        }
    }

    public function verifyBusinessOwner(array $data): void
    {
        if ($data['api_key'] === $this->startup->decrypted_api_key) {
            $this->isBusinessOwnerVerified = true;
            $this->providedApiKey = null;

            Notification::make()
                ->success()
                ->body('Business owner verified')
                ->send();

            return;
        }

        Notification::make()
            ->body('The provided API key does not match this startup')
            ->danger()
            ->send();
    }

    public function render()
    {
        return view('livewire.rfc-initiation');
    }

    private function fetchPage(): void
    {
        $this->loadingCustomers = true;
        $this->errorMessage = null;

        try {
            $service = $this->startup->getPaymentHandler();
            $data = $service->fetchCustomers($this->startup->decrypted_api_key, $this->currentPage);
            $this->customers = $data->customers;
            $this->hasMorePages = $data->hasMorePages;
            $this->hasPreviousPage = $data->hasPreviousPage;
        } catch (Exception $e) {
            $this->errorMessage = 'Failed to load customers: ' . $e->getMessage();
        } finally {
            $this->loadingCustomers = false;
        }
    }
}
