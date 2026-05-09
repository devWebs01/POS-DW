<?php

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\WithPagination;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Livewire\Volt\computed;
use function Livewire\Volt\state;
use function Livewire\Volt\uses;

name('transactions.index');

middleware('auth');
middleware('verified');

uses(WithPagination::class);

state([
    'search' => '',
    'sortBy' => 'id',
    'sortDirection' => 'desc',
])->url();

state([
    'showCreateModal' => false,
    'showDetailModal' => false,
    'showDeleteModal' => false,
    'deletingTransactionId' => null,
    'viewingTransactionId' => null,

    // Cart
    'productSearch' => '',
    'cart' => [],

    // Payment
    'payment_method' => 'cash',
    'paid_amount' => null,
    'notes' => '',
]);

$sort = function ($column) {
    if ($this->sortBy === $column) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortBy = $column;
        $this->sortDirection = 'asc';
    }
};

$transactions = computed(function () {
    return Transaction::query()
        ->withCount('items')
        ->with('user')
        ->where(function ($q) {
            $q->where('invoice_number', 'like', '%' . $this->search . '%');
        })
        ->orderBy($this->sortBy, $this->sortDirection)
        ->paginate(10);
});

$availableProducts = computed(function () {
    if (empty($this->productSearch)) {
        return collect();
    }

    return Product::query()
        ->where('is_active', true)
        ->where(function ($q) {
            $q->where('name', 'like', '%' . $this->productSearch . '%')->orWhere('sku', 'like', '%' . $this->productSearch . '%');
        })
        ->limit(10)
        ->get();
});

$viewTransaction = computed(function () {
    if (!$this->viewingTransactionId) {
        return null;
    }

    return Transaction::with(['items.product', 'user'])->find($this->viewingTransactionId);
});

$create = function () {
    $this->reset(['productSearch', 'cart', 'payment_method', 'paid_amount', 'notes', 'deletingTransactionId']);
    $this->payment_method = 'cash';
    $this->showCreateModal = true;
};

$addToCart = function ($productId) {
    $product = Product::findOrFail($productId);

    $exists = false;
    foreach ($this->cart as &$item) {
        if ($item['product_id'] === $productId) {
            $item['quantity']++;
            $item['subtotal'] = $item['quantity'] * $item['unit_price'];
            $exists = true;
            break;
        }
    }

    if (!$exists) {
        $this->cart[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'unit_price' => (float) $product->price,
            'quantity' => 1,
            'subtotal' => (float) $product->price,
        ];
    }
};

$updateCartQty = function ($index, $quantity) {
    $quantity = max(1, (int) $quantity);
    $this->cart[$index]['quantity'] = $quantity;
    $this->cart[$index]['subtotal'] = $quantity * $this->cart[$index]['unit_price'];
};

$removeFromCart = function ($index) {
    array_splice($this->cart, $index, 1);
};

