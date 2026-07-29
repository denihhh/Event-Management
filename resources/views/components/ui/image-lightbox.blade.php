<div
    x-data="{ open: false, src: '', alt: '' }"
    x-on:preview-image.window="src = $event.detail.src; alt = $event.detail.alt || ''; open = true"
>
    <template x-teleport="body">
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            x-on:keydown.escape.window="open = false"
            x-on:click="open = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/85 backdrop-blur-md cursor-zoom-out"
            style="display: none;"
        >
            <div class="relative max-w-5xl max-h-[90vh] overflow-hidden rounded-2xl shadow-2xl border border-white/10 bg-zinc-950/90 cursor-default" x-on:click.stop>
                <button
                    type="button"
                    x-on:click="open = false"
                    class="absolute top-4 right-4 z-10 p-2.5 rounded-full bg-black/60 hover:bg-black text-white/90 backdrop-blur-md transition shadow-md hover:scale-105"
                    title="{{ __('Close preview') }}"
                >
                    <flux:icon name="x-mark" class="size-6" />
                </button>

                <div class="p-2 sm:p-4 flex items-center justify-center min-h-[200px]">
                    <img :src="src" :alt="alt" class="max-h-[82vh] w-auto max-w-full object-contain rounded-xl shadow-lg" />
                </div>

                <div x-show="alt" class="px-4 py-2 text-center text-xs font-medium text-zinc-300 bg-black/40 backdrop-blur-xs border-t border-white/5">
                    <span x-text="alt"></span>
                </div>
            </div>
        </div>
    </template>
</div>
