<?php

use App\Models\Product;
use Flux\Flux;
use Livewire\WithPagination;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Livewire\Volt\computed;
use function Livewire\Volt\state;
use function Livewire\Volt\uses;

name('products.index');

middleware('auth');
middleware('verified');

uses(WithPagination::class);

state([
    'search' => '',
    'sortBy' => 'id',
    'sortDirection' => 'asc',
])->url();

state([
    'showDetailModal' => false,
    'detailProduct' => null,
    'showDeleteModal' => false,
    'deletingProductId' => null,
]);

$sort = function ($column) {
    if ($this->sortBy === $column) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortBy = $column;
        $this->sortDirection = 'asc';
    }
};

$products = computed(function () {
    return Product::query()
        ->with('category')
        ->where(function ($q) {
            $q->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('sku', 'like', '%'.$this->search.'%');
        })
        ->orderBy($this->sortBy, $this->sortDirection)
        ->paginate(10);
});

$totalProducts = computed(function () {
    return Product::count();
});

$activeProducts = computed(function () {
    return Product::where('is_active', true)->count();
});

$lowStockProducts = computed(function () {
    return Product::where('stock', '<', 5)->count();
});

$confirmDelete = function ($id) {
    $this->deletingProductId = $id;
    $this->showDeleteModal = true;
};

$viewProduct = function ($id) {
    $this->detailProduct = Product::with('category')->findOrFail($id);
    $this->showDetailModal = true;
};

$delete = function () {
    $product = Product::findOrFail($this->deletingProductId);
    $product->delete();

    $this->deletingProductId = null;
    $this->showDeleteModal = false;

    Flux::toast(variant: 'success', text: __('Product deleted.'));
};

?>

