@extends('layouts.app')
@section('title', 'Home | graphco')

@section('content')
    <section class="hero-slider" data-slider>
        @foreach ($banners as $index => $banner)
            <div class="hero-slide {{ $index === 0 ? 'is-active' : '' }}"
                style="background-image:url('{{ asset('assets/admin/uploads/' . $banner->photo) }}')">
                <div class="hero-overlay"></div>
                <div class="container hero-inner">
                    <h1 class="hero-title">
                        <span>{{ $locale === 'ar' ? $banner->title_ar : $banner->title_en }}</span>
                        <i></i>
                    </h1>
                    <span class="hero-sub">
                        {!! $locale === 'ar' ? $banner->descrispantion_ar : $banner->description_en !!}
                    </span>

                    <div class="hero-cta">
                        <a href="{{ route('products.index') }}" class="btn-cta">{{ __('front.our_products') }}
                            <svg width="18" height="18" viewBox="0 0 24 24">
                                <path d="M5 12h12M13 6l6 6-6 6" stroke="#fff" stroke-width="2" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        <div class="call-chip">
                            <span class="chip-ico">
                                <svg width="20" height="20" viewBox="0 0 24 24">
                                    <path
                                        d="M6.6 10.8c1.3 2.5 3.3 4.5 5.8 5.8l2-2c.3-.3.8-.4 1.1-.2 1.2.4 2.5.6 3.9.6.5 0 .9.4.9.9v3.4c0 .5-.4.9-.9.9C10.6 21.9 2.1 13.4 2.1 2.9c0-.5.4-.9.9-.9H7c.5 0 .9.4.9.9 0 1.3.2 2.6.6 3.9.1.4 0 .8-.3 1.1l-1.6 1.6Z"
                                        fill="#fff" />
                                </svg>
                            </span>
                            <div class="chip-txt">
                                <span>{{ __('front.our_service') }}</span>
                                <a dir="ltr" href="tel:{{ $setting->phone }}">{{ $setting->phone }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="hero-dots" data-dots></div>
    </section>

   <section class="featured" data-featured>
    <div class="container featured-grid">
        <div class="featured-left">
            <h2 class="featured-title">{{ __('front.featured_products') }}</h2>
            <a href="{{ route('products.index') }}" class="featured-btn">{{ __('messages.view_all') }}</a>
            <div class="featured-arrows">
                <button class="featured-arrow" data-prev type="button" aria-label="{{ __('messages.previous') }}">
                    <svg width="20" height="20" viewBox="0 0 24 24">
                        <path d="M15 6l-6 6 6 6" fill="none" stroke="#9b51e0" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
                <button class="featured-arrow" data-next type="button" aria-label="{{ __('messages.next') }}">
                    <svg width="20" height="20" viewBox="0 0 24 24">
                        <path d="M9 6l6 6-6 6" fill="none" stroke="#9b51e0" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="featured-right">
            @if ($featuredProducts->count() > 0)
                <div class="feat-viewport">
                    <div class="feat-track" data-track>
                        @foreach ($featuredProducts as $product)
                            <a href="{{ route('product.details', $product->slug) }}" style="color: inherit; text-decoration: none;">
                                <article class="feat-card">
                                    <div class="feat-card-img">
                                        <img src="{{ asset('assets/admin/uploads/' . $product->main_image) }}"
                                            alt="{{ app()->getLocale() == 'ar' ? $product->name_ar : $product->name_en }}"
                                            loading="lazy">
                                    </div>
                                    <div class="feat-card-content">
                                        <h3 class="feat-card-title">
                                            {{ app()->getLocale() == 'ar' ? $product->name_ar : $product->name_en }}
                                        </h3>
                                        <div class="feat-sep"></div>
                                        <p class="feat-card-text">
                                            {!! Str::limit(app()->getLocale() == 'ar' ? $product->subtitle_ar ?? $product->description_ar : $product->subtitle_en ?? $product->description_en, 120) !!}
                                        </p>
                                    </div>
                                </article>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="feat-viewport">
                    <div class="feat-track">
                        <div class="feat-empty">
                            <div class="feat-empty-icon">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#9b51e0" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                            </div>
                            <p class="feat-empty-text">{{ __('messages.no_featured_products') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

    <br>
    <br>

    <section class="brands" data-brands>
        <div class="container brands-head">
            <h2 class="brands-title">{{ __('front.Our Partners') }}</h2>
        </div>
        <br>

        <div class="brands-wrap">
            <button class="brands-nav" data-prev type="button">
                <svg width="20" height="20" viewBox="0 0 24 24">
                    <path d="M15 6l-6 6 6 6" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>

            <div class="brands-viewport">
                <div class="brands-track" data-track>
                    @foreach ($brands as $brand)
                        <div class="brand-item">
                            <a href="{{ route('products.index', ['brand' => $brand->id]) }}">
                                <img src="{{ asset('assets/admin/uploads/' . $brand->photo) }}" alt="brand"> </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <button class="brands-nav" data-next type="button">
                <svg width="20" height="20" viewBox="0 0 24 24">
                    <path d="M9 6l6 6-6 6" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
        </div>
    </section>

    <section class="services">
        <div class="container services-head">
            <span class="services-kicker">{{ __('front.graphco_services') }}</span>
            <h2 class="services-title">{{ __('front.services_headline') }}</h2>
        </div>

        <div class="container services-grid">
            @foreach ($services as $service)
                <article class="service-card">
                    <div class="service-ico">
                        <img src="{{ asset('assets/admin/uploads/' . $service->icon) }}" alt="">
                    </div>
                    <h3 class="service-title">{{ $locale === 'ar' ? $service->name_ar : $service->name_en }}</h3>
                    <p class="service-text">{!! $locale === 'ar' ? $service->description_ar : $service->description_en !!}</p>
                </article>
            @endforeach
        </div>
    </section>

    @if ($bottomSection)
        <section class="about-hero"
            style="background-image:url('{{ asset('assets/admin/uploads/' . $bottomSection->photo) }}')">
            <div class="about-hero__overlay"></div>
            <div class="container about-hero__grid">
                <div class="about-hero__left">
                    <h2 class="about-hero__title">
                        {{ $locale === 'ar' ? $bottomSection->name_ar : $bottomSection->name_en }}</h2>
                    <p class="about-hero__kicker">{!! $locale === 'ar' ? $bottomSection->short_description_ar : $bottomSection->short_description_en !!}</p>
                    <p class="about-hero__kicker">{!! $locale === 'ar' ? $bottomSection->tall_description_ar : $bottomSection->tall_description_en !!}</p>
                </div>
               
            </div>
        </section>
    @endif
@endsection
