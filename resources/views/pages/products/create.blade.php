<?php

use App\Models\Category;
use App\Models\Product;
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
    'category_id' => null,
    'name' => '',
    'slug' => '',
    'sku' => '',
    'price' => null,
    'stock' => null,
    'description' => '',
    'is_active' => true,
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
        <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="#">Home</flux:breadcrumbs.item>
                <flux:breadcrumbs.item href="{{ route('products.index') }}">Products</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('Create') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <form wire:submit="save" class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Create Product') }}</flux:heading>
                    <flux:subheading>{{ __('Add a new product to inventory.') }}</flux:subheading>
                </div>

                <flux:select wire:model="category_id" :label="__('Category')" placeholder="Choose category...">
                    @foreach ($this->categoryOptions as $category)
                        <flux:select.option value="{{ $category->id }}">
                            {{ $category->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input wire:model="name" :label="__('Name')" required autofocus />

                <flux:input wire:model="slug" :label="__('Slug')" />

                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="sku" :label="__('SKU')" />
                    <flux:input wire:model="price" :label="__('Price')" type="number" step="0.01" min="0" />
                </div>

                <flux:input wire:model="stock" :label="__('Stock')" type="number" min="0" />

                <flux:textarea wire:model="description" :label="__('Description')" />

                <flux:field variant="inline">
                    <flux:label>{{ __('Active') }}</flux:label>
                    <flux:switch wire:model.live="is_active" />
                </flux:field>

                <div class="flex justify-end gap-2">
                    <flux:button variant="filled" href="{{ route('products.index') }}">{{ __('Cancel') }}</flux:button>
                    <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
                </div>
            </form>

        </div>
    @endvolt
</x-layouts::app>
