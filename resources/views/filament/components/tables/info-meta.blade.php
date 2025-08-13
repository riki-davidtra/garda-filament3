<div class="space-y-4">
    <div class="grid grid-cols-2 gap-2">
        <div>
            <p class="text-sm text-gray-500">Dibuat Oleh</p>
            <p class="font-semibold">{{ $record->creator->name ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Dibuat Pada</p>
            <p class="font-semibold">{{ $record->created_at ? $record->created_at->format('d M Y H:i') : '-' }}</p>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-2">
        <div>
            <p class="text-sm text-gray-500">Diperbarui Oleh</p>
            <p class="font-semibold">{{ $record->updater->name ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Diperbarui Pada</p>
            <p class="font-semibold">{{ $record->updated_at ? $record->updated_at->format('d M Y H:i') : '-' }}</p>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-2">
        <div>
            <p class="text-sm text-gray-500">Dihapus Oleh</p>
            <p class="font-semibold">{{ $record->deleter->name ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Dihapus Pada</p>
            <p class="font-semibold">{{ $record->deleted_at ? $record->deleted_at->format('d M Y H:i') : '-' }}</p>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-2">
        <div>
            <p class="text-sm text-gray-500">Dipulihkan Oleh</p>
            <p class="font-semibold">{{ $record->restorer->name ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Dipulihkan Pada</p>
            <p class="font-semibold">{{ $record->restored_at ? $record->restored_at->format('d M Y H:i') : '-' }}</p>
        </div>
    </div>
</div>
