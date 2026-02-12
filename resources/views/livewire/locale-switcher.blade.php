<div class="filament-global-search">
    <label class="block text-sm font-medium text-gray-700">{{ __('Language') }}</label>
    <select wire:model="locale" wire:change="updatedLocale($event.target.value)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @foreach($locales as $code => $label)
            <option value="{{ $code }}">{{ $label }}</option>
        @endforeach
    </select>

    <script>
        window.addEventListener('locale-changed', function() {
            // simple reload to pick up translations immediately
            location.reload();
        });
    </script>
</div>
