@php
    $locales = $locales ?? config('app.available_locales', ['en' => 'English']);
@endphp

<div class="filament-global-search">
    <label class="block text-sm font-medium text-gray-700">{{ __('Language') }}</label>

    {{-- If rendered inside a Livewire component this will bind; otherwise the select will still render and submit via JS listener --}}
    <select @if(isset($this) && method_exists($this, 'isLivewireComponent') && $this->isLivewireComponent()) wire:model="locale" wire:change="updatedLocale($event.target.value)" @else id="locale-switcher-select" @endif class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @foreach ($locales as $code => $label)
            <option value="{{ $code }}">{{ $label }}</option>
        @endforeach
    </select>

    <script>
        window.addEventListener('locale-changed', function() {
            location.reload();
        });

        // If the select is outside Livewire, post to the server via fetch and reload
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('locale-switcher-select');
            if (!el) return;
            el.addEventListener('change', function () {
                var value = this.value;
                fetch('/locale', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ locale: value })
                }).then(function () { location.reload(); });
            });
        });
    </script>
</div>
