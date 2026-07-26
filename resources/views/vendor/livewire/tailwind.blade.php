@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Results Counter -->
            <div>
                <p class="text-xs text-zinc-600 leading-5 dark:text-zinc-400 font-medium">
                    <span>{!! __('Showing') !!}</span>
                    <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $paginator->firstItem() }}</span>
                    <span>{!! __('to') !!}</span>
                    <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $paginator->lastItem() }}</span>
                    <span>{!! __('of') !!}</span>
                    <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $paginator->total() }}</span>
                    <span>{!! __('results') !!}</span>
                </p>
            </div>

            <!-- Controls Toolbar -->
            <div class="flex items-center gap-1.5 flex-wrap justify-center sm:justify-end">
                <!-- First Page (|<) -->
                @if ($paginator->onFirstPage())
                    <span class="p-1.5 text-zinc-300 dark:text-zinc-700 cursor-not-allowed" aria-disabled="true" title="First Page">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 19L11 12l7-7M6 5v14"/></svg>
                    </span>
                @else
                    <button type="button" wire:click="gotoPage(1, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="p-1.5 rounded-lg text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer" title="First Page">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 19L11 12l7-7M6 5v14"/></svg>
                    </button>
                @endif

                <!-- Previous Page (<) -->
                @if ($paginator->onFirstPage())
                    <span class="p-1.5 text-zinc-300 dark:text-zinc-700 cursor-not-allowed" aria-disabled="true" title="Previous Page">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                @else
                    <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="p-1.5 rounded-lg text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer" title="Previous Page">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                @endif

                <!-- Page Numbers -->
                <div class="flex items-center gap-1">
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span class="px-2 py-1 text-xs font-semibold text-zinc-400 dark:text-zinc-500">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span class="px-3 py-1.5 text-xs font-extrabold rounded-lg bg-zinc-900 text-white dark:bg-purple-600 dark:text-white shadow-xs">
                                        {{ $page }}
                                    </span>
                                @else
                                    <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="px-3 py-1.5 text-xs font-medium rounded-lg text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>

                <!-- Next Page (>) -->
                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="p-1.5 rounded-lg text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer" title="Next Page">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                @else
                    <span class="p-1.5 text-zinc-300 dark:text-zinc-700 cursor-not-allowed" aria-disabled="true" title="Next Page">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @endif

                <!-- Last Page (>|) -->
                @if ($paginator->currentPage() < $paginator->lastPage())
                    <button type="button" wire:click="gotoPage({{ $paginator->lastPage() }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="p-1.5 rounded-lg text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer" title="Last Page">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 5l7 7-7 7M18 5v14"/></svg>
                    </button>
                @else
                    <span class="p-1.5 text-zinc-300 dark:text-zinc-700 cursor-not-allowed" aria-disabled="true" title="Last Page">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 5l7 7-7 7M18 5v14"/></svg>
                    </span>
                @endif

                <!-- Jump To Page Dropdown -->
                <div class="flex items-center gap-2 ml-2 pl-2 border-l border-zinc-200 dark:border-zinc-800">
                    <select
                        x-on:change="$wire.gotoPage($event.target.value, '{{ $paginator->getPageName() }}')"
                        class="px-2.5 py-1 text-xs font-bold rounded-lg bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white shadow-xs focus:ring-2 focus:ring-purple-500 cursor-pointer"
                        title="Jump to specific page"
                    >
                        @for ($p = 1; $p <= $paginator->lastPage(); $p++)
                            <option value="{{ $p }}" {{ $p == $paginator->currentPage() ? 'selected' : '' }}>
                                {{ $p }}
                            </option>
                        @endfor
                    </select>
                    <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Page</span>
                </div>
            </div>
        </nav>
    @endif
</div>
