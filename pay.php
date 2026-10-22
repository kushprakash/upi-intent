

<?php
include "merchant/config.php";
error_reporting(0);
$paymentUrl = "";

if (isset($_POST['pay_now'])) {

    $url = $site_url.'/api/create-order';
    $token = $_POST['api_token'];
    $amount = $_POST['amount'];
    $orderid = rand(1000000000, 9999999999);

    $data = array(
        'customer_mobile' => '9876543210',
        'user_token' => $token,
        'amount' => $amount,
        'order_id' => $orderid,
        'redirect_url' => $site_url.'/success.php',
        'remark1' => 'imb1',
        'remark2' => 'imb2',
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($response, true);

    if ($json["status"] == "success") {
        $paymentUrl = $json['result']['payment_url'];
    } else {
        $errorMsg = $response;
    }
}
?>

<style>
.page-wrap {
    width: 100%;
    max-width: 600px;
    margin: 60px auto;
    background: #ffffff;
    padding: 40px;
    border-radius: 25px;
    box-shadow: 0px 10px 35px rgba(0,0,0,0.15);
    position: relative;
    overflow: hidden;
}

.page-wrap::before {
    content: "";
    position: absolute;
    width: 300px;
    height: 300px;
    background: #0282ad50;
    filter: blur(120px);
    border-radius: 50%;
    top: -100px;
    right: -100px;
    z-index: -1;
}

.title {
    text-align: center;
    font-size: 32px;
    font-weight: 800;
    background-image: linear-gradient(115deg, #0282ad 35%, #FFA37B 100%);
    color: transparent;
    background-clip: text;
    margin-bottom: 20px;
}

.input-group {
    margin-bottom: 22px;
}

input {
    width: 100%;
    padding: 16px;
    font-size: 17px;
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    outline: none;
    transition: 0.3s;
    background: #fafbff;
}

input:focus {
    border-color: #0282ad;
    box-shadow: 0 0 8px #0282ad70;
}

.btn-pay {
    width: 100%;
    padding: 16px;
    font-size: 19px;
    font-weight: 700;
    background-color: forestgreen;
    color: white;
    border-radius: 14px;
    border: none;
    cursor: pointer;
    transition: 0.3s;
}

.btn-pay:hover {
    background-color: #0f8d32;
}

.error-box {
    margin-top: 20px;
    padding: 12px;
    background: #ffe5e5;
    color: #c40000;
    border-radius: 10px;
    text-align: center;
    font-size: 16px;
}
</style>

<main>
    <section class="banner-area pt-90 pb-70">
        <div class="container">

            <div class="page-wrap">
                <h2 class="title">Instant Payment</h2>

                <?php if (!empty($errorMsg)) { ?>
                    <div class="error-box">
                        <?= htmlspecialchars($errorMsg); ?>
                    </div>
                <?php } ?>

                <form method="POST">

                    <div class="input-group">
                        <input type="text" name="api_token" placeholder="Enter API Token" required>
                    </div>

                    <div class="input-group">
                        <input type="number" name="amount" placeholder="Enter Amount" required>
                    </div>

                    <button class="btn-pay" type="submit" name="pay_now">Pay Now</button>
                </form>

                <?php if ($paymentUrl) { ?>
                    <script>
                        // NEW TAB OPEN
                        window.open("<?= $paymentUrl ?>", "_blank");
                    </script>
                <?php } ?>

            </div>

        </div>
    </section>
</main>

<?php include 'imb-footer.php'; ?>
