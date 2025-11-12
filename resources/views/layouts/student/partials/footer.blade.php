<footer class="footer inner-footer">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget">
                        {{-- <a href="/"><img src="{{ asset('assets/img/logo.png') }}" alt="logo"></a> --}}
                        <div class="footer-about">
                            <p class="mt-2">
                                <strong>{{ config('app.name') }}</strong> is your platform for 1-to-1 learning. We connect you with expert tutors, so you can book lessons tailored to your schedule and goals.
                            </p>
                        </div>
                        {{-- <div class="social-icon">
                            <h6 class="mb-3 mt-3">Follow Us</h6>
                            <ul>
                                <li><a href="javascript:void(0)"><i class="fa-brands fa-facebook-f"></i></a></li>
                                <li><a href="javascript:void(0)"><i class="fa-brands fa-x-twitter"></i></a></li>
                                <li><a href="javascript:void(0)"><i class="fa-brands fa-instagram"></i></a></li>
                                <li><a href="javascript:void(0)"><i class="fa-brands fa-linkedin-in"></i></a></li>
                                <li><a href="javascript:void(0)"><i class="fa-brands fa-youtube"></i></a></li>
                            </ul>
                        </div> --}}
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="footer-widget footer-menu">
                                <h6 class="footer-title">For Students</h6>
                                <ul>
                                    @auth
                                    <li><a href="{{ route('student.tutors.search') }}">Find Teachers</a></li>
                                    @endauth
                                    <li><a href="{{ route('cms.how.works') }}">How It Works</a></li>
                                    <li><a href="#">Pricing</a></li> {{-- Added --}}
                                    @auth
                                    <li><a href="{{ route('student.dashboard') }}">Student Dashboard</a></li>
                                    @endauth
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6">
                            <div class="footer-widget footer-menu">
                                <h6 class="footer-title">Company & Legal</h6>
                                <ul>
                                    <li><a href="{{ route('cms.about') }}">About Us</a></li> {{-- Added --}}
                                    <li><a href="{{ route('cms.contact') }}">Contact Us</a></li> {{-- Added --}}
                                    <li><a href="{{ route('cms.terms') }}">Terms and Conditions</a></li> {{-- Added --}}
                                    <li><a href="{{ route('cms.privacy') }}">Privacy Policy</a></li> {{-- Added --}}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-lg-4 col-md-6">
                    <div class="footer-widget">
                        <h6 class="footer-title">Stay Connected</h6>
                        <p class="mb-2">Subscribe to get learning tips, updates & special offers.</p>
                        <div class="subscribe-input">
                            <form>
                                <input type="email" class="form-control" placeholder="Enter your email">
                                <button type="submit" class="btn btn-md btn-primary-gradient d-inline-flex align-items-center">
                                    <i class="fa-solid fa-paper-plane me-1"></i>Subscribe
                                </button>
                            </form>
                        </div>
                    </div>
                </div> --}}
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
                <div class="copyright-text">
                    <p class="mb-0">© {{ date('Y') }} <strong>{{config('app.name')}}</strong>. All rights reserved.</p>
                </div>
                <div class="copyright-menu">
                    <ul class="policy-menu">
                        {{-- I've kept these here as it's common to have them in both places --}}
                        <li><a href="{{ route('cms.terms') }}">Terms of Service</a></li>
                        <li><a href="{{ route('cms.privacy') }}">Privacy Policy</a></li>
                    </ul>
                </div>
                <ul class="payment-method">
                    <li><a href="javascript:void(0)"><img src="{{ asset('assets/img/icons/card-01.svg') }}" alt="Visa"></a></li>
                    <li><a href="javascript:void(0)"><img src="{{ asset('assets/img/icons/card-02.svg') }}" alt="MasterCard"></a></li>
                    <li><a href="javascript:void(0)"><img src="{{ asset('assets/img/icons/card-04.svg') }}" alt="PayPal"></a></li>
                    <li><a href="javascript:void(0)"><img src="{{ asset('assets/img/icons/card-05.svg') }}" alt="Stripe"></a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>