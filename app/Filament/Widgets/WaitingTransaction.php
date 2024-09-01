<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class WaitingTransaction extends BaseWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Waiting Transaction';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()->whereStatus('waiting')
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->weight(FontWeight::Bold)
                    ->sortable(),
                Tables\Columns\TextColumn::make('listing.title')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_days')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('USD')
                    ->weight(FontWeight::Bold)
                    ->sortable(),
            ])
            ->actions([
                Action::make('Approve')
                    ->button()
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(function (Transaction $record) {
                        $record->update(['status' => 'approved']);
                        Notification::make()
                            ->success()
                            ->title('Transcation approved.')
                            ->body('Your transcation has been approved.')
                            ->icon('heroicon-o-check-circle')
                            ->send();
                    })
                    ->hidden(fn(Transaction $record): bool => $record->status !== 'waiting'),
            ]);
    }
}
