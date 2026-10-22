<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= $website_settings['title'] ?></title>
    <meta name="description" content="Explore <?= $website_settings['title'] ?>'s complete UPI payment services including instant payments, merchant solutions, secure transactions, and easy integration">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="<?= $site_url ?>/<?= $website_settings['favicon'] ?>">

    <!-- CSS here -->
    <link rel="stylesheet" href="newassets/css/bootstrap.min.css">
    <link rel="stylesheet" href="newassets/css/animate.min.css">
    <link rel="stylesheet" href="newassets/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="newassets/css/slick.css">
    <link rel="stylesheet" href="newassets/css/default.css">
    <link rel="stylesheet" href="newassets/css/style.css">
    <link rel="stylesheet" href="newassets/css/responsive.css">
    
    <style>
        /* Service Hero Section */
        .service-hero {
            background: linear-gradient(135deg, #6e8efb 0%, #a777e3 100%);
            padding: 100px 0 80px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .service-hero:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('newassets/img/service_dots.png') center/cover no-repeat;
            opacity: 0.1;
        }
        .service-hero h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .service-hero p {
            font-size: 18px;
            opacity: 0.9;
            max-width: 600px;
        }
        
        /* Service Features */
        .service-card {
            background: white;
            border-radius: 12px;
            padding: 40px 30px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid #f0f0f0;
            position: relative;
            overflow: hidden;
        }
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }
        .service-card:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #6e8efb 0%, #a777e3 100%);
            transition: all 0.3s ease;
        }
        .service-card:hover:after {
            height: 6px;
        }
        .service-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #6e8efb 0%, #a777e3 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
        }
        .service-icon img {
            width: 40px;
            height: 40px;
        }
        .service-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #2a2a2a;
        }
        .service-card p {
            color: #666;
            margin-bottom: 20px;
        }
        .service-features li {
            margin-bottom: 8px;
            position: relative;
            padding-left: 25px;
            color: #555;
        }
        .service-features li:before {
            content: '';
            position: absolute;
            left: 0;
            top: 8px;
            width: 12px;
            height: 12px;
            background: #6e8efb;
            border-radius: 50%;
        }
        
        /* How It Works Section */
        .works-section {
            background: #f9f9ff;
            padding: 100px 0;
        }
        .work-step {
            position: relative;
            padding-left: 90px;
            margin-bottom: 40px;
        }
        .step-number {
            position: absolute;
            left: 0;
            top: 0;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #6e8efb 0%, #a777e3 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
        }
        .work-step h4 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #2a2a2a;
        }
        .work-step p {
            color: #666;
        }
        
        /* Benefits Section */
        .benefit-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            height: 87%;
        }
        .benefit-card h4 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #2a2a2a;
            display: flex;
            align-items: center;
        }
        .benefit-card h4 img {
            margin-right: 15px;
            width: 30px;
        }
        .benefit-card p {
            color: #666;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 767px) {
            .service-hero {
                padding: 80px 0 60px;
                text-align: center;
            }
            .service-hero h1 {
                font-size: 36px;
            }
            .work-step {
                padding-left: 70px;
            }
            .step-number {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
        }
    </style>
</head>

<body>

    <!-- Header Area -->
    <header>
        <div id="sticky-header" class="menu-area">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="mobile-nav-toggler">
                            <svg width="32" height="32" viewBox="0 0 448 512" fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg" class="icon-primary">
                                <path
                                    d="M0 96C0 78.3 14.3 64 32 64l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32L32 448c-17.7 0-32-14.3-32-32s14.3-32 32-32l384 0c17.7 0 32 14.3 32 32z" />
                            </svg>
                        </div>
                        <div class="menu-wrap">
                            <nav class="menu-nav">
                                <div class="logo">
                                    <a href="index"><img loading="lazy" src="<?= $site_url ?>/<?= $website_settings['logo'] ?>" alt="<?= $website_settings['title'] ?>"></a>
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
                                                <li><a href="privacy_policy">Privacy Policy</a></li>
                                                <li><a href="refund_policy">Refund Policy</a></li>
                                                <li><a href="Terms_and_Conditions">Terms and Conditions</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="merchant/register">Sign Up</a></li>
                                    </ul>
                                </div>
                                <div class="header-action">
                                    <ul class="list-wrap">
                                        <li class="header-btn"><a href="merchant/index" class="btn">Login<span></span></a></li>
                                    </ul>
                                </div>
                            </nav>
                        </div>

                        <!-- Mobile Menu -->
                        <div class="mobile-menu">
                            <nav class="menu-box">
                                <div class="close-btn">
                                    <svg width="32" height="32" viewBox="0 0 448 512" fill="currentColor"
                                        xmlns="http://www.w3.org/2000/svg" class="icon-primary">
                                        <path
                                            d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                                    </svg>
                                </div>
                                <div class="nav-logo">
                                    <a href="index"><img loading="lazy" src="<?= $site_url ?>/<?= $website_settings['logo'] ?>" alt="<?= $website_settings['title'] ?>"></a>
                                </div>
                                <div class="menu-outer">
                                    <!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header-->
                                </div>
                                <div class="social-links">
                                    <ul class="list-wrap">
                                        <li class="header-btn"><a href="merchant/index" class="btn">Login<span></span></a></li>
                                        <li class="header-btn"><a href="merchant/register" class="btn" style="background-color:blueviolet;">Register<span></span></a></li>
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
    <!-- Header Area End -->