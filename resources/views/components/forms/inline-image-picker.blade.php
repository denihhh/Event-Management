@props([
    'inlineImage' => null,
    'uploadedInlineImages' => [],
])

<div class="p-4 rounded-xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-200/70 dark:border-purple-900/40 space-y-3">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div>
            <span class="text-xs font-bold text-purple-900 dark:text-purple-300 flex items-center gap-1.5">
                <flux:icon name="photo" class="size-4 text-purple-600 dark:text-purple-400" />
                {{ __('Insert Inline Photos into Description') }}
            </span>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                {{ __('Upload event merchandise, t-shirts, schedule images, or venue maps directly into description.') }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-purple-700 dark:text-purple-300 bg-white dark:bg-zinc-800 border border-purple-300 dark:border-purple-700 rounded-lg hover:bg-purple-50 dark:hover:bg-zinc-700 transition">
                <flux:icon name="arrow-up-tray" class="size-3.5" />
                <span>{{ __('Upload Photo') }}</span>
                <input type="file" wire:model.live="inlineImage" accept="image/*" class="hidden" />
            </label>
        </div>
    </div>

    <div wire:loading wire:target="inlineImage" class="text-xs text-purple-600 dark:text-purple-400 flex items-center gap-1.5">
        <flux:icon name="arrow-path" class="size-3.5 animate-spin" />
        <span>{{ __('Uploading image...') }}</span>
    </div>

    @if (! empty($uploadedInlineImages))
        <div class="pt-2 border-t border-purple-200/50 dark:border-purple-800/40">
            <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400 block mb-2">{{ __('Uploaded Photos (Click to insert):') }}</span>
            <div class="flex flex-wrap gap-2">
                @foreach ($uploadedInlineImages as $img)
                    <button type="button" wire:click="insertInlineImageTag('{{ $img['url'] }}')" class="group relative rounded-lg overflow-hidden border border-purple-300 dark:border-purple-700 size-14 shrink-0 hover:ring-2 hover:ring-purple-500">
                        <img src="{{ $img['url'] }}" alt="Inline image" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                            <flux:icon name="plus" class="size-5 text-white" />
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    @endif
</div>
