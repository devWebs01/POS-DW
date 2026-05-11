<?php

use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Number;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Livewire\Volt\computed;
use function Livewire\Volt\state;

name('reports.index');

middleware('auth');
middleware('verified');

state([
    'from_date' => now()->startOfMonth()->format('Y-m-d'),
    'to_date' => now()->format('Y-m-d'),
])->url();

$summary = computed(function () {
    $query = Transaction::whereBetween('created_at', [$this->from_date.' 00:00:00', $this->to_date.' 23:59:59']);

    $totalRevenue = (float) $query->sum('total_amount');
    $totalTransactions = $query->count();
    $totalItems = TransactionItem::whereHas('transaction', function ($q) {
        $q->whereBetween('created_at', [$this->from_date.' 00:00:00', $this->to_date.' 23:59:59']);
    })->sum('quantity');

    return [
        'total_revenue' => $totalRevenue,
        'total_transactions' => $totalTransactions,
        'average_order' => $totalTransactions > 0 ? round($totalRevenue / $totalTransactions, 2) : 0,
        'total_items_sold' => $totalItems,
    ];
});

$dailyRevenue = computed(function () {
    return Transaction::whereBetween('created_at', [$this->from_date.' 00:00:00', $this->to_date.' 23:59:59'])
        ->selectRaw('DATE(created_at) as date, COUNT(*) as total_transactions, SUM(total_amount) as revenue')
        ->groupBy('date')
        ->orderBy('date')
        ->get();
});

$paymentMethodBreakdown = computed(function () {
    $labels = [
        'cash' => __('Cash'),
        'transfer' => __('Transfer'),
        'debit_card' => __('Debit Card'),
        'credit_card' => __('Credit Card'),
    ];

    return Transaction::whereBetween('created_at', [$this->from_date.' 00:00:00', $this->to_date.' 23:59:59'])
        ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
        ->groupBy('payment_method')
        ->orderByDesc('total')
        ->get()
        ->map(fn ($row) => [
            'label' => $labels[$row->payment_method] ?? $row->payment_method,
            'count' => $row->count,
            'total' => (float) $row->total,
        ]);
});

