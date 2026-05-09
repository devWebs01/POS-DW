<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;

use Flux\Flux;
use Illuminate\Support\Str;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Livewire\Volt\computed;
use function Livewire\Volt\state;

name('products.create');
middleware('auth');
middleware('verified');

state([
    'user_id', 
    'invoice_number', 
    'total_amount',
        'paid_amount', 
        'change_amount', 
        'payment_method', 
        'notes',
]);

$categoryOptions = computed(function () {
    return Category::orderBy('name')->get();
});

$updatedName = function () {
    if (empty($this->slug) || $this->slug === str()->slug($this->name)) {
        $this->slug = str()->slug($this->name);
    }
};

$save = function () {
    if (empty($this->slug)) {
        $this->slug = str()->slug($this->name);
    }

    if (empty($this->sku)) {
        $this->sku = 'PRD-'.strtoupper(Str::random(8));
    }

    $validated = $this->validate([
        'category_id' => 'required|exists:categories,id',
        'name' => 'required|string|max:200',
        'slug' => 'required|string|max:220|unique:products,slug',
        'sku' => 'required|string|max:50|unique:products,sku',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'description' => 'nullable|string',
        'is_active' => 'boolean',
    ]);

    Product::create($validated);

    $this->reset(['category_id', 'name', 'slug', 'sku', 'price', 'stock', 'description']);

    Flux::toast(variant: 'success', text: __('Product created.'));

    $this->dispatch('product-created');
};

$clearErrors = function () {
    $this->resetErrorBag();
};

?>

<x-layouts::app :title="__('Create Product')">
    @volt
        <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="#">Home</flux:breadcrumbs.item>
                <flux:breadcrumbs.item href="{{ route('products.index') }}">Products</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('Create') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <div>
                <flux:heading size="xl">{{ __('Create Product') }}</flux:heading>
                <flux:subheading>{{ __('Add a new product to inventory.') }}</flux:subheading>
            </div>

            <form wire:submit="save">
                <div class="space-y-8">
                    {{-- Basic Information --}}
                    <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                        <div class="mb-6">
                            <flux:heading size="lg">{{ __('Basic Information') }}</flux:heading>
                            <flux:subheading>{{ __('Product name, category, and identifiers.') }}</flux:subheading>
                        </div>

                        <div class="space-y-5">
                            <flux:select wire:model="category_id" :label="__('Category')" placeholder="Choose category...">
                                @foreach ($this->categoryOptions as $category)
                                    <flux:select.option value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>

                            <flux:input wire:model="name" :label="__('Product Name')" placeholder="Enter product name" required autofocus />

                            <flux:input wire:model="slug" :label="__('Slug')" placeholder="auto-generated from name" />

                            <div class="grid grid-cols-2 gap-5">
                                <flux:input wire:model="sku" :label="__('SKU')" placeholder="auto-generated if empty" />
                                <flux:input wire:model="stock" :label="__('Stock')" type="number" min="0" placeholder="0" />
                            </div>
                        </div>
                    </div>

                    {{-- Pricing --}}
                    <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                        <div class="mb-6">
                            <flux:heading size="lg">{{ __('Pricing') }}</flux:heading>
                            <flux:subheading>{{ __('Set the product price.') }}</flux:subheading>
                        </div>

                        <flux:input wire:model="price" :label="__('Price')" type="number" step="0.01" min="0" placeholder="0.00" prefix="Rp" />
                    </div>

                    {{-- Details --}}
                    <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                        <div class="mb-6">
                            <flux:heading size="lg">{{ __('Details') }}</flux:heading>
                            <flux:subheading>{{ __('Product description and status.') }}</flux:subheading>
                        </div>

                        <div class="space-y-5">
                            <flux:textarea wire:model="description" :label="__('Description')" placeholder="Enter product description..." />

                            <flux:field variant="inline">
                                <flux:label>{{ __('Active') }}</flux:label>
                                <flux:switch wire:model.live="is_active" />
                            </flux:field>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-2">
                        <flux:button variant="filled" href="{{ route('products.index') }}">{{ __('Cancel') }}</flux:button>
                        <flux:button variant="primary" type="submit">{{ __('Save Product') }}</flux:button>
                    </div>
                </div>
            </form>
        </div>
    @endvolt
</x-layouts::app>
