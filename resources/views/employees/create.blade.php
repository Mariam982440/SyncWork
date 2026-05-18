{{-- resources/views/employees/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">Nouvel employé</x-slot>

    <div class="max-w-2xl bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <x-input-field name="first_name" label="Prénom" :value="old('first_name')" required />
                <x-input-field name="last_name" label="Nom" :value="old('last_name')" required />
            </div>

            <x-input-field name="email" label="E-mail professionnel" type="email"
                           :value="old('email')" required class="mt-4" />

            <x-input-field name="phone" label="Téléphone" :value="old('phone')" class="mt-4" />

            <div class="grid grid-cols-2 gap-4 mt-4">
                <x-input-field name="department" label="Département" :value="old('department')" required />
                <x-input-field name="position" label="Poste" :value="old('position')" required />
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <x-input-field name="hire_date" label="Date d'embauche"
                               type="date" :value="old('hire_date')" required />
                <x-input-field name="salary" label="Salaire mensuel (MAD)"
                               type="number" step="0.01" :value="old('salary')" />
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Photo (optionnel)</label>
                <input type="file" name="avatar" accept="image/*"
                       class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                              file:rounded-lg file:border-0 file:bg-blue-50
                              file:text-blue-700 hover:file:bg-blue-100" />
                <x-input-error :messages="$errors->get('avatar')" class="mt-1" />
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('employees.index') }}"
                   class="px-4 py-2 text-sm text-gray-600 hover:underline">Annuler</a>
                <x-button type="submit">Créer l'employé</x-button>
            </div>
        </form>
    </div>
</x-app-layout>