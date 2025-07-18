<?php

use App\Models\User;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use function Livewire\Volt\{state, uses};
use function Laravel\Folio\{name, middleware};

uses([LivewireAlert::class]);
middleware(['auth']);

name('profile.guest');

state([
    'user' => fn () => Auth()->user(),
    'name' => fn () => $this->user->name,
    'email' => fn () => $this->user->email,
    'telp' => fn () => $this->user->telp,
    'password',
]);

$edit = function () {
    $user = $this->user;

    $validateData = $this->validate([
        'name' => 'required|min:5',
        'email' => 'required|min:5|' . Rule::unique(User::class)->ignore($user->id),
        'password' => 'min:5|nullable',
        'telp' => 'required|digits_between:11,12|' . Rule::unique(User::class)->ignore($user->id),
    ]);

    $user = $this->user;

    // Jika wire:model password terisi, lakukan update password
    if (! empty($this->password)) {
        $validateData['password'] = bcrypt($this->password);
    } else {
        // Jika wire:model password tidak terisi, gunakan password yang lama
        $validateData['password'] = $user->password;
    }
    $user->update($validateData);

    $this->alert('success', 'Proses berhasil!', [
        'position' => 'center',
        'timer' => 3000,
        'toast' => true,
    ]);
};

?>

<x-guest-layout>

    @volt
    <div>
        <x-slot name="title">{{ $user->name }}</x-slot>

        <div class="container">
            <div class="card border-0">
                <div class="alert alert-primary border-0" role="alert">
                    <strong>Data Profile</strong>
                    <p>Pada halaman edit pengguna, kamu dapat mengubah informasi pengguna.
                    </p>
                </div>
                <div class="card-body border rounded">
                    <form wire:submit="edit">
                        @csrf
                        <div class="row">
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name" id="name" aria-describedby="nameId"
                                        placeholder="Enter admin name" autofocus autocomplete="name" />
                                    @error('name')
                                        <small id="nameId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        wire:model="email" id="email" aria-describedby="emailId"
                                        placeholder="Enter admin email" />
                                    @error('email')
                                        <small id="emailId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="telp" class="form-label">Telpon</label>
                                    <input type="number" class="form-control @error('telp') is-invalid @enderror"
                                        wire:model="telp" id="telp" aria-describedby="telpId"
                                        placeholder="Enter admin telp" />
                                    @error('telp')
                                        <small id="telpId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Kata Sandi</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        wire:model="password" id="password" aria-describedby="passwordId"
                                        placeholder="Enter admin password" />
                                    @error('password')
                                        <small id="passwordId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                            <div class="col-md align-self-center text-end">
                                <span wire:loading class="spinner-border spinner-border-sm"></span>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endvolt
</x-guest-layout>
