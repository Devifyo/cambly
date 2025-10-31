<footer class="footer inner-footer">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <!-- Company Info -->
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-3 col-md-3">
                            <div class="footer-widget footer-menu">
                                <h6 class="footer-title">Company</h6>
                                <ul>
                                    <li><a href="#">About Us</a></li>
                                    <li><a href="#">How It Works</a></li>
                                    <li><a href="#">Careers</a></li>
                                    <li><a href="#">Blog</a></li>
                                    <li><a href="#">Contact</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Students -->
                        <div class="col-lg-3 col-md-3">
                            <div class="footer-widget footer-menu">
                                <h6 class="footer-title">For Students</h6>
                                <ul>
                                    <li><a href="#">Find Tutors</a></li>
                                    <li><a href="#">Book a Lesson</a></li>
                                    <li><a href="#">Student Dashboard</a></li>
                                    <li><a href="#">Credits & Plans</a></li>
                                    <li><a href="#">Support</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Tutors -->
                        <div class="col-lg-3 col-md-3">
                            <div class="footer-widget footer-menu">
                                <h6 class="footer-title">For Tutors</h6>
                                <ul>
                                    <li><a href="#">Become a Tutor</a></li>
                                    <li><a href="#">Tutor Dashboard</a></li>
                                    <li><a href="#">Earning Guide</a></li>
                                    <li><a href="#">Tutor Community</a></li>
                                    <li><a href="#">FAQs</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Resources -->
                        <div class="col-lg-3 col-md-3">
                            <div class="footer-widget footer-menu">
                                <h6 class="footer-title">Resources</h6>
                                <ul>
                                    <li><a href="#">Pricing</a></li>
                                    <li><a href="#">Reviews</a></li>
                                    <li><a href="#">Learning Tips</a></li>
                                    <li><a href="#">Help Center</a></li>
                                    <li><a href="#">Community</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Newsletter -->
                <div class="col-lg-4 col-md-7">
                    <div class="footer-widget">
                        <h6 class="footer-title">Stay Connected</h6>
                        <p class="mb-2">Subscribe to get learning tips, tutor updates & special offers.</p>
                        <div class="subscribe-input">
                            <form>
                                <input type="email" class="form-control" placeholder="Enter your email">
                                <button type="submit" class="btn btn-md btn-primary-gradient d-inline-flex align-items-center">
                                    <i class="fa-solid fa-paper-plane me-1"></i>Subscribe
                                </button>
                            </form>
                        </div>
                        <div class="social-icon">
                            <h6 class="mb-3 mt-3">Follow Us</h6>
                            <ul>
                                <li><a href="#"><i class="fa-brands fa-facebook-f"></i></a></li>
                                <li><a href="#"><i class="fa-brands fa-x-twitter"></i></a></li>
                                <li><a href="#"><i class="fa-brands fa-instagram"></i></a></li>
                                <li><a href="#"><i class="fa-brands fa-linkedin-in"></i></a></li>
                                <li><a href="#"><i class="fa-brands fa-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Background Shapes -->
        <div class="footer-bg">
            <img src="{{ asset('assets/img/bg/footer-bg-01.png') }}" alt="bg" class="footer-bg-01">
            <img src="{{ asset('assets/img/bg/footer-bg-02.png') }}" alt="bg" class="footer-bg-02">
            <img src="{{ asset('assets/img/bg/footer-bg-03.png') }}" alt="bg" class="footer-bg-03">
            <img src="{{ asset('assets/img/bg/footer-bg-04.png') }}" alt="bg" class="footer-bg-04">
        </div>
    </div>

    <!-- Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <div class="copyright">
                <div class="copyright-text">
                    <p class="mb-0">© 2025 <strong>{{config('app.name')}}</strong>. Learn. Connect. Grow.</p>
                </div>
                <div class="copyright-menu">
                    <ul class="policy-menu">
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>
                <ul class="payment-method">
                    <li><a href="#"><img src="{{ asset('assets/img/icons/card-01.svg') }}" alt="Visa"></a></li>
                    <li><a href="#"><img src="{{ asset('assets/img/icons/card-02.svg') }}" alt="MasterCard"></a></li>
                    <li><a href="#"><img src="{{ asset('assets/img/icons/card-04.svg') }}" alt="PayPal"></a></li>
                    <li><a href="#"><img src="{{ asset('assets/img/icons/card-05.svg') }}" alt="Stripe"></a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
