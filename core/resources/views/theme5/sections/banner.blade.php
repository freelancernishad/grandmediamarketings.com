@php
$content = content('banner.content');
$counter = element('banner.element');
@endphp
    <section class="banner-section">
        <div class="banner-el-img">
            <img src="{{ getFile('banner', 'banner.gif') }}" alt="image">
        </div>
        <div class="banner-el-img2">
            <img src="{{ getFile('banner', 'banner-bg.png') }}" alt="image">
        </div>
        <div class="banner-el-img3">
            <img src="{{ getFile('banner', 'bitcoin.png') }}" alt="image">
        </div>

        <div class="banner-el-left-arrow">
            <img src="{{ getFile('banner', 'left-arrow.png') }}" alt="image">
        </div>

        <div class="banner-el-right-arrow">
            <img src="{{ getFile('banner', 'right-arrow.png') }}" alt="image">
        </div>


        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-8 text-center"> 
                    <div 
                        class="banner-content paroller"
                        data-paroller-factor="0.4"
                        data-paroller-factor-sm="0.2"
                        data-paroller-factor-xs="0.1"
                    >
                        <h2 class="banner-title wow fadeInUp" data-wow-duration="0.3s" data-wow-delay="0.3s">{{ __(@$content->data->title) }}</h2>
                        <p class="banner-description mt-3 wow fadeInUp" data-wow-duration="0.3s" data-wow-delay="0.5s">{{ __(@$content->data->short_description) }}</p>
                        <div class="mt-4 wow fadeInUp" data-wow-duration="0.3s" data-wow-delay="0.7s">
                            <a href="{{ @$content->data->button_text_link }}" class="btn main-btn me-3">
                                <span>{{ __('Get Started') }}</span>
                            </a>
                            <a href="{{ $content->data->button_text_2_link}}" class="btn main-btn2 bg-white sp_text_dark">
                                <span>{{ __('Know More') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="counter-section"> 
        <div class="container"> 
            <div class="row counter-wrapper justify-content-center">
                @foreach ($counter as $count)
                    <div class="col-lg-3 col-sm-6">
                        <div class="counter-item">
                            <h3 class="counter-title">{{ $count->data->total }}</h3>
                            <p class="caption">{{ $count->data->title }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div> 
    </div>

    @push('style')
    <style>
        .tradingview-widget-container{
            height: 46px !important;
        }
        .tradingview-widget-copyright {
            display: none;
        }
    </style>
@endpush
