<?php
require_once 'merchant/config.php';
include 'imb-header1.php';
?>

    <!-- Main Content -->
    <main>
        <!-- Refund Policy Section -->
        <section class="refund-policy pt-80 pb-80">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="refund-content">
                            <h2 class="text-center mb-50">Refund Policy</h2>
                            
                            <div class="policy-section">
                                <h4>1. Refund Eligibility</h4>
                                <p>imb Pay offers refunds for failed or disputed UPI transactions under the following conditions:</p>
                                <ul>
                                    <li>Transaction failed but amount was deducted from payer's account</li>
                                    <li>Duplicate payment for the same transaction</li>
                                    <li>Payment made to wrong beneficiary</li>
                                    <li>Service not delivered as promised by merchant</li>
                                    <li>Unauthorized/fraudulent transactions</li>
                                </ul>
                            </div>
                            
                            <div class="policy-section">
                                <h4>2. Non-Refundable Transactions</h4>
                                <p>The following transactions are typically not eligible for refunds:</p>
                                <ul>
                                    <li>Successful payments where services were delivered as described</li>
                                    <li>Payments made more than 180 days ago</li>
                                    <li>Transactions where the merchant has a no-refund policy clearly stated</li>
                                    <li>Bank processing fees and other third-party charges</li>
                                </ul>
                            </div>
                            
                            <div class="policy-section">
                                <h4>3. Refund Process</h4>
                                <p>To request a refund:</p>
                                <ol>
                                    <li>Submit refund request through your imb Pay dashboard or email support@pay.garudhub.in</li>
                                    <li>Provide transaction details (UTR number, date, amount)</li>
                                    <li>Submit supporting documents if required</li>
                                    <li>Our team will verify the request within 3-5 business days</li>
                                    <li>Approved refunds will be processed within 7-10 business days</li>
                                </ol>
                            </div>
                            
                            <div class="policy-section">
                                <h4>4. Refund Methods</h4>
                                <p>Refunds will be issued through:</p>
                                <ul>
                                    <li>Original payment method (preferred)</li>
                                    <li>Bank transfer to registered account</li>
                                    <li>imb Pay wallet credit (for select cases)</li>
                                </ul>
                                <p>Processing fees may apply for certain refund methods.</p>
                            </div>
                            
                            <div class="policy-section">
                                <h4>5. Refund Timeframe</h4>
                                <p>Standard refund processing times:</p>
                                <ul>
                                    <li><strong>Failed transactions:</strong> 3-5 business days (automatic in most cases)</li>
                                    <li><strong>Disputed transactions:</strong> 7-14 business days after resolution</li>
                                    <li><strong>Merchant-initiated refunds:</strong> As per merchant's processing time</li>
                                </ul>
                                <p>Bank processing may add 1-3 additional business days.</p>
                            </div>
                            
                            <div class="policy-section">
                                <h4>6. Chargebacks and Disputes</h4>
                                <p>For disputed transactions:</p>
                                <ul>
                                    <li>Customers must first attempt to resolve with the merchant</li>
                                    <li>Formal disputes must be filed within 60 days of transaction</li>
                                    <li>We may temporarily hold funds during investigation</li>
                                    <li>Evidence from both parties will be reviewed</li>
                                    <li>Final decision will be communicated within 30 days</li>
                                </ul>
                            </div>
                            
                            <div class="policy-section">
                                <h4>7. Merchant Responsibilities</h4>
                                <p>Merchants using imb Pay must:</p>
                                <ul>
                                    <li>Maintain clear refund policies on their platforms</li>
                                    <li>Process valid refund requests within 7 days</li>
                                    <li>Maintain sufficient balance for potential refunds</li>
                                    <li>Cooperate with imb Pay during dispute resolution</li>
                                </ul>
                            </div>
                            
                            <div class="policy-section">
                                <h4>8. Late or Missing Refunds</h4>
                                <p>If your refund hasn't arrived:</p>
                                <ul>
                                    <li>Check your bank account again (processing delays may occur)</li>
                                    <li>Contact your bank (they may be holding the funds)</li>
                                    <li>Contact our support team with transaction details</li>
                                </ul>
                            </div>
                            
                            <div class="policy-section">
                                <h4>9. Policy Changes</h4>
                                <p>We may update this Refund Policy periodically. Changes will be posted on this page with updated effective date.</p>
                            </div>
                            
                            <div class="policy-section">
                                <h4>10. Contact Us</h4>
                                <p>For refund-related questions or assistance:</p>
                                <p>Email: refunds@pay.garudhub.in<br>
                                Phone: +91 XXXXXXXXXX (Refund Department)<br>
                                Hours: Mon-Sat, 10AM-6PM IST</p>
                            </div>
                            
                            <p class="policy-update">Effective Date: [Insert Date]</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>
    <!-- Main Content End -->

<?php include 'imb-footer1.php'; ?>

<style>
    /* Refund Policy Styles */
    .refund-policy {
        background-color: #f9f9ff;
    }
    .refund-content {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 5px 30px rgba(0,0,0,0.05);
    }
    .refund-content h2 {
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
    .policy-section ul, .policy-section ol {
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
        .refund-content {
            padding: 25px;
        }
        .refund-content h2 {
            font-size: 26px;
        }
    }
</style>