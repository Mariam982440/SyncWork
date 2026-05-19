@php $balance = $employee->leaveBalance(now()->year); @endphp

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Acquis</p>
        <p class="text-2xl font-semibold text-gray-800 mt-1">
            {{ $balance?->accrued_days ?? 0 }} <span class="text-sm font-normal text-gray-400">j</span>
        </p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Utilisés</p>
        <p class="text-2xl font-semibold text-gray-800 mt-1">
            {{ $balance?->used_days ?? 0 }} <span class="text-sm font-normal text-gray-400">j</span>
        </p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Disponibles</p>
        <p class="text-2xl font-semibold {{ ($balance?->available_days ?? 0) > 0 ? 'text-green-600' : 'text-red-500' }} mt-1">
            {{ $balance?->available_days ?? 0 }} <span class="text-sm font-normal text-gray-400">j</span>
        </p>
    </div>
</div>