$save = function () {
    if (empty($this->cart)) {
        Flux::toast(variant: 'error', text: __('Please add at least one product.'));

        return;
    }

    $totalAmount = collect($this->cart)->sum('subtotal');

    if (($this->paid_amount ?? 0) < $totalAmount) {
        Flux::toast(variant: 'error', text: __('Paid amount is less than total.'));

        return;
    }

    $validated = $this->validate([
        'payment_method' => 'required|string|in:cash,transfer,debit_card,credit_card',
        'paid_amount' => 'required|numeric|min:0',
        'notes' => 'nullable|string|max:500',
    ]);

    $invoiceNumber = 'INV-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6));

    $transaction = Transaction::create([
        'user_id' => auth()->id(),
        'invoice_number' => $invoiceNumber,
        'total_amount' => $totalAmount,
        'paid_amount' => $validated['paid_amount'],
        'change_amount' => max(0, $validated['paid_amount'] - $totalAmount),
        'payment_method' => $validated['payment_method'],
        'notes' => $validated['notes'] ?? '',
    ]);

    $items = [];
    foreach ($this->cart as $item) {
        $items[] = [
            'transaction_id' => $transaction->id,
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price'],
            'subtotal' => $item['subtotal'],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    TransactionItem::insert($items);

    $this->reset(['productSearch', 'cart', 'payment_method', 'paid_amount', 'notes', 'showCreateModal']);

    Flux::toast(variant: 'success', text: __('Transaction #:invoice created.', ['invoice' => $invoiceNumber]));
};

$viewDetail = function ($id) {
    $this->viewingTransactionId = $id;
    $this->showDetailModal = true;
};

$confirmDelete = function ($id) {
    $this->deletingTransactionId = $id;
    $this->showDeleteModal = true;
};

$delete = function () {
    $transaction = Transaction::findOrFail($this->deletingTransactionId);
    $transaction->items()->delete();
    $transaction->delete();

    $this->deletingTransactionId = null;
    $this->showDeleteModal = false;

    Flux::toast(variant: 'success', text: __('Transaction deleted.'));
};

?>

<x-layouts::app :title="__('Transactions')">
    @volt
        <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="#">Home</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Transactions</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <div class="flex items-center justify-between">
                <flux:heading size="lg">Transactions</flux:heading>
                <flux:button variant="primary" icon="plus" wire:click="create">
                    {{ __('New Transaction') }}
                </flux:button>
            </div>

            <flux:input size="md" wire:model.live="search" type="search" placeholder="Search by invoice number..." />

            <flux:table :paginate="$this->transactions">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'id'" :direction="$sortDirection"
                        wire:click="sort('id')">
                        Invoice
                    </flux:table.column>

                    <flux:table.column>
                        Customer
                    </flux:table.column>

                    <flux:table.column>
                        Items
                    </flux:table.column>

                    <flux:table.column sortable :sorted="$sortBy === 'total_amount'" :direction="$sortDirection"
                        wire:click="sort('total_amount')">
                        Total
                    </flux:table.column>

                    <flux:table.column>
                        Payment
                    </flux:table.column>

                    <flux:table.column>
                        Cashier
                    </flux:table.column>

                    <flux:table.column>
                        Actions
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->transactions as $transaction)
                        <flux:table.row :key="$transaction->id">
                            <flux:table.cell>
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $transaction->invoice_number }}</span>
                                    <span
                                        class="text-xs text-zinc-500">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $transaction->customer }}
                            </flux:table.cell>


                            <flux:table.cell>
                                <flux:badge size="sm" inset="top bottom">
                                    {{ $transaction->items_count }} {{ Str::plural('item', $transaction->items_count) }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell variant="strong">
                                {{ Number::currency($transaction->total_amount, 'IDR', 'id') }}
                            </flux:table.cell>

                            <flux:table.cell>
                                @php
                                    $methodColors = [
                                        'cash' => 'green',
                                        'transfer' => 'blue',
                                        'debit_card' => 'purple',
                                        'credit_card' => 'orange',
                                    ];
                                    $methodLabels = [
                                        'cash' => 'Cash',
                                        'transfer' => 'Transfer',
                                        'debit_card' => 'Debit Card',
                                        'credit_card' => 'Credit Card',
                                    ];
                                @endphp
                                <flux:badge size="sm" :color="$methodColors[$transaction->payment_method] ?? 'gray'"
                                    inset="top bottom">
                                    {{ $methodLabels[$transaction->payment_method] ?? $transaction->payment_method }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>{{ $transaction->user?->name ?? '-' }}</flux:table.cell>

                            <flux:table.cell>
                                <flux:dropdown position="bottom" align="end">
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                        inset="top bottom" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye" wire:click="viewDetail({{ $transaction->id }})">
                                            {{ __('View') }}
                                        </flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger"
                                            wire:click="confirmDelete({{ $transaction->id }})">
                                            {{ __('Delete') }}
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            {{-- Create Transaction Modal --}}
            <flux:modal wire:model.self="showCreateModal" class="max-w-3xl">
                <form wire:submit="save" class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('New Transaction') }}</flux:heading>
                        <flux:subheading>{{ __('Add products and process payment.') }}</flux:subheading>
                    </div>

                    {{-- Product Search --}}
                    <flux:input wire:model.live="productSearch" type="search"
                        placeholder="Search products by name or SKU..." />

                    {{-- Available Products --}}
                    @if (count($this->availableProducts) > 0)
                        <div class="max-h-40 overflow-y-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                            <table class="w-full text-sm">
                                <tbody>
                                    @foreach ($this->availableProducts as $product)
                                        <tr class="border-b border-zinc-100 last:border-b-0 dark:border-zinc-800">
                                            <td class="px-3 py-2">
                                                <span class="font-medium">{{ $product->name }}</span>
                                                <span
                                                    class="ml-2 text-xs text-zinc-500">({{ Number::currency($product->price, 'IDR', 'id') }})</span>
                                                @if ($product->stock < 1)
                                                    <flux:badge size="xs" color="red" inset="top bottom">out of
                                                        stock</flux:badge>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-right">
                                                <flux:button size="xs" variant="ghost" icon="plus"
                                                    wire:click="addToCart({{ $product->id }})"
                                                    :disabled="$product->stock < 1" />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- Cart --}}
                    @if (count($this->cart) > 0)
                        <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                                        <th class="px-3 py-2 text-left font-medium">Product</th>
                                        <th class="px-3 py-2 text-right font-medium">Price</th>
                                        <th class="px-3 py-2 text-center font-medium">Qty</th>
                                        <th class="px-3 py-2 text-right font-medium">Subtotal</th>
                                        <th class="px-3 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0; @endphp
                                    @foreach ($this->cart as $index => $item)
                                        @php $total += $item['subtotal']; @endphp
                                        <tr class="border-b border-zinc-100 last:border-b-0 dark:border-zinc-800">
                                            <td class="px-3 py-2">{{ $item['name'] }}</td>
                                            <td class="px-3 py-2 text-right">
                                                {{ Number::currency($item['unit_price'], 'IDR', 'id') }}</td>
                                            <td class="px-3 py-2 text-center">
                                                <flux:input type="number" size="xs" min="1"
                                                    value="{{ $item['quantity'] }}"
                                                    wire:change="updateCartQty({{ $index }}, $event.target.value)"
                                                    class="w-20 text-center" />
                                            </td>
                                            <td class="px-3 py-2 text-right font-medium">
                                                {{ Number::currency($item['subtotal'], 'IDR', 'id') }}</td>
                                            <td class="px-3 py-2 text-right">
                                                <flux:button size="xs" variant="ghost" icon="x-mark"
                                                    wire:click="removeFromCart({{ $index }})" />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="border-t bg-zinc-50 font-semibold dark:border-zinc-700 dark:bg-zinc-800">
                                        <td colspan="3" class="px-3 py-2 text-right">Total</td>
                                        <td class="px-3 py-2 text-right">{{ Number::currency($total, 'IDR', 'id') }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div
                            class="rounded-lg border border-dashed border-zinc-300 p-8 text-center text-sm text-zinc-500 dark:border-zinc-600">
                            {{ __('Search and add products to start a transaction.') }}
                        </div>
                    @endif

                    {{-- Payment --}}
                    <div class="grid gap-4 sm:grid-cols-3">
                        <flux:select wire:model="payment_method" :label="__('Payment Method')">
                            <flux:select.option value="cash">Cash</flux:select.option>
                            <flux:select.option value="transfer">Transfer</flux:select.option>
                            <flux:select.option value="debit_card">Debit Card</flux:select.option>
                            <flux:select.option value="credit_card">Credit Card</flux:select.option>
                        </flux:select>

                        <flux:input wire:model.live="paid_amount" :label="__('Paid Amount')" type="number" step="0.01"
                            min="0" />

                        <div>
                            <flux:label>{{ __('Change') }}</flux:label>
                            <div class="mt-2 text-lg font-bold">
                                @php $change = max(0, ($paid_amount ?? 0) - collect($this->cart)->sum('subtotal')); @endphp
                                {{ Number::currency($change, 'IDR', 'id') }}
                            </div>
                        </div>
                    </div>

                    <flux:textarea wire:model="notes" :label="__('Notes')" />

                    <div class="flex justify-end gap-2">
                        <flux:modal.close>
                            <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button variant="primary" type="submit">{{ __('Save Transaction') }}</flux:button>
                    </div>
                </form>
            </flux:modal>

            {{-- Detail Modal --}}
            <flux:modal wire:model.self="showDetailModal" class="max-w-2xl">
                @if ($this->viewTransaction)
                    <div class="space-y-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <flux:heading size="lg">{{ $this->viewTransaction->invoice_number }}</flux:heading>
                                <flux:subheading>
                                    {{ $this->viewTransaction->created_at->format('d M Y, H:i') }}
                                    &middot;
                                    {{ $this->viewTransaction->user?->name ?? '-' }}
                                </flux:subheading>
                            </div>
                            @php
                                $methodLabels = [
                                    'cash' => 'Cash',
                                    'transfer' => 'Transfer',
                                    'debit_card' => 'Debit Card',
                                    'credit_card' => 'Credit Card',
                                ];
                            @endphp
                            <flux:badge size="sm" inset="top bottom">
                                {{ $methodLabels[$this->viewTransaction->payment_method] ?? $this->viewTransaction->payment_method }}
                            </flux:badge>
                        </div>

                        <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                                        <th class="px-3 py-2 text-left font-medium">Product</th>
                                        <th class="px-3 py-2 text-right font-medium">Price</th>
                                        <th class="px-3 py-2 text-center font-medium">Qty</th>
                                        <th class="px-3 py-2 text-right font-medium">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->viewTransaction->items as $item)
                                        <tr class="border-b border-zinc-100 last:border-b-0 dark:border-zinc-800">
                                            <td class="px-3 py-2">{{ $item->product?->name ?? '-' }}</td>
                                            <td class="px-3 py-2 text-right">
                                                {{ Number::currency($item->unit_price, 'IDR', 'id') }}</td>
                                            <td class="px-3 py-2 text-center">{{ $item->quantity }}</td>
                                            <td class="px-3 py-2 text-right font-medium">
                                                {{ Number::currency($item->subtotal, 'IDR', 'id') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="border-t bg-zinc-50 font-semibold dark:border-zinc-700 dark:bg-zinc-800">
                                        <td colspan="3" class="px-3 py-2 text-right">Total</td>
                                        <td class="px-3 py-2 text-right">
                                            {{ Number::currency($this->viewTransaction->total_amount, 'IDR', 'id') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-zinc-500">{{ __('Paid') }}</span>
                                <p class="font-medium">
                                    {{ Number::currency($this->viewTransaction->paid_amount, 'IDR', 'id') }}</p>
                            </div>
                            <div>
                                <span class="text-zinc-500">{{ __('Change') }}</span>
                                <p class="font-medium">
                                    {{ Number::currency($this->viewTransaction->change_amount, 'IDR', 'id') }}</p>
                            </div>
                            <div>
                                <span class="text-zinc-500">{{ __('Items') }}</span>
                                <p class="font-medium">{{ $this->viewTransaction->items->count() }}
                                    {{ Str::plural('item', $this->viewTransaction->items->count()) }}</p>
                            </div>
                        </div>

                        @if ($this->viewTransaction->notes)
                            <div>
                                <span class="text-sm text-zinc-500">{{ __('Notes') }}</span>
                                <p class="mt-1 text-sm">{{ $this->viewTransaction->notes }}</p>
                            </div>
                        @endif

                        <div class="flex justify-end">
                            <flux:modal.close>
                                <flux:button variant="filled">{{ __('Close') }}</flux:button>
                            </flux:modal.close>
                        </div>
                    </div>
                @endif
            </flux:modal>

            {{-- Delete Confirmation Modal --}}
            <flux:modal wire:model.self="showDeleteModal" class="max-w-lg">
                <form wire:submit="delete" class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('Delete Transaction') }}</flux:heading>
                        <flux:subheading>
                            {{ __('Are you sure you want to delete this transaction? All items in this transaction will also be deleted. This action cannot be undone.') }}
                        </flux:subheading>
                    </div>

                    <div class="flex justify-end gap-2">
                        <flux:modal.close>
                            <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button variant="danger" type="submit">{{ __('Delete') }}</flux:button>
                    </div>
                </form>
            </flux:modal>
        </div>
    @endvolt
</x-layouts::app>
