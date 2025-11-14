<footer class="footer inner-footer">

    <div class="footer-top">
        <div class="container">
            <div class="row">

                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget">
                        <div class="footer-about">
                            <p class="mt-2">
                                <strong>{{ config('app.name') }}</strong> is your platform to build your teaching business. 
                                We connect you with students and provide the tools to manage your schedule, lessons, and earnings.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 col-md-6">
                    <div class="row">

                        <div class="col-lg-6 col-md-6">
                            <div class="footer-widget footer-menu">
                                <h6 class="footer-title">For Teachers</h6>
                                <ul>
                                    @auth
                                        <li><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                                    @endauth
                                    @guest
                                        <li><a href="{{ route('auth.register') }}">Become a Teacher</a></li>
                                    @endguest
                                    <li><a href="{{ route('cms.how.works') }}">How it Works</a></li>
                                    <li><a href="{{ route('cms.contact') }}">Support Center</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6">
                            <div class="footer-widget footer-menu">
                                <h6 class="footer-title">Company & Legal</h6>
                                <ul>
                                    <li><a href="{{ route('cms.about') }}">About Us</a></li>
                                    <li><a href="{{ route('cms.contact') }}">Contact Us</a></li>
                                    <li><a href="{{ route('cms.terms') }}">Terms & Conditions</a></li>
                                    <li><a href="{{ route('cms.privacy') }}">Privacy Policy</a></li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="footer-bg">
            <img src="{{ asset('assets/img/bg/footer-bg-01.png') }}" alt="bg" class="footer-bg-01">
            <img src="{{ asset('assets/img/bg/footer-bg-02.png') }}" alt="bg" class="footer-bg-02">
            <img src="{{ asset('assets/img/bg/footer-bg-03.png') }}" alt="bg" class="footer-bg-03">
            <img src="{{ asset('assets/img/bg/footer-bg-04.png') }}" alt="bg" class="footer-bg-04">
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="copyright">

                <p class="mb-0">
                    © {{ date('Y') }} <strong>{{config('app.name')}}</strong>. All rights reserved.
                </p>

                <ul class="policy-menu">
                    <li><a href="{{ route('cms.terms') }}">Terms of Service</a></li>
                    <li><a href="{{ route('cms.privacy') }}">Privacy Policy</a></li>
                </ul>

                <ul class="payment-method">
                    <li><img src="{{ asset('assets/img/icons/card-01.svg') }}" alt="Visa"></li>
                    <li><img src="{{ asset('assets/img/icons/card-02.svg') }}" alt="MasterCard"></li>
                    <li><img src="{{ asset('assets/img/icons/card-04.svg') }}" alt="PayPal"></li>
                    <li><img src="{{ asset('assets/img/icons/card-05.svg') }}" alt="Stripe"></li>
                </ul>

            </div>
        </div>
    </div>

</footer>