<?php

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

use function Livewire\Volt\computed;
use function Livewire\Volt\state;
use function Livewire\Volt\uses;

uses(WithPagination::class);

state([
    'search' => '',
    'showModal' => false,
    'showDeleteModal' => false,
    'userId' => null,
    'name' => '',
    'email' => '',
    'password' => '',
    'password_confirmation' => '',
    'selectedRoles' => [],
]);

$roles = computed(fn() => Role::all());

$users = computed(function () {
    return User::query()
        ->where(function($query) {
            $query->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
        })
        ->latest()
        ->paginate(10);
});

$save = function () {
    $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->userId)],
    ];

    if (!$this->userId) {
        $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
    } elseif ($this->password) {
        $rules['password'] = ['string', 'min:8', 'confirmed'];
    }

    $this->validate($rules);

    $data = [
        'name' => $this->name,
        'email' => $this->email,
    ];

    if ($this->password) {
        $data['password'] = Hash::make($this->password);
    }

    if ($this->userId) {
        $user = User::find($this->userId);
        $user->update($data);
        $user->syncRoles($this->selectedRoles);
        Flux::toast(variant: 'success', text: __('User updated.'));
    } else {
        $user = User::create($data);
        $user->syncRoles($this->selectedRoles);
        Flux::toast(variant: 'success', text: __('User created.'));
    }

    $this->closeModal();
};

$edit = function ($id) {
    $user = User::find($id);
    $this->userId = $user->id;
    $this->name = $user->name;
    $this->email = $user->email;
    $this->selectedRoles = $user->roles->pluck('name')->toArray();
    $this->password = '';
    $this->password_confirmation = '';
    $this->showModal = true;
};

$confirmDelete = function ($id) {
    $this->userId = $id;
    $this->showDeleteModal = true;
};

$delete = function () {
    User::find($this->userId)->delete();
    $this->userId = null;
    $this->showDeleteModal = false;
    Flux::toast(variant: 'success', text: __('User deleted.'));
};

$closeModal = function () {
    $this->showModal = false;
    $this->userId = null;
    $this->name = '';
    $this->email = '';
    $this->password = '';
    $this->password_confirmation = '';
    $this->selectedRoles = [];
};

?>

<x-layouts::app :title="__('Users')">
    @volt
        <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="#">{{ __('Home') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('Users') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="xl">{{ __('Users') }}</flux:heading>
                    <flux:subheading>{{ __('Manage Users') }}</flux:subheading>
                </div>
                <flux:button variant="primary" icon="plus" wire:click="$set('showModal', true)">
                    {{ __('Add User') }}
                </flux:button>
            </div>

            {{-- Search --}}
            <flux:input size="md" wire:model.live="search" type="search" placeholder="{{ __('Search') }}..." />

            {{-- Table --}}
            <flux:table :paginate="$this->users">
                <flux:table.columns>
                    <flux:table.column>{{ __('Name') }}</flux:table.column>
                    <flux:table.column>{{ __('Email') }}</flux:table.column>
                    <flux:table.column>{{ __('Roles') }}</flux:table.column>
                    <flux:table.column>{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                        <flux:table.row :key="$user->id">
                            <flux:table.cell class="font-medium">{{ $user->name }}</flux:table.cell>
                            <flux:table.cell>{{ $user->email }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles as $role)
                                        <flux:badge size="sm" inset="top bottom">{{ $role->name }}</flux:badge>
                                    @endforeach
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:dropdown position="bottom" align="end">
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />
                                    <flux:menu>
                                        <flux:menu.item icon="pencil" wire:click="edit({{ $user->id }})">
                                            {{ __('Edit') }}
                                        </flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger" wire:click="confirmDelete({{ $user->id }})">
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
                        <flux:heading size="lg">{{ $userId ? __('Edit User') : __('Add User') }}</flux:heading>
                        <flux:subheading>{{ __('Manage user account details.') }}</flux:subheading>
                    </div>

                    <div class="space-y-4">
                        <flux:input wire:model="name" :label="__('Name')" required />
                        <flux:input wire:model="email" type="email" :label="__('Email')" required />
                        
                        <div class="grid grid-cols-2 gap-4">
                            <flux:input wire:model="password" type="password" :label="__('Password')" :required="!$userId" viewable />
                            <flux:input wire:model="password_confirmation" type="password" :label="__('Confirm Password')" :required="!$userId" viewable />
                        </div>

                        <div>
                            <flux:label>{{ __('Roles') }}</flux:label>
                            <div class="mt-2 flex flex-wrap gap-4">
                                @foreach($this->roles as $role)
                                    <flux:checkbox wire:model="selectedRoles" :value="$role->name" :label="$role->name" />
                                @endforeach
                            </div>
                        </div>

                        @if($userId)
                            <p class="text-xs text-zinc-500 italic">{{ __('Leave password blank to keep current password.') }}</p>
                        @endif
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
                            <flux:heading size="lg">{{ __('Delete User') }}</flux:heading>
                            <flux:subheading class="mt-1">
                                {{ __('Are you sure you want to delete this user? This action cannot be undone.') }}
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
