<x-filament-panels::page>
    @if ($generatedToken)
        <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg space-y-2">
            <p class="font-semibold">Your API Token (copy it now, it won't be shown again):</p>
            <div class="flex items-center gap-2">
                <input type="text" readonly value="{{ $generatedToken }}" class="w-full p-2 rounded border" onclick="this.select()" />
            </div>
            <p class="text-sm text-gray-500">
                In n8n, split this at the "|" character: the part before it is your Token ID, the part after is your Token Secret.
            </p>
        </div>
    @else
        <p>Click "Generate API Token" above to create a new token for use in n8n.</p>
    @endif
</x-filament-panels::page>