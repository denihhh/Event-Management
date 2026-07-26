@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex gap-2 items-center justify-between">

        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-zinc-400 bg-zinc-100 border border-zinc-200 cursor-not-allowed leading-5 rounded-md dark:text-zinc-600 dark:bg-zinc-900/60 dark:border-zinc-800">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-200 leading-5 rounded-md hover:text-zinc-900 focus:outline-none focus:ring ring-purple-300 focus:border-purple-400 active:bg-zinc-100 transition ease-in-out duration-150 dark:bg-zinc-900 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-200 leading-5 rounded-md hover:text-zinc-900 focus:outline-none focus:ring ring-purple-300 focus:border-purple-400 active:bg-zinc-100 transition ease-in-out duration-150 dark:bg-zinc-900 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-zinc-400 bg-zinc-100 border border-zinc-200 cursor-not-allowed leading-5 rounded-md dark:text-zinc-600 dark:bg-zinc-900/60 dark:border-zinc-800">
                {!! __('pagination.next') !!}
            </span>
        @endif

    </nav>
@endif
