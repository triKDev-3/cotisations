<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Modifier le Compte') }} : {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('collecteur.jeunes.update', $user) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Nom Complet')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="old('name', $user->name)" required autofocus autocomplete="name" />
                        </div>

                        <!-- Email Address -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                :value="old('email', $user->email)" required autocomplete="username" />
                        </div>

                        <!-- Numero Compte -->
                        <div>
                            <x-input-label for="numero_compte" :value="__('Numéro de Compte')" />
                            <x-text-input id="numero_compte" class="block mt-1 w-full" type="text" name="numero_compte"
                                :value="old('numero_compte', $user->numero_compte)" required />
                        </div>

                        <!-- Password (Optional) -->
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Changer le mot de
                                passe (Laisser vide pour ne pas changer)</h4>

                            <div class="mb-4">
                                <x-input-label for="password" :value="__('Nouveau mot de passe')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                                    autocomplete="new-password" />
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                                    name="password_confirmation" autocomplete="new-password" />
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                            <button type="button"
                                onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce compte ? Cette action est irréversible.')) document.getElementById('delete-form').submit();"
                                class="text-red-600 hover:text-red-900 text-sm font-semibold">
                                {{ __('Supprimer ce compte') }}
                            </button>

                            <div class="flex gap-4">
                                <a href="{{ route('collecteur.dashboard') }}"
                                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                    {{ __('Annuler') }}
                                </a>
                                <x-primary-button>
                                    {{ __('Mettre à jour') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>

                    <form id="delete-form" action="{{ route('collecteur.jeunes.destroy', $user) }}" method="POST"
                        class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>