<x-layouts::app :title="__('Products')">
    @volt
        <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="#">Home</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Products</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            {{-- Header --}}
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="xl">Products</flux:heading>
                    <flux:subheading>{{ __('Manage your product inventory') }}</flux:subheading>
                </div>
                <flux:button variant="primary" icon="plus" href="{{ route('products.create') }}">
                    {{ __('Add Product') }}
                </flux:button>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-500">{{ __('Total Products') }}</p>
                    <p class="mt-1 text-2xl font-semibold">{{ $this->totalProducts }}</p>
                </div>
                <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-500">{{ __('Active') }}</p>
                    <p class="mt-1 text-2xl font-semibold text-green-600">{{ $this->activeProducts }}</p>
                </div>
                <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-500">{{ __('Low Stock') }}</p>
                    <p class="mt-1 text-2xl font-semibold text-red-600">{{ $this->lowStockProducts }}</p>
                </div>
            </div>

            {{-- Search --}}
            <flux:input size="md" wire:model.live="search" type="search" placeholder="Search by name or SKU..." />

            {{-- Table --}}
            <flux:table :paginate="$this->products">
                <flux:table.columns>
                    <flux:table.column
                        sortable
                        :sorted="$sortBy === 'name'"
                        :direction="$sortDirection"
                        wire:click="sort('name')"
                    >
                        Name
                    </flux:table.column>

                    <flux:table.column>
                        Category
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        :sorted="$sortBy === 'sku'"
                        :direction="$sortDirection"
                        wire:click="sort('sku')"
                    >
                        SKU
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        :sorted="$sortBy === 'price'"
                        :direction="$sortDirection"
                        wire:click="sort('price')"
                    >
                        Price
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        :sorted="$sortBy === 'stock'"
                        :direction="$sortDirection"
                        wire:click="sort('stock')"
                    >
                        Stock
                    </flux:table.column>

                    <flux:table.column
                        sortable
                        :sorted="$sortBy === 'is_active'"
                        :direction="$sortDirection"
                        wire:click="sort('is_active')"
                    >
                        Status
                    </flux:table.column>

                    <flux:table.column>
                        Actions
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->products as $product)
                        <flux:table.row :key="$product->id">
                            <flux:table.cell class="font-medium">{{ $product->name }}</flux:table.cell>

                            <flux:table.cell>
                                <flux:badge size="sm" inset="top bottom">
                                    {{ $product->category?->name ?? '-' }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell variant="strong">
                                <code class="text-xs">{{ $product->sku }}</code>
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ Number::currency($product->price, 'IDR', 'id') }}
                            </flux:table.cell>

                            <flux:table.cell>
                                @php $lowStock = $product->stock < 5; @endphp
                                <flux:badge :color="$lowStock ? 'red' : 'green'" size="sm" inset="top bottom">
                                    {{ $product->stock }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge :color="$product->is_active ? 'green' : 'red'" size="sm" inset="top bottom">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:dropdown position="bottom" align="end">
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye" wire:click="viewProduct({{ $product->id }})">
                                            {{ __('View') }}
                                        </flux:menu.item>
                                        <flux:menu.item icon="pencil" href="{{ route('products.edit', ['product' => $product->id]) }}">
                                            {{ __('Edit') }}
                                        </flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger" wire:click="confirmDelete({{ $product->id }})">
                                            {{ __('Delete') }}
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            {{-- Detail Modal --}}
            <flux:modal wire:model.self="showDetailModal" class="max-w-4xl w-full">
                @if ($detailProduct)
                    <div class="space-y-8">
                        {{-- Header --}}
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <flux:heading size="xl" class="truncate">{{ $detailProduct->name }}</flux:heading>
                                <flux:subheading>{{ __('Product details') }}</flux:subheading>
                            </div>
                            <flux:badge :color="$detailProduct->is_active ? 'emerald' : 'red'" size="lg">
                                {{ $detailProduct->is_active ? 'Active' : 'Inactive' }}
                            </flux:badge>
                        </div>

                        {{-- Info Grid --}}
                        <div class="grid grid-cols-2 gap-x-10 gap-y-8">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-zinc-400">{{ __('Category') }}</p>
                                <p class="mt-1.5 text-base font-medium">{{ $detailProduct->category?->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-zinc-400">{{ __('SKU') }}</p>
                                <p class="mt-1.5 text-base font-medium">
                                    <code class="rounded-md bg-zinc-100 px-2 py-0.5 text-sm dark:bg-zinc-700">{{ $detailProduct->sku }}</code>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-zinc-400">{{ __('Price') }}</p>
                                <p class="mt-1.5 text-2xl font-semibold">{{ Number::currency($detailProduct->price, 'IDR', 'id') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-zinc-400">{{ __('Stock') }}</p>
                                <p class="mt-1.5 text-2xl font-semibold">
                                    <span :class="$detailProduct->stock < 5 ? 'text-red-600' : 'text-green-600'">{{ $detailProduct->stock }}</span>
                                    <span class="text-sm font-normal text-zinc-400">units</span>
                                </p>
                            </div>
                        </div>

                        {{-- Description --}}
                        @if ($detailProduct->description)
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-zinc-400">{{ __('Description') }}</p>
                                <p class="mt-2 text-base leading-relaxed text-zinc-600 dark:text-zinc-300">{{ $detailProduct->description }}</p>
                            </div>
                        @endif

                        {{-- Footer --}}
                        <div class="flex items-center justify-between border-t border-zinc-200 pt-4 dark:border-zinc-700">
                            <div class="flex gap-6 text-xs text-zinc-400">
                                <span>{{ __('Created') }} {{ $detailProduct->created_at->format('d M Y, H:i') }}</span>
                                <span>{{ __('Updated') }} {{ $detailProduct->updated_at->format('d M Y, H:i') }}</span>
                            </div>
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
                    <div class="flex items-start gap-4">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                            <flux:icon name="exclamation-triangle" variant="micro" class="text-red-600" />
                        </div>
                        <div>
                            <flux:heading size="lg">{{ __('Delete Product') }}</flux:heading>
                            <flux:subheading class="mt-1">
                                {{ __('Are you sure you want to delete this product? Transaction records will be preserved. This action cannot be undone.') }}
                            </flux:subheading>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-700">
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