$topProducts = computed(function () {
    return TransactionItem::selectRaw('product_id, SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
        ->whereHas('transaction', function ($q) {
            $q->whereBetween('created_at', [$this->from_date.' 00:00:00', $this->to_date.' 23:59:59']);
        })
        ->with('product:id,name')
        ->groupBy('product_id')
        ->orderByDesc('total_revenue')
        ->limit(10)
        ->get()
        ->map(fn ($row) => [
            'name' => $row->product?->name ?? __('Deleted Product'),
            'total_quantity' => (int) $row->total_quantity,
            'total_revenue' => (float) $row->total_revenue,
        ]);
});

$categoryBreakdown = computed(function () {
    $rows = TransactionItem::selectRaw('products.category_id, SUM(transaction_items.quantity) as total_quantity, SUM(transaction_items.subtotal) as total_revenue')
        ->join('products', 'transaction_items.product_id', '=', 'products.id')
        ->whereHas('transaction', function ($q) {
            $q->whereBetween('created_at', [$this->from_date.' 00:00:00', $this->to_date.' 23:59:59']);
        })
        ->groupBy('products.category_id')
        ->orderByDesc('total_revenue')
        ->get();

    $categories = Category::pluck('name', 'id');

    return $rows->map(fn ($row) => [
        'category_name' => $categories[$row->category_id] ?? __('Unknown'),
        'total_quantity' => (int) $row->total_quantity,
        'total_revenue' => (float) $row->total_revenue,
    ]);
});

?>

<x-layouts::app :title="__('Reports')">
    @volt
        <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="#">{{ __('Home') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('Reports') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <div class="flex items-center justify-between">
                <flux:heading size="lg">{{ __('Reports') }}</flux:heading>
            </div>

            {{-- Date Range Filter --}}
            <div class="flex flex-wrap items-end gap-4">
                <div class="w-48">
                    <flux:input wire:model.live="from_date" type="date" :label="__('From')" />
                </div>
                <div class="w-48">
                    <flux:input wire:model.live="to_date" type="date" :label="__('To')" />
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-sm text-zinc-500">{{ __('Total Revenue') }}</p>
                    <p class="mt-1 text-2xl font-bold">
                        {{ Number::currency($this->summary['total_revenue'], 'IDR', 'id') }}
                    </p>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-sm text-zinc-500">{{ __('Transactions') }}</p>
                    <p class="mt-1 text-2xl font-bold">{{ Number::format($this->summary['total_transactions']) }}</p>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-sm text-zinc-500">{{ __('Avg Order Value') }}</p>
                    <p class="mt-1 text-2xl font-bold">
                        {{ Number::currency($this->summary['average_order'], 'IDR', 'id') }}
                    </p>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-sm text-zinc-500">{{ __('Items Sold') }}</p>
                    <p class="mt-1 text-2xl font-bold">{{ Number::format($this->summary['total_items_sold']) }}</p>
                </div>
            </div>

            {{-- Daily Revenue --}}
            <div>
                <flux:heading size="base" class="mb-3">{{ __('Daily Revenue') }}</flux:heading>

                @php $dailyData = $this->dailyRevenue; @endphp

                @if (count($dailyData) > 0)
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>{{ __('Date') }}</flux:table.column>
                            <flux:table.column>{{ __('Transactions') }}</flux:table.column>
                            <flux:table.column>{{ __('Revenue') }}</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach ($dailyData as $row)
                                <flux:table.row>
                                    <flux:table.cell>
                                        {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        {{ Number::format($row->total_transactions) }}
                                    </flux:table.cell>
                                    <flux:table.cell variant="strong">
                                        {{ Number::currency($row->revenue, 'IDR', 'id') }}
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                @else
                    <div
                        class="rounded-lg border border-dashed border-zinc-300 p-6 text-center text-sm text-zinc-500 dark:border-zinc-600">
                        {{ __('No transactions found for the selected period.') }}
                    </div>
                @endif
            </div>

            {{-- Two-column: Payment Methods & Top Products --}}
            <div class="grid gap-6 lg:grid-cols-2">
                {{-- Payment Method Breakdown --}}
                <div>
                    <flux:heading size="base" class="mb-3">{{ __('Payment Methods') }}</flux:heading>

                    @php $paymentData = $this->paymentMethodBreakdown; @endphp

                    @if (count($paymentData) > 0)
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('Method') }}</flux:table.column>
                                <flux:table.column>{{ __('Count') }}</flux:table.column>
                                <flux:table.column>{{ __('Total') }}</flux:table.column>
                            </flux:table.columns>

                            <flux:table.rows>
                                @foreach ($paymentData as $row)
                                    <flux:table.row>
                                        <flux:table.cell>{{ $row['label'] }}</flux:table.cell>
                                        <flux:table.cell>
                                            {{ Number::format($row['count']) }}
                                        </flux:table.cell>
                                        <flux:table.cell variant="strong">
                                            {{ Number::currency($row['total'], 'IDR', 'id') }}
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    @else
                        <div
                            class="rounded-lg border border-dashed border-zinc-300 p-6 text-center text-sm text-zinc-500 dark:border-zinc-600">
                            {{ __('No data available.') }}
                        </div>
                    @endif
                </div>

                {{-- Top Products --}}
                <div>
                    <flux:heading size="base" class="mb-3">{{ __('Top Products') }}</flux:heading>

                    @php $topProductsData = $this->topProducts; @endphp

                    @if (count($topProductsData) > 0)
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('Product') }}</flux:table.column>
                                <flux:table.column>{{ __('Sold') }}</flux:table.column>
                                <flux:table.column>{{ __('Revenue') }}</flux:table.column>
                            </flux:table.columns>

                            <flux:table.rows>
                                @foreach ($topProductsData as $row)
                                    <flux:table.row>
                                        <flux:table.cell>{{ $row['name'] }}</flux:table.cell>
                                        <flux:table.cell>
                                            {{ Number::format($row['total_quantity']) }}
                                        </flux:table.cell>
                                        <flux:table.cell variant="strong">
                                            {{ Number::currency($row['total_revenue'], 'IDR', 'id') }}
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    @else
                        <div
                            class="rounded-lg border border-dashed border-zinc-300 p-6 text-center text-sm text-zinc-500 dark:border-zinc-600">
                            {{ __('No data available.') }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Category Breakdown --}}
            <div>
                <flux:heading size="base" class="mb-3">{{ __('Sales by Category') }}</flux:heading>

                @php $categoryData = $this->categoryBreakdown; @endphp

                @if (count($categoryData) > 0)
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>{{ __('Category') }}</flux:table.column>
                            <flux:table.column>{{ __('Items Sold') }}</flux:table.column>
                            <flux:table.column>{{ __('Revenue') }}</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach ($categoryData as $row)
                                <flux:table.row>
                                    <flux:table.cell>{{ $row['category_name'] }}</flux:table.cell>
                                    <flux:table.cell>
                                        {{ Number::format($row['total_quantity']) }}
                                    </flux:table.cell>
                                    <flux:table.cell variant="strong">
                                        {{ Number::currency($row['total_revenue'], 'IDR', 'id') }}
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                @else
                    <div
                        class="rounded-lg border border-dashed border-zinc-300 p-6 text-center text-sm text-zinc-500 dark:border-zinc-600">
                        {{ __('No data available.') }}
                    </div>
                @endif
            </div>
        </div>
    @endvolt
</x-layouts::app>
