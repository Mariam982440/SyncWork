{{-- resources/views/employees/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('employees.index') }}" class="hover:underline text-blue-600">Employés</a>
            <span>&gt;</span>
            <span>Modifier le profil</span>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-800">
                Modifier les informations de {{ $employee->full_name }}
            </h2>
            <a href="{{ route('employees.index') }}" class="text-sm text-gray-500 hover:underline">
                Annuler
            </a>
        </div>

        {{-- Ne pas oublier le enctype="multipart/form-data" pour l'avatar --}}
        <form method="POST" action="{{ route('employees.update', $employee) }}" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Section Avatar --}}
            <div class="flex items-center gap-6 pb-6 border-b border-gray-100">
                <div class="relative">
                    @if($employee->avatar)
                        <img src="{{ Storage::url($employee->avatar) }}"
                             class="w-20 h-20 rounded-full object-cover border-2 border-gray-200" alt="Avatar actuel" />
                    @else
                        <div class="w-20 h-20 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xl font-medium">
                            {{ strtoupper(substr($employee->first_name,0,1).substr($employee->last_name,0,1)) }}
                        </div>
                    @endif
                </div>

                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photo de profil</label>
                    <input type="file" name="avatar" accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                    <p class="text-xs text-gray-400 mt-1">Format accepté : JPG, PNG (Max. 2Mo). Laissez vide pour conserver l'actuel.</p>
                    @error('avatar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Informations Civiles --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                    <input type="text" id="first_name" name="first_name" 
                           value="{{ old('first_name', $employee->first_name) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('first_name') border-red-500 @enderror" required />
                    @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" id="last_name" name="last_name" 
                           value="{{ old('last_name', $employee->last_name) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('last_name') border-red-500 @enderror" required />
                    @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Contact --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Adresse e-mail</label>
                <input type="email" id="email" name="email" 
                       value="{{ old('email', $employee->email) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('email') border-red-500 @enderror" required />
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Poste et département --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Département</label>
                    <input type="text" id="department" name="department" 
                           value="{{ old('department', $employee->department) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('department') border-red-500 @enderror" required />
                    @error('department') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Poste occupé</label>
                    <input type="text" id="position" name="position" 
                           value="{{ old('position', $employee->position) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('position') border-red-500 @enderror" required />
                    @error('position') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Date d'embauche --}}
            <div>
                <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-1">Date d'embauche</label>
                <input type="date" id="hire_date" name="hire_date" 
                       value="{{ old('hire_date', $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('hire_date') border-red-500 @enderror" required />
                @error('hire_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Actions du formulaire --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('employees.index') }}"
                   class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 transition">
                    Annuler
                </a>
                <button type="submit"
                        class="bg-blue-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-800 transition shadow-sm">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</x-app-layout>