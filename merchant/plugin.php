<?php include "header.php"; ?>
<?php
if($userdata["aadhar_kyc"] == 1){
    echo "<script> location.replace('dashboard?aadhar_kyc=0') </script>";
}  
 ?>

<style>
     /* Modal box */
        .modal-box {
            width: 100%;
            max-width: 600px;
            padding: 20px;
            font-size: 14px;
            
        }

        /* Media query for mobile */
        @media (max-width: 768px) {
            .modal-box {
                margin-left: 10px;  /* Smaller margin for mobile */
                margin-right: 10px; /* Smaller margin for mobile */
            }
        }
        
         /* Header */
        .modal-header1 {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .modal-subheader {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        /* Details section */
        .modal-details {
            background:#eeeeee;
            border: 1px solid #e1e5ec;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .modal-details p {
        margin: 0px 0;
        font-size: 15px;
        line-height: 24px;
        font-weight: 500;
        color: #333;
        }

        .modal-details h6 {
            font-size: 17px;
            margin: 0px;
            margin-bottom: 10px;
        }

        .modal-details p strong {
            font-weight: bold;
            font-size: 16px;
            
        }

        /* Grid layout for details */
        .modal-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px;
        }

        .modal-details-grid .label {
            font-weight: normal;
        }

        .modal-details .total {
            font-size: 18px;
            font-weight: bold;
            margin-top: 0px;
        }
        
        .modal-details .discountprice {
            color: #D1291A;
            margin-top: 0px;
        }

        /* Buttons */
        .modal-buttons {
            display: flex;
            justify-content: center;
        }

        .modal-buttons button {
            padding: 10px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .modal-buttons .cancel-button {
            background-color: #fff;
            color: #333;
            border: 1px solid #ccc;
            margin-right: 10px;
        }

        .modal-buttons .cancel-button:hover {
            background-color: #f2f2f2;
        }

        .modal-buttons .confirm-button {
            background-color: #25a6a1;
            color: white;
        }

        .modal-buttons .confirm-button:hover {
            background-color: #0994BB;
        }
</style>

<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-code"></i> Plugin SDKs</h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
        </ul>
    </div>

    <div class="tile mb-4">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row row-card-no-pd">
                        <div class="col-md-12">
                            <div class="main-panel">
                                <div class="content">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <!--<h4 class="page-title">SDK Downloads</h4>-->
                                                <!--<p>Discover and download the right SDKs for your application below.</p>-->
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- PHP Array for SDKs with price and discount -->
                                            <?php
                                            switch($userdata["plan_id"]){
                                                case 1:
                                                $percent = 25;
                                                break;
                                                case 5:
                                                $percent = 100;
                                                break;
                                                case 6:
                                                $percent = 100;
                                                break;
                                                case 7:
                                                $percent = 100;
                                                break;
                                                case 8:
                                                $percent = 100;
                                                break;
                                                default:
                                                $percent = 0;
                                                
                                            }
                                            
                                            $sdks = [
                                                ["Android SDK", "sdk/imb_android_tT123gg.zip", "https://admin.tops-int.com/storage/media/editor_images/62782.jpg", 649, $percent],
                                                ["PHP SDK", "sdk/imb_php_656fgfg.zip", "https://www.dxheroes.io/_next/image?url=https%3A%2F%2Fde9hxialx57ow.cloudfront.net%2Fmedia%2F914_1_304895e2a2.png&w=3840&q=75", 209, $percent],
                                                ["Java SDK", "sdk/imb_java_ytytyt6t6.zip", "https://www.oracle.com/a/tech/img/rc10-java-badge-3.png", 349, $percent],
                                                ["Python SDK", "sdk/imb_python_yttsytsa666.zip", "https://blog.sendsafely.com/hubfs/python_logo.png", 625, $percent],
                                                ["C# SDK", "sdk/imb_c_tywetwyet66.zip", "https://cdn.prod.website-files.com/622b8c382517fc649ea0621e/63781909359e1d515871ce85_Csharp.jpg", 377, $percent],
                                                ["Ruby SDK", "sdk/imb_ruby_tydtsyd666.zip", "https://assets.carehq.co.uk/ruby.4ivk82.og_image.001.jpg", 358, $percent],
                                                ["JavaScript SDK", "sdk/imb_javaScript_tytyty.zip", "https://cdn.hashnode.com/res/hashnode/image/upload/v1700553888436/2dfaa2cc-1287-4b62-b412-9afc46013df2.webp?w=1600&h=840&fit=crop&crop=entropy&auto=compress,format&format=webp", 399, $percent],
                                                ["C++ SDK", "sdk/imb_c++_ttduatdau.zip", "https://www.shutterstock.com/image-vector/emblem-c-plus-programming-language-600nw-1669056601.jpg", 289, $percent],
                                                ["Kotlin SDK", "sdk/imb_kotlin_fts2323.zip", "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQNgh5mdo18A5fKdujYPa3OLLMgePnBNBQS9g&s", 279, $percent],
                                                ["Swift SDK", "sdk/imb_swift_shdhsdghgh.zip", "https://developer.apple.com/swift/images/swift-og.png", 369, $percent],
                                                ["WHMCS Modules", "sdk/imb_whmcs_ygsydgsy7.zip", "https://api.cryptocloud.plus/media/articles/img_1723640348.921932.jpg", 649, $percent],
                                                ["WordPress Plugin", "sdk/imb_WordPress_tftftf55.zip", "https://www.pluginhive.com/wp-content/uploads/2023/06/ph_img_woocommerce_wordpress.png", 555, $percent],
                                                ["Colour Prodction sdk", "#", "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSzpuqWqgXfUJerw7t29WPfG4F3aekpKJUwog&s", 250, $percent]
                                            ];

                                            foreach ($sdks as $sdk) {
                                                $fetchpluginuser = $conn->query("SELECT * FROM `plugins_list` WHERE `user_id` = '$userid' AND `plugin_name` = '".$sdk[0]."'");
                                                $discountedPrice = $sdk[3] - ($sdk[3] * ($sdk[4] / 100));
                                                echo '<div class="col-lg-3 col-md-6 mb-4">
                                                    <div class="card">
                                                        <img class="card-img-top" src="' . $sdk[2] . '" alt="' . $sdk[0] . '" style="height: 200px; object-fit: cover;">
                                                        <div class="card-body">
                                                            <h5 class="card-title">' . $sdk[0] . '</h5>
                                                            <p class="card-text">Our SDK provides seamless integration for your applications.</p>
                                                            <p class="card-text"><strong>Price: ₹' . $sdk[3] . '</strong> <span class="text-danger">(' . $sdk[4] . '% OFF)</span></p>
                                                            <p class="card-text"><strong>Discounted Price: ₹' . $discountedPrice . '</strong></p>';
                                                            
                                                            if($discountedPrice == 0 || $fetchpluginuser->num_rows > 0){
                                                            echo '<a href="' . $sdk[1] . '" class="btn btn-success btn-block">Download</a>';
                                                            }else{
                                                            echo '
                                                            <button class="btn btn-primary btn-block subscbtn" data-pname="'.$sdk[0].'" data-price="'.$sdk[3].'" data-amount="'.$discountedPrice.'">Buy Now</button>
                                                            ';
                                                            }
                                                        echo '</div>
                                                    </div>
                                                </div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Confirmation Modal -->
<div class="modal fade" id="subsconfirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
           
            <div class="modal-body">
                
    <div class="modal-box">
        <div class="modal-header1">Confirm your payment</div>
        <div class="modal-subheader">Quickly and secure, free transactions.</div>

               <div class="modal-details" id="modalDetails" style="display: block;">
            <h6>Details</h6>
            <div class="modal-details-grid">
                <p class="label">Name</p>
                <p id="Name"><?= $userdata["name"] ?></p>
                <p class="label">Mobile</p>
                <p id="Mobile"><?= substr($userdata["mobile"],0,3) ?>XXXX<?= substr($userdata["mobile"],-3) ?></p>
                <p class="label">Plugin Name</p>
                <p id="Plan">Stater</p>
                <p class="label">Payment Method</p>
                <p id="paymentMethod">UPI</p>
                <p class="label">Purchase Date</p>
                <p id="paymentDate"><?= date("M d, Y") ?></p>

                <hr style="border: 1px solidrgb(165, 165, 165); margin: 10px 0;">
                <hr style="border: 1px solidrgb(165, 165, 165); margin: 10px 0;">

                <p class="label">Price  :  </p>
                <p id="price" class="price">₹1000.00</p>
                <p class="label">Discount  :  </p>
                <p id="discountprice" class="discountprice">-₹100.00</p>
                <p class="label">Total amount  :  </p>
                <p id="paymentAmount" class="total">₹1000.00</p>
            </div>
        </div>
        
        <div class="modal-buttons">
            <button class="cancel-button" data-dismiss="modal">Cancel Payment</button>
             <form method="POST" action="lib/plugin_pay">
            <input type="hidden" name="amount" class="csubsamount">
            <input type="hidden" name="plugin_name" class="cpname">
            <button name="buypluginbtn" id="confirmButton" class="confirm-button">Confirm Payment</button>
            </form>
        </div>
            </div>
           
        </div>
    </div>
  </div>
</div>

<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>

<script>
    
    $(document).on('click','.subscbtn',function(){
    
    let pname = $(this).data('pname');
    let price = $(this).data('price');
    let amount = $(this).data('amount');
    let discountprice = price - amount;
    
    
    $(".cpname").val(pname);
    $(".csubsamount").val(amount);
    
    $('#Plan').text(pname);
    $('#price').text(`₹ ${price}`);
    $('#discountprice').text(`-₹ ${discountprice}`);
    $('#paymentAmount').text(`₹ ${amount}`);
    
    $("#subsconfirmModal").modal('show');
    
});
</script>

</body>
</html>


