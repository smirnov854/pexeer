<!-- header start here  -->
<header class="header-area" id="sticky">
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="{{route('home')}}"><img src="{{show_image(1,'logo')}}" alt="logo" /></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav m-auto" id="nav">
                    <li class="nav-item current">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#feature">Feature</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-to-trade">How to Trade</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonial">Testimonial</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">FAQ</a>
                    </li>
                </ul>
                <div class="navbar-right">
                    <div class="navbar-right-wrap">
                        <a href="#" class="register-button">sign in</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
<!-- header end here  -->
@foreach($sections as $item)
    <div class="{{$item->section_key}}-section" @if(!$item->section_status) style="display: none" @endif>
        @include('admin.landing.landing_page.'.$active_page_key.'.'.$item->section_key,['section_parent'=>$item,'section_data'=>$section_data[$item->section_key]])
    </div>
@endforeach
<!-- footer area start here  -->
<footer class="footer-area">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <div class="single-widget about-widget">
                        <a href="{{route('home')}}" class="brand-logo"><img src="{{show_image(1,'logo')}}" alt="logo" /></a>
                        <p class="about-text">{{$settings['footer_description'] ?? 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni nisi cumque delectus.'}}</p>
                        <ul class="social-media">
                            <li><a href="{{$settings['facebook_link'] ?? '#'}}"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="{{$settings['twitter_link'] ?? '#'}}"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="{{$settings['linkedin_link'] ?? '#'}}"><i class="fab fa-linkedin-in"></i></a></li>
                            <li><a href="{{$settings['instagram_link'] ?? '#'}}"><i class="fab fa-instagram"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <div class="single-widget menu-widget">
                        <h3 class="widget-title">Features</h3>
                        <ul>
                            @foreach($footer_features as $feature)
                                @if($feature->footer_status)
                                    <li>{{$feature->sub_title}}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <div class="single-widget menu-widget">
                        <h3 class="widget-title">Services</h3>
                        <ul>
                            @foreach($custom_pages as $page)
                                @if(in_array($page->id,$selected_custom_pages))
                                    <li><a href="{{route('seeDetails',$page->key)}}">{{$page->title}}</a></li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <div class="single-widget address-widget">
                        <h3 class="widget-title">contact</h3>
                        <p>{{$settings['contact_address'] ?? '5055 North 03th Avenue,Penscola, FL 32503, New York'}}</p>
                        <ul class="content-links">
                            <li><i class="fas fa-phone-alt"></i>{{$settings['site_mobile'] ?? '+1 965 047 658 23'}}</li>
                            <li><i class="fas fa-envelope"></i>{{$settings['site_email'] ?? 'pexeercom54@gmail.com'}}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p class="copyright-text text-center">{{$settings['copyright_text']}}</p>
        </div>
    </div>
</footer>
<!-- footer area end here  -->
