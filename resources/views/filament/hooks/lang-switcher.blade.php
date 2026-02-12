<div>
    <x-filament::dropdown maxHeight="250px" placement="left-start" teleport="true">
        <x-slot name="trigger">
            <div
                class="fi-dropdown-list-item fi-dropdown-list-item-color-gray fi-color-gray flex w-full items-center gap-2 whitespace-nowrap rounded-md p-2 text-sm outline-none transition-colors duration-75 hover:bg-gray-50 focus-visible:bg-gray-50 disabled:pointer-events-none disabled:opacity-70 dark:hover:bg-white/5 dark:focus-visible:bg-white/5">
                <x-filament::icon icon="heroicon-c-chevron-left" class="mx-1 h-5 w-5 text-gray-500 dark:text-gray-400" />
                {{ __('Language') }}
            </div>
        </x-slot>

        <x-filament::dropdown.header class="font-semibold" color="gray" icon="heroicon-o-language">
            {{ __('Select Language') }}
        </x-filament::dropdown.header>

        <x-filament::dropdown.list>
            <x-filament::dropdown.list.item :color="(app()->getLocale() === 'en') ? 'primary' : null" icon="heroicon-m-chevron-right" :href="url('lang/en')"
                tag="a">
                English
            </x-filament::dropdown.list.item>

            <x-filament::dropdown.list.item :color="(app()->getLocale() === 'ro') ? 'primary' : null" icon="heroicon-m-chevron-right" :href="url('lang/ro')"
                tag="a">
                Română
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
