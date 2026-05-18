{{-- resources/views/employees/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">Employés</x-slot>

    {{-- Barre recherche + filtres --}}
    <form method="GET" action="{{ route('employees.index') }}"
          class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Rechercher un employé..."
               class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-64 focus:ring-2 focus:ring-blue-500 focus:outline-none" />

        <select name="department"
                class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="">Tous les départements</option>
            @foreach($departments as $dept)
                <option value="{{ $dept }}" @selected(request('department') === $dept)>{{ $dept }}</option>
            @endforeach
        </select>

        <select name="position"
                class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="">Tous les postes</option>
            @foreach($positions as $pos)
                <option value="{{ $pos }}" @selected(request('position') === $pos)>{{ $pos }}</option>
            @endforeach
        </select>

        <button type="submit"
                class="bg-blue-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-800">
            Filtrer
        </button>

        @if(request()->hasAny(['search','department','position']))
            <a href="{{ route('employees.index') }}"
               class="text-sm text-gray-500 self-center hover:underline">Réinitialiser</a>
        @endif
    </form>

    <x-alert />

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-medium text-gray-700">
                {{ $employees->total() }} employé(s)
            </h2>
            @if(auth()->user()->hasRole(['admin','rh']))
                <a href="{{ route('employees.create') }}"
                   class="bg-blue-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-800">
                    + Nouvel employé
                </a>
            @endif
        </div>

        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">Employé</th>
                    <th class="px-6 py-3 text-left">Département</th>
                    <th class="px-6 py-3 text-left">Poste</th>
                    <th class="px-6 py-3 text-left">Embauche</th>
                    <th class="px-6 py-3 text-left">Statut</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($employees as $employee)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($employee->avatar)
                                    <img src="{{ Storage::url($employee->avatar) }}"
                                         class="w-8 h-8 rounded-full object-cover" alt="" />
                                @else
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700
                                                flex items-center justify-center text-xs font-medium">
                                        {{ strtoupper(substr($employee->first_name,0,1).substr($employee->last_name,0,1)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-800">{{ $employee->full_name }}</p>
                                    <p class="text-gray-400 text-xs">{{ $employee->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $employee->department }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $employee->position }}</td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ $employee->hire_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <x-badge type="success">Actif</x-badge>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('employees.show', $employee) }}"
                               class="text-blue-600 hover:underline text-xs">Voir</a>
                            @if(auth()->user()->hasRole(['admin','rh']))
                                <a href="{{ route('employees.edit', $employee) }}"
                                   class="text-gray-500 hover:underline text-xs">Modifier</a>
                                <form method="POST" action="{{ route('employees.destroy', $employee) }}"
                                      class="inline"
                                      onsubmit="return confirm('Archiver cet employé ?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:underline text-xs">Archiver</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            Aucun employé trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-100">
            {{ $employees->links() }}
        </div>
    </div>
</x-app-layout>