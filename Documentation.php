<?php include "merchant/config.php"; ?>
<?php include 'imb-header1.php'; ?>

    <!-- Main Content -->
    <main>
        <!-- API Documentation Section -->
        <section class="api-docs pt-80 pb-80">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="docs-content">
                            <h1 class="text-center mb-50">imb Pay API Documentation</h1>
                            
                            <div class="docs-section">
                                <h2>Introduction</h2>
                                <p>imb Pay provides simple APIs to integrate UPI payments into your application. This documentation covers the Create Order and Check Order Status APIs.</p>
                                
                                <div class="alert alert-info">
                                    <h4>Base URL</h4>
                                    <p>All API endpoints start with: <code><?= $site_url ?>/api/</code></p>
                                </div>
                                
                                <h3>Authentication</h3>
                                <p>All requests require your <code>user_token</code> for authentication. This token is provided when you register as a merchant.</p>
                            </div>
                            
                            <div class="docs-section">
                                <h2>Create Order API</h2>
                                <p>Initiate a new payment transaction and get a payment URL for your customer.</p>
                                
                                <div class="endpoint-card">
                                    <div class="endpoint-header">
                                        <span class="method post">POST</span>
                                        <code>/create-order</code>
                                    </div>
                                    
                                    <h4>Request Parameters</h4>
                                    <div class="table-responsive">
                                        <table class="params-table">
                                            <thead>
                                                <tr>
                                                    <th>Parameter</th>
                                                    <th>Type</th>
                                                    <th>Required</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>customer_mobile</td>
                                                    <td>String</td>
                                                    <td>Yes</td>
                                                    <td>Customer's 10-digit mobile number</td>
                                                </tr>
                                                <tr>
                                                    <td>user_token</td>
                                                    <td>String</td>
                                                    <td>Yes</td>
                                                    <td>Your merchant authentication token</td>
                                                </tr>
                                                <tr>
                                                    <td>amount</td>
                                                    <td>Numeric</td>
                                                    <td>Yes</td>
                                                    <td>Transaction amount (minimum ₹1)</td>
                                                </tr>
                                                <tr>
                                                    <td>order_id</td>
                                                    <td>String</td>
                                                    <td>Yes</td>
                                                    <td>Your unique order identifier (max 50 chars)</td>
                                                </tr>
                                                <tr>
                                                    <td>redirect_url</td>
                                                    <td>URL</td>
                                                    <td>Yes</td>
                                                    <td>URL to redirect after payment completion</td>
                                                </tr>
                                                <tr>
                                                    <td>remark1</td>
                                                    <td>String</td>
                                                    <td>No</td>
                                                    <td>Additional remark (max 100 chars)</td>
                                                </tr>
                                                <tr>
                                                    <td>remark2</td>
                                                    <td>String</td>
                                                    <td>No</td>
                                                    <td>Additional remark (max 100 chars)</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <h4>Important Notes</h4>
                                    <ul>
                                        <li><strong>Order Timeout:</strong> 30 minutes. Orders automatically fail after timeout</li>
                                        <li><strong>Content-Type:</strong> application/x-www-form-urlencoded</li>
                                        <li><strong>Order ID:</strong> Must be unique per transaction</li>
                                    </ul>
                                    
                                    <h4>Request Example</h4>
                                    <div class="code-block">
                                        <pre>POST /api/create-order
Content-Type: application/x-www-form-urlencoded

customer_mobile=8145344963&user_token=4f4f2d5860edb2ee76ba899d3b63bd02&amount=1&order_id=8787772321800&redirect_url=https://yourwebsite.com/callback&remark1=testremark&remark2=testremark2</pre>
                                    </div>
                                    
                                    <h4>Success Response (200 OK)</h4>
                                    <div class="code-block">
                                        <pre>{
    "status": true,
    "message": "Order Created Successfully",
    "result": {
        "orderId": "1234561705047510",
        "payment_url": "https://yourwebsite.com/payment/pay.php?data=MTIzNDU2MTcwNTA0NzUxMkyNTIy"
    }
}</pre>
                                    </div>
                                    
                                    <h4>Error Responses</h4>
                                    <div class="code-block">
                                        <pre>{
    "status": false,
    "message": "Order_id Already Exist"
}

{
    "status": false,
    "message": "Invalid user_token"
}

{
    "status": false,
    "message": "Amount must be at least 1"
}</pre>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="docs-section">
                                <h2>Check Order Status API</h2>
                                <p>Check the status of an existing order/transaction.</p>
                                
                                <div class="endpoint-card">
                                    <div class="endpoint-header">
                                        <span class="method post">POST</span>
                                        <code>/check-order-status</code>
                                    </div>
                                    
                                    <h4>Request Parameters</h4>
                                    <div class="table-responsive">
                                        <table class="params-table">
                                            <thead>
                                                <tr>
                                                    <th>Parameter</th>
                                                    <th>Type</th>
                                                    <th>Required</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>user_token</td>
                                                    <td>String</td>
                                                    <td>Yes</td>
                                                    <td>Your merchant authentication token</td>
                                                </tr>
                                                <tr>
                                                    <td>order_id</td>
                                                    <td>String</td>
                                                    <td>Yes</td>
                                                    <td>The order ID you want to check</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <h4>Request Example</h4>
                                    <div class="code-block">
                                        <pre>POST /api/check-order-status
