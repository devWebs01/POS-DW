<?php

use Spatie\Permission\Models\Permission;
use Flux\Flux;

use function Livewire\Volt\computed;
use function Livewire\Volt\state;

state([
    'showModal' => false,
    'showDeleteModal' => false,
    'permissionId' => null,
    'name' => '',
]);

$permissions = computed(fn() => Permission::all());

$save = function () {
    $this->validate([
        'name' => 'required|string|max:255|unique:permissions,name,' . ($this->permissionId ?? 'NULL'),
    ]);

    if ($this->permissionId) {
        Permission::findById($this->permissionId)->update(['name' => $this->name]);
        Flux::toast(variant: 'success', text: __('Permission updated.'));
    } else {
        Permission::create(['name' => $this->name]);
        Flux::toast(variant: 'success', text: __('Permission created.'));
    }

    $this->closeModal();
};

$edit = function ($id) {
    $permission = Permission::findById($id);
    $this->permissionId = $permission->id;
    $this->name = $permission->name;
    $this->showModal = true;
};

$confirmDelete = function ($id) {
    $this->permissionId = $id;
    $this->showDeleteModal = true;
};

$delete = function () {
    Permission::findById($this->permissionId)->delete();
    $this->permissionId = null;
    $this->showDeleteModal = false;
    Flux::toast(variant: 'success', text: __('Permission deleted.'));
};

$closeModal = function () {
    $this->showModal = false;
    $this->permissionId = null;
    $this->name = '';
};

?>

<x-layouts::app :title="__('Permissions')">
    @volt
        <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="#">{{ __('Home') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('Permissions') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="xl">{{ __('Permissions') }}</flux:heading>
                    <flux:subheading>{{ __('Manage Permissions') }}</flux:subheading>
                </div>
                <flux:button variant="primary" icon="plus" wire:click="$set('showModal', true)">
                    {{ __('Add Permission') }}
                </flux:button>
            </div>

            {{-- Table --}}
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Name') }}</flux:table.column>
                    <flux:table.column>{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->permissions as $permission)
                        <flux:table.row :key="$permission->id">
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $permission->name }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:dropdown position="bottom" align="end">
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />
                                    <flux:menu>
                                        <flux:menu.item icon="pencil" wire:click="edit({{ $permission->id }})">
                                            {{ __('Edit') }}
                                        </flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger" wire:click="confirmDelete({{ $permission->id }})">
                                            {{ __('Delete') }}
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            {{-- Create/Edit Modal --}}
            <flux:modal wire:model.self="showModal" class="max-w-lg w-full">
                <form wire:submit="save" class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ $permissionId ? __('Edit Permission') : __('Add Permission') }}</flux:heading>
                        <flux:subheading>{{ __('Define permission name (e.g., manage-users).') }}</flux:subheading>
                    </div>

                    <div class="space-y-4">
                        <flux:input wire:model="name" :label="__('Name')" placeholder="manage-users" required />
                    </div>

                    <div class="flex justify-end gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                        <flux:button variant="filled" wire:click="closeModal">{{ __('Cancel') }}</flux:button>
                        <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
                    </div>
                </form>
            </flux:modal>

            {{-- Delete Confirmation Modal --}}
            <flux:modal wire:model.self="showDeleteModal" class="max-w-lg">
                <form wire:submit="delete" class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                            <flux:icon name="exclamation-triangle" variant="micro" class="text-red-600" />
                        </div>
                        <div>
                            <flux:heading size="lg">{{ __('Delete Permission') }}</flux:heading>
                            <flux:subheading class="mt-1">
                                {{ __('Are you sure you want to delete this permission?') }}
                            </flux:subheading>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                        <flux:button variant="filled" wire:click="$set('showDeleteModal', false)">{{ __('Cancel') }}</flux:button>
                        <flux:button variant="danger" type="submit">{{ __('Delete') }}</flux:button>
                    </div>
                </form>
            </flux:modal>
        </div>
    @endvolt
</x-layouts::app>
