<?php

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Flux\Flux;

use function Livewire\Volt\computed;
use function Livewire\Volt\state;

state([
    'showModal' => false,
    'showDeleteModal' => false,
    'roleId' => null,
    'name' => '',
    'selectedPermissions' => [],
]);

$roles = computed(fn() => Role::with('permissions')->get());
$permissions = computed(fn() => Permission::all());

$save = function () {
    $this->validate([
        'name' => 'required|string|max:255|unique:roles,name,' . ($this->roleId ?? 'NULL'),
    ]);

    if ($this->roleId) {
        $role = Role::findById($this->roleId);
        $role->update(['name' => $this->name]);
        $role->syncPermissions($this->selectedPermissions);
        Flux::toast(variant: 'success', text: __('Role updated.'));
    } else {
        $role = Role::create(['name' => $this->name]);
        $role->syncPermissions($this->selectedPermissions);
        Flux::toast(variant: 'success', text: __('Role created.'));
    }

    $this->closeModal();
};

$edit = function ($id) {
    $role = Role::findById($id);
    $this->roleId = $role->id;
    $this->name = $role->name;
    $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
    $this->showModal = true;
};

$confirmDelete = function ($id) {
    $this->roleId = $id;
    $this->showDeleteModal = true;
};

$delete = function () {
    Role::findById($this->roleId)->delete();
    $this->roleId = null;
    $this->showDeleteModal = false;
    Flux::toast(variant: 'success', text: __('Role deleted.'));
};

$closeModal = function () {
    $this->showModal = false;
    $this->roleId = null;
    $this->name = '';
    $this->selectedPermissions = [];
};

?>

<x-layouts::app :title="__('Roles')">
    @volt
        <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="#">{{ __('Home') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('Roles') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="xl">{{ __('Roles') }}</flux:heading>
                    <flux:subheading>{{ __('Manage Roles') }}</flux:subheading>
                </div>
                <flux:button variant="primary" icon="plus" wire:click="$set('showModal', true)">
                    {{ __('Add Role') }}
                </flux:button>
            </div>

            {{-- Table --}}
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Name') }}</flux:table.column>
                    <flux:table.column>{{ __('Permissions') }}</flux:table.column>
                    <flux:table.column>{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->roles as $role)
                        <flux:table.row :key="$role->id">
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $role->name }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($role->permissions as $permission)
                                        <flux:badge size="sm" inset="top bottom">{{ $permission->name }}</flux:badge>
                                    @empty
                                        <span class="text-xs text-zinc-500 italic">{{ __('No permissions') }}</span>
                                    @endforelse
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:dropdown position="bottom" align="end">
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />
                                    <flux:menu>
                                        <flux:menu.item icon="pencil" wire:click="edit({{ $role->id }})">
                                            {{ __('Edit') }}
                                        </flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger" wire:click="confirmDelete({{ $role->id }})">
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
            <flux:modal wire:model.self="showModal" class="max-w-2xl w-full">
                <form wire:submit="save" class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ $roleId ? __('Edit Role') : __('Add Role') }}</flux:heading>
                        <flux:subheading>{{ __('Define role name and assign permissions.') }}</flux:subheading>
                    </div>

                    <div class="space-y-4">
                        <flux:input wire:model="name" :label="__('Name')" required />
                        
                        <div>
                            <flux:label>{{ __('Permissions') }}</flux:label>
                            <div class="mt-2 grid grid-cols-2 gap-2 md:grid-cols-3">
                                @foreach($this->permissions as $permission)
                                    <flux:checkbox wire:model="selectedPermissions" 
                                        :value="$permission->name" 
                                        :label="$permission->name" />
                                @endforeach
                            </div>
                        </div>
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
                            <flux:heading size="lg">{{ __('Delete Role') }}</flux:heading>
                            <flux:subheading class="mt-1">
                                {{ __('Are you sure you want to delete this role?') }}
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