Content-Type: application/x-www-form-urlencoded

user_token=2048f66bef68633fa3262d7a398ab577&order_id=8052313697</pre>
                                    </div>
                                    
                                    <h4>Success Response (200 OK)</h4>
                                    <div class="code-block">
                                        <pre>{
    "status": "COMPLETED",
    "message": "Transaction Successfully",
    "result": {
        "txnStatus": "COMPLETED",
        "resultInfo": "Transaction Success",
        "orderId": "784525sdD",
        "status": "SUCCESS",
        "amount": "1",
        "date": "2024-01-12 13:22:08",
        "utr": "454525454245"
    }
}</pre>
                                    </div>
                                    
                                    <h4>Possible Status Values</h4>
                                    <ul>
                                        <li><strong>COMPLETED:</strong> Payment successful</li>
                                        <li><strong>PENDING:</strong> Payment initiated but not completed</li>
                                        <li><strong>FAILED:</strong> Payment failed or expired</li>
                                        <li><strong>ERROR:</strong> Technical error occurred</li>
                                    </ul>
                                    
                                    <h4>Error Responses</h4>
                                    <div class="code-block">
                                        <pre>{
    "status": "ERROR",
    "message": "Order not found"
}

{
    "status": "ERROR",
    "message": "Invalid user_token"
}</pre>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="docs-section">
                                <h2>Best Practices</h2>
                                <div class="best-practices">
                                    <div class="practice-card">
                                        <h4>Order ID Generation</h4>
                                        <p>Generate unique order IDs on your server. Recommended format: <code>[merchant_prefix][timestamp][random_digits]</code></p>
                                    </div>
                                    <div class="practice-card">
                                        <h4>Error Handling</h4>
                                        <p>Always check the <code>status</code> field in responses and handle all possible error cases.</p>
                                    </div>
                                    <div class="practice-card">
                                        <h4>Webhook Integration</h4>
                                        <p>For real-time notifications, implement our webhook API to receive payment status updates instantly.</p>
                                    </div>
                                    <div class="practice-card">
                                        <h4>Testing</h4>
                                        <p>Use our sandbox environment for testing with test amounts (₹1-₹10 recommended).</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="docs-section">
                                <h2>Support</h2>
                                <p>For technical support or questions about API integration:</p>
                                <ul>
                                    <li>Email: <a href="mailto:support@pay.garudhub.in">support@pay.garudhub.in</a></li>
                                    <li>Phone: +91 XXXXXXXXXX (10AM-6PM IST)</li>
                                    <li>WhatsApp: <a href="https://wa.me/91XXXXXXXXXX">+91 XXXXXXXXXX</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>
    <!-- Main Content End -->

<?php include 'imb-footer1.php'; ?>

<style>
    /* API Documentation Styles */
    .api-docs {
        background-color: #f9f9ff;
    }
    .docs-content {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 5px 30px rgba(0,0,0,0.05);
    }
    .docs-content h1, 
    .docs-content h2, 
    .docs-content h3, 
    .docs-content h4 {
        color: #2a2a2a;
        margin-top: 30px;
        margin-bottom: 20px;
    }
    .docs-content h1 {
        font-size: 32px;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
    }
    .docs-content h2 {
        font-size: 26px;
        color: #6e8efb;
    }
    .docs-content h3 {
        font-size: 22px;
    }
    .docs-content h4 {
        font-size: 18px;
    }
    
    .endpoint-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 40px;
        border-left: 4px solid #6e8efb;
    }
    .endpoint-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    .method {
        padding: 5px 12px;
        border-radius: 4px;
        font-weight: bold;
        margin-right: 15px;
        font-size: 14px;
    }
    .method.post {
        background: #6e8efb;
        color: white;
    }
    .endpoint-header code {
        font-size: 18px;
        color: #333;
    }
    
    .params-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .params-table th, 
    .params-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    .params-table th {
        background: #f1f3f5;
        font-weight: 600;
    }
    
    .code-block {
        background: #2d2d2d;
        color: #f8f8f2;
        padding: 15px;
        border-radius: 5px;
        overflow-x: auto;
        margin-bottom: 20px;
    }
    .code-block pre {
        margin: 0;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        line-height: 1.5;
    }
    
    .alert {
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .alert-info {
        background: #e7f5ff;
        border-left: 4px solid #4dabf7;
    }
    
    .best-practices {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .practice-card {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #a777e3;
    }
    .practice-card h4 {
        margin-top: 0;
        color: #5f3dc4;
    }
    
    @media (max-width: 767px) {
        .docs-content {
            padding: 20px;
        }
        .best-practices {
            grid-template-columns: 1fr;
        }
    }
</style>