
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= $website_settings['title'] ?></title>
    <meta name="description" content="Xolio - Creative Agency Template">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="<?= $site_url ?>/<?= $website_settings['favicon'] ?>">

    <!-- CSS here -->
    <link rel="stylesheet" href="newassets/css/bootstrap.min.css">
    <link rel="stylesheet" href="newassets/css/animate.min.css">
    <!-- <link rel="stylesheet" href="newassets/css/magnific-popup.css"> -->
    <!-- <link rel="stylesheet" href="newassets/css/fontawesome-all.min.css"> -->
    <link rel="stylesheet" href="newassets/css/swiper-bundle.min.css">
    <!-- <link rel="stylesheet" href="newassets/css/odometer.css"> -->
    <link rel="stylesheet" href="newassets/css/slick.css">
    <link rel="stylesheet" href="newassets/css/default.css">
    <link rel="stylesheet" href="newassets/css/style.css">
    <link rel="stylesheet" href="newassets/css/responsive.css">
</head>

<body>




    <!-- Custom-cursor -->
    <div class="mouseCursor cursor-outer"></div>
    <div class="mouseCursor cursor-inner"><span>Drag</span></div>
    <!-- Custom-cursor-end -->

    <!-- Scroll-top -->
    <button class="scroll-top scroll-to-target" data-target="html">
        <!-- <i class="fas fa-angle-up"></i> -->
        <img src="https://cdn3.iconfinder.com/data/icons/faticons/32/arrow-up-01-512.png" alt="Up arrow" width="16" height="16" />
    </button>
    <!-- Scroll-top-end-->

    <!-- header-area -->
    <header>
        <div id="sticky-header" class="menu-area transparent-header">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="mobile-nav-toggler">
                            <!-- <i class="fas fa-bars"></i> -->
                            <svg width="32" height="32" viewBox="0 0 448 512" fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg" class="icon-primary">
                                <path
                                    d="M0 96C0 78.3 14.3 64 32 64l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32L32 448c-17.7 0-32-14.3-32-32s14.3-32 32-32l384 0c17.7 0 32 14.3 32 32z" />
                            </svg>

                        </div>
                        <div class="menu-wrap">
                            <nav class="menu-nav">
                                <div class="logo">
                                    <a href="index"><img loading="lazy" src="<?= $site_url ?>/<?= $website_settings['logo'] ?>" alt="Logo"></a>
                                </div>
                                <div class="navbar-wrap main-menu d-none d-lg-flex">
                                    <ul class="navigation">
                                        <li><a href="index">Home</a></li>
                                        <li><a href="service" class="active">Services</a></li>
                                        <li><a href="demo">Demo</a></li>
                                        <li class="menu-item-has-children dropdown-btn"><a href="#">Important link
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"
                                                    fill="currentColor" viewBox="0 0 16 16" style="margin-left: 6px;">
                                                    <path d="M1.5 5.5l6 6 6-6" stroke="currentColor" stroke-width="2"
                                                        fill="none" />
                                                </svg>
                                            </a>
                                            <ul class="sub-menu">
                                                <li><a href="Documentation">Documentation</a></li>
                                                <li><a href="pay">API Test</a></li>
                                                <li><a href="privacy_policy">Privacy Policy</a></li>
                                                <li><a href="refund_policy">Refund Policy</a></li>
                                                <li><a href="Terms_and_Conditions">Terms and Conditions</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="merchant/register">Sign Up</a></li>
                                        </li>
                                    </ul>
                                </div>
                                <div class="header-action">
                                    <ul class="list-wrap">
                                        <li class="header-btn"><a href="merchant/index" class="btn">Login<span></span></a>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                        </div>

                        <!-- Mobile Menu  -->
                        <div class="mobile-menu">
                            <nav class="menu-box">
                                <div class="close-btn">
                                    <!-- <i class="fas fa-times"></i> -->
                                    <svg width="32" height="32" viewBox="0 0 448 512" fill="currentColor"
                                        xmlns="http://www.w3.org/2000/svg" class="icon-primary">
                                        <path
                                            d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                                    </svg>
                                </div>
                                <div class="nav-logo">
                                    <a href="index"><img loading="lazy" src="<?= $site_url ?>/<?= $website_settings['logo'] ?>" alt="Logo"></a>
                                </div>
                                <div class="menu-outer">
                                    <!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header-->
                                </div>
                                <!-- <div class="social-links">
                                        <ul class="clearfix list-wrap">
                                            <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                            <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                            <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                                            <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                            <li><a href="#"><i class="fab fa-youtube"></i></a></li>
                                        </ul>
                                    </div> -->


                                <div class="social-links">
                                    <ul class="list-wrap">
                                        <li class="header-btn"><a href="merchant/index" class="btn">Login<span></span></a>
                                        </li>
                                        <li class="header-btn"><a href="merchant/register" class="btn"
                                                style="background-color:blueviolet;">Register<span></span></a>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                        </div>
                        <div class="menu-backdrop"></div>
                        <!-- End Mobile Menu -->

                    </div>
                </div>
            </div>

        </div>
    </header>
    <!-- header-area-end -->