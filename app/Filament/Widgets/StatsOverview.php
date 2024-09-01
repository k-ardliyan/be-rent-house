<?php

namespace App\Filament\Widgets;

use App\Models\Listing;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{
    private function getPercentage(int $from, int $to)
    {
        return $to - $from / ($to + $from / 2) * 100;
    }

    protected function getStats(): array
    {
        $newListing = Listing::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $transactions = Transaction::whereStatus('approved')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year);

        $prevTransactions = Transaction::whereStatus('approved')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year);

        $transactionsChange = $this->getPercentage($prevTransactions->count(), $transactions->count());
        $revenueChange = $this->getPercentage($prevTransactions->sum('total_price'), $transactions->sum('total_price'));

        return [
            Stat::make('Listing', $newListing)
                ->description('New listing this month'),
            Stat::make('Transaction', $transactions->count())
                ->description($transactionsChange > 0 ? 'Increased by ' . $transactionsChange . '%' : 'Decreased by ' . $transactionsChange . '%')
                ->descriptionIcon($transactionsChange > 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->descriptionColor($transactionsChange > 0 ? 'success' : 'danger'),
            Stat::make('Revenue', Number::currency($transactions->sum('total_price')))
                ->description($revenueChange > 0 ? 'Increased by ' . $revenueChange . '%' : 'Decreased by ' . $revenueChange . '%')
                ->descriptionIcon($revenueChange > 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->descriptionColor($revenueChange > 0 ? 'success' : 'danger'),

        ];
    }
}
