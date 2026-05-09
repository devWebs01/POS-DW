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
        <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="#">Home</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Products</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <div class="flex items-center justify-between">
                <flux:heading size="lg">Products</flux:heading>
                <flux:button variant="primary" icon="plus" href="{{ route('products.create') }}">
                    {{ __('Add') }}
                </flux:button>
            </div>

            <flux:input size="md" wire:model.live="search" type="search" placeholder="Filter by name or SKU..." />

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
                            <flux:table.cell>{{ $product->name }}</flux:table.cell>

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
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">{{ $detailProduct->name }}</flux:heading>
                            <flux:subheading>{{ __('Product details') }}</flux:subheading>
                        </div>

                        <flux:field variant="labeled">
                            <flux:label>{{ __('Category') }}</flux:label>
                            <flux:input :value="$detailProduct->category?->name ?? '-'" readonly />
                        </flux:field>

                        <flux:field variant="labeled">
                            <flux:label>{{ __('SKU') }}</flux:label>
                            <flux:input :value="$detailProduct->sku" readonly />
                        </flux:field>

                        <div class="grid grid-cols-2 gap-4">
                            <flux:field variant="labeled">
                                <flux:label>{{ __('Price') }}</flux:label>
                                <flux:input :value="Number::currency($detailProduct->price, 'IDR', 'id')" readonly />
                            </flux:field>

                            <flux:field variant="labeled">
                                <flux:label>{{ __('Stock') }}</flux:label>
                                <flux:input :value="$detailProduct->stock" readonly />
                            </flux:field>
                        </div>

                        <flux:field variant="labeled">
                            <flux:label>{{ __('Status') }}</flux:label>
                            <flux:badge :color="$detailProduct->is_active ? 'green' : 'red'" size="sm">
                                {{ $detailProduct->is_active ? 'Active' : 'Inactive' }}
                            </flux:badge>
                        </flux:field>

                        @if ($detailProduct->description)
                            <flux:field variant="labeled">
                                <flux:label>{{ __('Description') }}</flux:label>
                                <flux:textarea :value="$detailProduct->description" readonly />
                            </flux:field>
                        @endif

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-zinc-500">{{ __('Created') }}</span>
                                <p>{{ $detailProduct->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div>
                                <span class="font-medium text-zinc-500">{{ __('Updated') }}</span>
                                <p>{{ $detailProduct->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

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
                        <flux:heading size="lg">{{ __('Delete Product') }}</flux:heading>
                        <flux:subheading>
                            {{ __('Are you sure you want to delete this product? Transaction records will be preserved. This action cannot be undone.') }}
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
