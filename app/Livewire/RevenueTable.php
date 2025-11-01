<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Startup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Livewire\Component;

final class RevenueTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        $perPage = 50;

        return $table
            ->query(Startup::with('founder')->orderBy('total_revenue', 'desc'))
            ->columns([
                TextColumn::make('rank')
                    ->label('#')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            1 => new HtmlString('<span class="text-2xl">🥇</span>'),
                            2 => new HtmlString('<span class="text-2xl">🥈</span>'),
                            3 => new HtmlString('<span class="text-2xl">🥉</span>'),
                            default => $state
                        };
                    }),

                TextColumn::make('name')
                    ->label('Startup')
                    ->formatStateUsing(fn(Startup $record) => new HtmlString(
                        $record->name .
                            ($record->business_created_at ?
                                ' <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">' .
                                $record->business_created_at->diffForHumans(short: true) .
                                '</span>' : '') .
                            '<span class="ml-1">' .
                            $this->getVerifiedCommentsBadge($record)
                            . '</span>'
                    ))
                    ->description(fn(Startup $record) => new HtmlString("<span class='text-xs text-gray-500 dark:text-gray-400'>" . str($record->description)->limit(70) . '</span>'))
                    ->url(fn(Startup $record): string => route('startup.show', $record->slug)),

                TextColumn::make('founder.x_handle')
                    ->label('Founder')
                    ->url(fn(Startup $record): ?string => $record->founder ? route('founder.show', $record->founder->x_handle) : null),

                TextColumn::make('total_revenue')
                    ->label('Revenue')
                    ->formatStateUsing(fn(int|float $state): string => '$' . number_format($state, 0)),
            ])
            ->paginated([$perPage]);
    }

    private function getVerifiedCommentsBadge(Startup $startup): HtmlString
    {
        $commentCount = $startup->rfcs()->whereNotNull('response')->count();

        return new HtmlString(view('components.verified-comments-badge', [
            'commentCount' => $commentCount,
        ])->render());
    }
}
