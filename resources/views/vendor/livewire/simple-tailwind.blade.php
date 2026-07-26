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
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
            <span>
                @if ($paginator->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-zinc-400 bg-zinc-100 border border-zinc-200 cursor-not-allowed leading-5 rounded-md dark:bg-zinc-900/60 dark:border-zinc-800 dark:text-zinc-600">
                        {!! __('pagination.previous') !!}
                    </span>
                @else
                    <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-200 leading-5 rounded-md hover:text-zinc-900 focus:outline-none focus:ring ring-purple-300 focus:border-purple-400 active:bg-zinc-100 transition ease-in-out duration-150 dark:bg-zinc-900 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
                        {!! __('pagination.previous') !!}
                    </button>
                @endif
            </span>

            <span>
                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-200 leading-5 rounded-md hover:text-zinc-900 focus:outline-none focus:ring ring-purple-300 focus:border-purple-400 active:bg-zinc-100 transition ease-in-out duration-150 dark:bg-zinc-900 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
                        {!! __('pagination.next') !!}
                    </button>
                @else
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-zinc-400 bg-zinc-100 border border-zinc-200 cursor-not-allowed leading-5 rounded-md dark:bg-zinc-900/60 dark:border-zinc-800 dark:text-zinc-600">
                        {!! __('pagination.next') !!}
                    </span>
                @endif
            </span>
        </nav>
    @endif
</div>
