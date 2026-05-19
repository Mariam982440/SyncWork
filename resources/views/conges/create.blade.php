<x-app-layout>
    <x-slot name="header">Nouvelle demande de congé</x-slot>

    @include('conges.partials.balance-widget')

    @error('blocked')
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ $message }}
        </div>
    @enderror

    <div class="max-w-lg bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('conges.store') }}">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <x-input-field name="start_date" label="Date de début"
                               type="date" :value="old('start_date')" required />
                <x-input-field name="end_date" label="Date de fin"
                               type="date" :value="old('end_date')" required />
            </div>

            <div class="mt-3 text-sm text-gray-500" id="days-preview"></div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Motif <span class="text-gray-400 font-normal">(optionnel)</span>
                </label>
                <textarea name="reason" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                          placeholder="Précisez si nécessaire...">{{ old('reason') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('conges.index') }}"
                   class="px-4 py-2 text-sm text-gray-600 hover:underline">Annuler</a>
                <x-button type="submit">Envoyer la demande</x-button>
            </div>
        </form>
    </div>

    {{-- Calcul jours ouvrables en JS natif (pas de framework) --}}
    <script>
        function countWorkingDays(start, end) {
            let count = 0, cur = new Date(start);
            const last = new Date(end);
            while (cur <= last) {
                const day = cur.getDay();
                if (day !== 0 && day !== 6) count++;
                cur.setDate(cur.getDate() + 1);
            }
            return count;
        }

        const startInput = document.getElementById('start_date');
        const endInput   = document.getElementById('end_date');
        const preview    = document.getElementById('days-preview');

        function updatePreview() {
            if (startInput.value && endInput.value) {
                const days = countWorkingDays(startInput.value, endInput.value);
                preview.textContent = days > 0
                    ? `↳ ${days} jour(s) ouvrable(s) sera/seront déduit(s).`
                    : '↳ Aucun jour ouvrable sur cette période.';
            }
        }

        startInput.addEventListener('change', updatePreview);
        endInput.addEventListener('change', updatePreview);
    </script>
</x-app-layout>