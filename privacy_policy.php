<?php
require_once 'merchant/config.php';
include 'imb-header1.php';
?>

    <!-- Main Content -->
    <main>
       

        <!-- Privacy Policy Section -->
        <section class="privacy-policy pt-80 pb-80">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="privacy-content">
                            <h2 class="text-center mb-50">Privacy Policy</h2>
                            
                            <div class="policy-section">
                                <h4>1. Introduction</h4>
                                <p>imb Pay ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our UPI payment gateway services.</p>
                            </div>
                            
                            <div class="policy-section">
                                <h4>2. Information We Collect</h4>
                                <p>We may collect the following types of information:</p>
                                <ul>
                                    <li><strong>Personal Information:</strong> Name, email address, phone number, business details, KYC documents, bank account information</li>
                                    <li><strong>Transaction Information:</strong> Payment details, transaction amounts, beneficiary details</li>
                                    <li><strong>Technical Information:</strong> IP address, device information, browser type, operating system</li>
                                    <li><strong>Usage Data:</strong> How you interact with our services, pages visited, features used</li>
                                </ul>
                            </div>
                            
                            <div class="policy-section">
                                <h4>3. How We Use Your Information</h4>
                                <p>We use the collected information to:</p>
                                <ul>
                                    <li>Provide and maintain our payment services</li>
                                    <li>Process transactions and prevent fraud</li>
                                    <li>Verify your identity and comply with KYC regulations</li>
                                    <li>Improve our services and develop new features</li>
                                    <li>Communicate with you about your account and transactions</li>
                                    <li>Comply with legal obligations and prevent illegal activities</li>
                                </ul>
                            </div>
                            
                            <div class="policy-section">
                                <h4>4. Data Security</h4>
                                <p>We implement industry-standard security measures including:</p>
                                <ul>
                                    <li>256-bit SSL encryption for all data transmissions</li>
                                    <li>PCI DSS compliant infrastructure</li>
                                    <li>Tokenization for sensitive payment data</li>
                                    <li>Regular security audits and penetration testing</li>
                                    <li>Role-based access controls to your information</li>
                                </ul>
                            </div>
                            
                            <div class="policy-section">
                                <h4>5. Data Retention</h4>
                                <p>We retain your personal information only as long as necessary to:</p>
                                <ul>
                                    <li>Provide you with our services</li>
                                    <li>Comply with legal obligations (typically 7 years for financial records)</li>
                                    <li>Resolve disputes and enforce our agreements</li>
                                </ul>
                            </div>
                            
                            <div class="policy-section">
                                <h4>6. Information Sharing</h4>
                                <p>We may share your information with:</p>
                                <ul>
                                    <li>Banks and financial institutions to process payments</li>
                                    <li>Regulatory authorities as required by law</li>
                                    <li>Service providers who assist in our operations (with strict confidentiality agreements)</li>
                                    <li>Law enforcement when legally required</li>
                                </ul>
                                <p>We never sell your personal information to third parties.</p>
                            </div>
                            
                            <div class="policy-section">
                                <h4>7. Your Rights</h4>
                                <p>You have the right to:</p>
                                <ul>
                                    <li>Access and receive a copy of your personal data</li>
                                    <li>Request correction of inaccurate information</li>
                                    <li>Request deletion of your data (subject to legal requirements)</li>
                                    <li>Object to or restrict certain processing activities</li>
                                    <li>Withdraw consent where applicable</li>
                                </ul>
                            </div>
                            
                            <div class="policy-section">
                                <h4>8. Cookies and Tracking</h4>
                                <p>We use cookies and similar technologies to:</p>
                                <ul>
                                    <li>Authenticate users and prevent fraud</li>
                                    <li>Remember your preferences</li>
                                    <li>Analyze service usage and improve performance</li>
                                </ul>
                                <p>You can control cookies through your browser settings.</p>
                            </div>
                            
                            <div class="policy-section">
                                <h4>9. Changes to This Policy</h4>
                                <p>We may update this Privacy Policy periodically. We will notify you of significant changes through email or prominent notices on our website.</p>
                            </div>
                            
                            <div class="policy-section">
                                <h4>10. Contact Us</h4>
                                <p>For any privacy-related questions or requests, please contact our Data Protection Officer at:</p>
                                <p>Email: privacy@pay.garudhub.in<br>
                                Phone: +91 XXXXXXXXXX<br>
                                Address: [Your Registered Office Address]</p>
                            </div>
                            
                            <p class="policy-update">Last Updated: [Insert Date]</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>
    <!-- Main Content End -->

<?php include 'imb-footer1.php'; ?>

<style>
    /* Privacy Policy Styles */
    .privacy-policy {
        background-color: #f9f9ff;
    }
    .privacy-content {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 5px 30px rgba(0,0,0,0.05);
    }
    .privacy-content h2 {
        color: #2a2a2a;
        font-size: 32px;
        margin-bottom: 30px;
    }
    .policy-section {
        margin-bottom: 30px;
    }
    .policy-section h4 {
        color: #6e8efb;
        font-size: 20px;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 1px solid #eee;
    }
    .policy-section p {
        color: #555;
        line-height: 1.8;
        margin-bottom: 15px;
    }
    .policy-section ul {
        margin-left: 20px;
        margin-bottom: 15px;
    }
    .policy-section li {
        color: #555;
        line-height: 1.8;
        margin-bottom: 8px;
    }
    .policy-update {
        text-align: right;
        font-style: italic;
        color: #888;
        margin-top: 40px;
    }
    
    @media (max-width: 767px) {
        .privacy-content {
            padding: 25px;
        }
        .privacy-content h2 {
            font-size: 26px;
        }
    }
</style>