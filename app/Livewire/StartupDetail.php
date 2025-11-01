<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Startup;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Illuminate\View\View;
use Livewire\Component;

final class StartupDetail extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Startup $startup;

    public function mount(string $slug): void
    {
        $this->startup = Startup::where('slug', $slug)->with('founder')->firstOrFail();
    }

    public function startupInfolist(Schema $schema): Schema
    {
        $actions = [
            Action::make('rfc')
                ->button()
                ->label('Send RFCs')
                ->icon(Heroicon::ChatBubbleLeft)
                ->url(route('rfc.initiation', $this->startup->slug)),
        ];

        if ($this->startup->monthly_recurring_revenue > 0) {
            $mrrFormatted = number_format((float) $this->startup->monthly_recurring_revenue, 0);
            $tweetText = "🚀 {$this->startup->name} is crushing it at \$$mrrFormatted MRR! Verified on OpenMRR. {$this->startup->pageUrl}";
            $tweetUrl = 'https://x.com/intent/tweet?text='.urlencode($tweetText);

            $actions[] = Action::make('share_mrr')
                ->button()
                ->label('Share MRR')
                ->icon(Heroicon::Share)
                ->url($tweetUrl)
                ->openUrlInNewTab();
        } else {
            $actions[] = Action::make('copy_url')
                ->button()
                ->label('Copy URL')
                ->icon(Heroicon::Share)
                ->action(fn () => $this->js(
                    <<<JS
                        navigator.clipboard.writeText('{$this->startup->pageUrl}');

                        new FilamentNotification()
                            .title('Link copied to clipboard')
                            .body('{$this->startup->pageUrl}')
                            .success()
                            .send();
                    JS
                ));
        }

        if ($this->startup->website_url) {
            $actions[] = Action::make('website_url')
                ->button()
                ->label('Visit Website')
                ->icon(Heroicon::ArrowTopRightOnSquare)
                ->url($this->startup->website_url)
                ->openUrlInNewTab();
        }

        return $schema
            ->record($this->startup)
            ->inline()
            ->components(collect($actions)->map(fn ($action) => $action->size(Size::ExtraSmall))->all());
    }

    public function render(): View
    {
        return view('livewire.startup-detail')->title($this->startup->name);
    }
}
