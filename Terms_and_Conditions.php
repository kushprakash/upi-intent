<?php
require_once 'merchant/config.php';
include 'imb-header1.php';
?>

    <!-- Main Content -->
    <main>
        <!-- Terms and Conditions Section -->
        <section class="terms-conditions pt-80 pb-80">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="terms-content">
                            <h2 class="text-center mb-50">Terms and Conditions</h2>
                            
                            <div class="terms-section">
                                <h4>1. Acceptance of Terms</h4>
                                <p>By accessing or using imb Pay's UPI payment services ("Services"), you agree to be bound by these Terms and Conditions ("Terms"). If you do not agree to all terms, you may not use our Services.</p>
                            </div>
                            
                            <div class="terms-section">
                                <h4>2. Service Description</h4>
                                <p>imb Pay provides:</p>
                                <ul>
                                    <li>UPI payment processing services</li>
                                    <li>Merchant account facilities</li>
                                    <li>Payment gateway integration</li>
                                    <li>Transaction reporting and analytics</li>
                                </ul>
                                <p>All services are subject to RBI guidelines and NPCI regulations.</p>
                            </div>
                            
                            <div class="terms-section">
                                <h4>3. Account Registration</h4>
                                <p>To use our Services, you must:</p>
                                <ul>
                                    <li>Provide accurate and complete registration information</li>
                                    <li>Be at least 18 years old</li>
                                    <li>Have legal authority to bind your business (for merchants)</li>
                                    <li>Complete KYC verification as required</li>
                                </ul>
                                <p>You are responsible for maintaining the confidentiality of your account credentials.</p>
                            </div>
                            
                            <div class="terms-section">
                                <h4>4. Prohibited Activities</h4>
                                <p>You agree not to:</p>
                                <ul>
                                    <li>Use the Services for illegal activities or prohibited businesses</li>
                                    <li>Attempt to circumvent security measures</li>
                                    <li>Initiate fraudulent or unauthorized transactions</li>
                                    <li>Use bots, scrapers, or other automated tools</li>
                                    <li>Violate any applicable laws or regulations</li>
                                </ul>
                            </div>
                            
                            <div class="terms-section">
                                <h4>5. Transaction Processing</h4>
                                <p>All transactions are subject to:</p>
                                <ul>
                                    <li>UPI transaction limits as set by RBI/NPCI</li>
                                    <li>Fraud screening and risk assessment</li>
                                    <li>Bank processing times and availability</li>
                                    <li>Merchant-specific terms where applicable</li>
                                </ul>
                                <p>imb Pay is not responsible for errors caused by incorrect payment details provided by users.</p>
                            </div>
                            
                            <div class="terms-section">
                                <h4>6. Fees and Charges</h4>
                                <p>Our fee structure includes:</p>
                                <ul>
                                    <li><strong>UPI Transactions:</strong> 0% fees (unless otherwise specified)</li>
                                    <li><strong>Chargebacks:</strong> ₹200 per instance</li>
                                    <li><strong>Premium Features:</strong> As per current pricing</li>
                                </ul>
                                <p>We reserve the right to modify fees with 30 days notice.</p>
                            </div>
                            
                            <div class="terms-section">
                                <h4>7. Settlement to Merchants</h4>
                                <p>Settlement terms:</p>
                                <ul>
                                    <li>Standard settlement: T+1 business days</li>
                                    <li>Minimum settlement amount: ₹500</li>
                                    <li>Settlement only to verified bank accounts</li>
                                    <li>Holidays may delay processing</li>
                                </ul>
                            </div>
                            
                            <div class="terms-section">
                                <h4>8. Dispute Resolution</h4>
                                <p>For transaction disputes:</p>
                                <ol>
                                    <li>Contact the merchant directly first</li>
                                    <li>Submit dispute through imb Pay dashboard within 60 days</li>
                                    <li>Provide all relevant transaction details</li>
                                    <li>imb Pay will mediate and make final determination</li>
                                </ol>
                            </div>
                            
                            <div class="terms-section">
                                <h4>9. Liability Limitations</h4>
                                <p>imb Pay's liability is limited to:</p>
                                <ul>
                                    <li>Direct damages up to the transaction amount</li>
                                    <li>Cases of proven negligence on our part</li>
                                </ul>
                                <p>We are not liable for:</p>
                                <ul>
                                    <li>Third party actions or failures</li>
                                    <li>Force majeure events</li>
                                    <li>Indirect, consequential, or punitive damages</li>
                                </ul>
                            </div>
                            
                            <div class="terms-section">
                                <h4>10. Termination</h4>
                                <p>We may terminate or suspend your account:</p>
                                <ul>
                                    <li>For violation of these Terms</li>
                                    <li>Upon regulatory requirement</li>
                                    <li>For prolonged inactivity (12+ months)</li>
                                </ul>
                                <p>You may terminate your account by written notice with 30 days notice.</p>
                            </div>
                            
                            <div class="terms-section">
                                <h4>11. Governing Law</h4>
                                <p>These Terms shall be governed by Indian law. Any disputes shall be subject to the exclusive jurisdiction of courts in [Your City], India.</p>
                            </div>
                            
                            <div class="terms-section">
                                <h4>12. Amendments</h4>
                                <p>We may modify these Terms periodically. Continued use after changes constitutes acceptance. We will notify users of material changes.</p>
                            </div>
                            
                            <div class="terms-section">
                                <h4>13. Contact Information</h4>
                                <p>For questions about these Terms:</p>
                                <p>Email: legal@pay.garudhub.in<br>
                                Address: [Your Registered Office Address]<br>
                                Phone: +91 XXXXXXXXXX</p>
                            </div>
                            
                            <p class="terms-update">Effective Date: [Insert Date]</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>
    <!-- Main Content End -->

<?php include 'imb-footer1.php'; ?>

<style>
    /* Terms and Conditions Styles */
    .terms-conditions {
        background-color: #f9f9ff;
    }
    .terms-content {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 5px 30px rgba(0,0,0,0.05);
    }
    .terms-content h2 {
        color: #2a2a2a;
        font-size: 32px;
        margin-bottom: 30px;
    }
    .terms-section {
        margin-bottom: 30px;
    }
    .terms-section h4 {
        color: #6e8efb;
        font-size: 20px;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 1px solid #eee;
    }
    .terms-section p {
        color: #555;
        line-height: 1.8;
        margin-bottom: 15px;
    }
    .terms-section ul, .terms-section ol {
        margin-left: 20px;
        margin-bottom: 15px;
    }
    .terms-section li {
        color: #555;
        line-height: 1.8;
        margin-bottom: 8px;
    }
    .terms-update {
        text-align: right;
        font-style: italic;
        color: #888;
        margin-top: 40px;
    }
    
    @media (max-width: 767px) {
        .terms-content {
            padding: 25px;
        }
        .terms-content h2 {
            font-size: 26px;
        }
    }
</style>