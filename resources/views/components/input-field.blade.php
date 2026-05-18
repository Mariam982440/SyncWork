@props(['name', 'label', 'type' => 'text', 'value' => '', 'required' => false])

<div {{ $attributes->class([]) }}>
    <label for="{{ $name }}"
           class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if($required) <span class="text-red-500">*</span> @endif
    </label>
    <input type="{{ $type }}"
           name="{{ $name }}"
           id="{{ $name }}"
           value="{{ old($name, $value) }}"
           {{ $required ? 'required' : '' }}
           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                  focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none
                  @error($name) border-red-400 @enderror" />
    <x-input-error :messages="$errors->get($name)" class="mt-1" />
</div>