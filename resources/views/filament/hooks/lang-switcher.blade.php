<div>
    <x-filament::dropdown
        maxHeight="250px"
        placement="left-start"
        teleport="true"
    >
        <x-slot name="trigger">
            <div class="p-2 flex items-center justify-start gap-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md">
                <x-filament::icon
                    icon="heroicon-c-chevron-left"
                    class="mx-1 h-5 w-5 text-gray-500 dark:text-gray-400"
                />
                {{ __('Language') }}
            </div>
        </x-slot>

        <x-filament::dropdown.header
            class="font-semibold"
            color="gray"
            icon="heroicon-o-language"
        >
            {{ __('Select Language') }}
        </x-filament::dropdown.header>

        <x-filament::dropdown.list>
            <x-filament::dropdown.list.item
                :color="(app()->getLocale() === 'en') ? 'primary' : null"
                icon="heroicon-m-chevron-right"
                :href="url('lang/en')"
                tag="a"
            >
                English
            </x-filament::dropdown.list.item>

            <x-filament::dropdown.list.item
                :color="(app()->getLocale() === 'ro') ? 'primary' : null"
                icon="heroicon-m-chevron-right"
                :href="url('lang/ro')"
                tag="a"
            >
                Română
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
