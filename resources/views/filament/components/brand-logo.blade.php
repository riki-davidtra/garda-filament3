<a href="{{ route('filament.admin.pages.dashboard') }}" class="flex items-center space-x-3 px-2 py-1">
    <img src="{{ App::make('settingItems')['favicon']->value ?? asset('assets/images/favicon.png') }}" alt="Logo" class="h-8 w-8 rounded object-contain">
    <span class="text-sm font-bold text-gray-900 dark:text-white">
        {{ App::make('settingItems')['site_name']->value ?? 'Site Name' }}
    </span>
</a>
