@props([
    'banner' => null,
    'existingBannerPath' => null,
])

<div class="space-y-2">
    <label class="block text-sm font-medium text-zinc-900 dark:text-zinc-100">
        {{ __('Event Banner / Thumbnail') }} <span class="text-xs text-zinc-500 font-normal">({{ __('16:9 Widescreen Recommended') }})</span>
    </label>

    @if ($banner)
        <div class="relative rounded-xl overflow-hidden aspect-[16/9] border border-purple-300 dark:border-purple-800 shadow-sm bg-zinc-900">
            <img src="{{ $banner->temporaryUrl() }}" alt="New Banner Preview" class="w-full h-full object-cover" />
            <button type="button" wire:click="removeBanner" class="absolute top-3 right-3 bg-red-600/90 hover:bg-red-600 text-white p-1.5 rounded-full backdrop-blur-xs transition">
                <flux:icon name="x-mark" class="size-5" />
            </button>
            <div class="absolute bottom-2 left-3 bg-black/60 text-white text-xs px-2 py-1 rounded-md font-medium backdrop-blur-xs">
                {{ __('16:9 Banner Preview') }}
            </div>
        </div>
    @elseif ($existingBannerPath)
        <div class="relative rounded-xl overflow-hidden aspect-[16/9] border border-zinc-200 dark:border-zinc-700 shadow-sm bg-zinc-900">
            <img src="{{ \Illuminate\Support\Facades\Storage::url($existingBannerPath) }}" alt="Current Banner" class="w-full h-full object-cover" />
            <div class="absolute top-3 right-3 flex items-center gap-2">
                <label class="cursor-pointer bg-white/90 hover:bg-white dark:bg-zinc-800/90 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-semibold px-2.5 py-1.5 rounded-lg shadow-sm backdrop-blur-xs transition">
                    {{ __('Replace') }}
                    <input type="file" wire:model.live="banner" accept="image/*" class="hidden" />
                </label>
                <button type="button" wire:click="removeBanner" class="bg-red-600/90 hover:bg-red-600 text-white p-1.5 rounded-lg backdrop-blur-xs transition" title="Remove Banner">
                    <flux:icon name="trash" class="size-4" />
                </button>
            </div>
        </div>
    @else
        <label class="flex flex-col items-center justify-center w-full aspect-[16/9] max-h-56 rounded-xl border-2 border-dashed border-zinc-300 dark:border-zinc-700 hover:border-purple-500 dark:hover:border-purple-400 bg-zinc-50 dark:bg-zinc-900/50 cursor-pointer transition">
            <div class="flex flex-col items-center justify-center py-6 text-center px-4">
                <flux:icon name="photo" class="size-10 text-zinc-400 mb-2" />
                <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Click to upload event banner') }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ __('16:9 aspect ratio recommended (e.g. 1920×1080). PNG, JPG, WEBP up to 5MB') }}</p>
            </div>
            <input type="file" wire:model.live="banner" accept="image/*" class="hidden" />
        </label>
    @endif

    @error('banner')
        <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
    @enderror
</div>
