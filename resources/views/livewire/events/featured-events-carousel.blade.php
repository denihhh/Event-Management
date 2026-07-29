<div class="space-y-4">
    @if ($featuredEvents->isNotEmpty())
        <div
            x-data="{
                timer: null,
                isHovered: false,
                init() {
                    this.startAutoScroll();
                },
                startAutoScroll() {
                    this.stopAutoScroll();
                    this.timer = setInterval(() => {
                        if (!this.isHovered) {
                            this.next();
                        }
                    }, 5000);
                },
                stopAutoScroll() {
                    if (this.timer) clearInterval(this.timer);
                },
                next() {
                    const el = this.$refs.carousel;
                    if (!el) return;
                    const card = el.querySelector('.snap-start');
                    const cardWidth = card ? card.offsetWidth + 20 : 340;
                    if (el.scrollLeft + el.clientWidth >= el.scrollWidth - 15) {
                        el.scrollTo({ left: 0, behavior: 'smooth' });
                    } else {
                        el.scrollBy({ left: cardWidth, behavior: 'smooth' });
                    }
                },
                prev() {
                    const el = this.$refs.carousel;
                    if (!el) return;
                    const card = el.querySelector('.snap-start');
                    const cardWidth = card ? card.offsetWidth + 20 : 340;
                    if (el.scrollLeft <= 15) {
                        el.scrollTo({ left: el.scrollWidth, behavior: 'smooth' });
                    } else {
                        el.scrollBy({ left: -cardWidth, behavior: 'smooth' });
                    }
                }
            }"
            x-on:mouseenter="isHovered = true"
            x-on:mouseleave="isHovered = false"
            x-on:touchstart="isHovered = true"
            x-on:touchend="isHovered = false"
            class="space-y-4 relative group/carousel"
        >
            <!-- Section Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <div class="flex items-center gap-2">
                        <flux:heading size="xl" level="2" class="text-xl font-bold text-zinc-900 dark:text-white">
                            {{ __('Upcoming Featured Events') }}
                        </flux:heading>
                    </div>
                    <flux:subheading class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                        {{ __('Explore top handpicked gatherings, tech summits, and workshops.') }}
                    </flux:subheading>
                </div>

                <a
                    href="{{ route('events.index') }}"
                    wire:navigate
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 transition shrink-0"
                >
                    <span>{{ __('Browse All Events') }}</span>
                    <flux:icon name="arrow-right" class="size-3.5" />
                </a>
            </div>

            <!-- Carousel Outer Relative Wrapper with Outer Arrow Navigation -->
            <div class="relative">
                <!-- Left Navigation Arrow (<) -->
                <button
                    type="button"
                    x-on:click="prev()"
                    class="absolute -left-3 sm:-left-5 top-1/2 -translate-y-1/2 z-20 size-10 rounded-full bg-white/90 dark:bg-zinc-900/90 text-zinc-800 dark:text-white border border-zinc-200 dark:border-zinc-700/80 shadow-lg hover:scale-110 active:scale-95 transition flex items-center justify-center backdrop-blur-md opacity-0 group-hover/carousel:opacity-100 focus:opacity-100"
                    title="{{ __('Previous events') }}"
                >
                    <flux:icon name="chevron-left" class="size-5" />
                </button>

                <!-- Horizontal Scroll Track -->
                <div
                    x-ref="carousel"
                    class="flex gap-5 overflow-x-auto snap-x snap-mandatory scroll-smooth no-scrollbar [&::-webkit-scrollbar]:hidden [scrollbar-width:none] [ms-overflow-style:none] py-2 px-1 border-y border-transparent"
                >
                    @foreach ($featuredEvents as $event)
                        <x-cards.featured-event-card
                            wire:key="featured-carousel-{{ $event->id }}"
                            :event="$event"
                        />
                    @endforeach
                </div>

                <!-- Right Navigation Arrow (>) -->
                <button
                    type="button"
                    x-on:click="next()"
                    class="absolute -right-3 sm:-right-5 top-1/2 -translate-y-1/2 z-20 size-10 rounded-full bg-white/90 dark:bg-zinc-900/90 text-zinc-800 dark:text-white border border-zinc-200 dark:border-zinc-700/80 shadow-lg hover:scale-110 active:scale-95 transition flex items-center justify-center backdrop-blur-md opacity-0 group-hover/carousel:opacity-100 focus:opacity-100"
                    title="{{ __('Next events') }}"
                >
                    <flux:icon name="chevron-right" class="size-5" />
                </button>
            </div>
        </div>

        <!-- Image Lightbox Modal for Card Poster Previews -->
        <x-ui.image-lightbox />
    @endif
</div>
