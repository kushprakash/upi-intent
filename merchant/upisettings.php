<?php

include "header.php";

if($userdata["aadhar_kyc"] == 1){
    echo "<script> location.replace('dashboard?aadhar_kyc=0') </script>";
}  

// ini_set("display_errors",true);
// error_reporting(E_ALL);

?>

    <style>
        
        .form-check-input.is-invalid {
    border-color: red;
}
.form-check-input.is-invalid~.form-check-label {
    color: red;
}

.border-bottom {
    border-bottom: 1px solid #dee2e6 !important;
}

#sbi_examp,#hdfc_examp,#paytm_examp,#phonepe_examp{
    display:none;
}

.card{
    box-shadow: 2px 12px 35px -25px rgba(000, 000, 000, 0.4);
}


     .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 25px;
    }
    
    .switch input {
        display: none;
    }
    
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #fff;
        transition: .4s;
        border:2px solid #28a745;
        border-radius: 34px;
    }
    
    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 5px;
        bottom: 3.5px;
        background-color: #28a745;
        transition: .4s;
        border-radius: 50%;
    }
    
    input:checked + .slider {
        background-color: #28a745;
    }
    
    input:checked + .slider:before {
        transform: translateX(20px);
        background-color: #fff;
    }

    </style>
    
    
    <div id="emoji-widget">
        <img src="https://cdnl.iconscout.com/lottie/premium/thumb/customer-care-8147491-6529814.gif" alt="GIF Widget">
    </div>

    <audio id="widget-audio" src="<?= $site_url ?>/Voice/connectmerchants.mp3"></audio>

    <script>
        document.getElementById('emoji-widget').onclick = () => document.getElementById('widget-audio').play();
    </script>
    
    
    <?php 
    $token = $userdata['user_token'];
    $hdfc = $conn->query("SELECT id FROM hdfc WHERE user_token = '$token' ORDER BY id DESC LIMIT 1");
    $paytm = $conn->query("SELECT id FROM paytm_tokens WHERE user_token = '$token' ORDER BY id DESC LIMIT 1");
    $freecharge = $conn->query("SELECT id FROM freecharge WHERE user_token = '$token' ORDER BY id DESC LIMIT 1");
    $mobikwik = $conn->query("SELECT id FROM mobikwik_token WHERE user_token = '$token' ORDER BY id DESC LIMIT 1");
    $phonepe = $conn->query("SELECT sl FROM phonepe_tokens WHERE user_token = '$token' ORDER BY sl DESC LIMIT 1");
    $sbi = $conn->query("SELECT merchant_id FROM merchant WHERE user_token = '$token' ORDER BY merchant_id DESC LIMIT 1");
    $googlepe = $conn->query("SELECT id FROM gpay_tokens WHERE user_token = '$token' ORDER BY id DESC LIMIT 1");
    $bharatpe = $conn->query("SELECT id FROM bharatpe_tokens WHERE user_token = '$token' ORDER BY id DESC LIMIT 1");
    $amazon_pay = $conn->query("SELECT id FROM amazon_pay WHERE user_token = '$token' ORDER BY id DESC LIMIT 1");
    $quintuspay = $conn->query("SELECT id FROM quintus_tokens WHERE user_token = '$token' ORDER BY id DESC LIMIT 1");
   
   
    if($userdata["term_and_condition"] == 0 || $hdfc->num_rows == 0 && $paytm->num_rows == 0 && $freecharge->num_rows == 0 && $mobikwik->num_rows == 0 && $phonepe->num_rows == 0 && $sbi->num_rows == 0 && $googlepe->num_rows == 0 && $bharatpe->num_rows == 0 && $amazon_pay->num_rows == 0){ 
    
    ?>
    <style>
    #merchantaddbox{
        display:none;
    }
    </style>
    <?php }else{ ?>
    <style>
    #select_merchant_box{
        display:none;
    }
    </style>
    
    <?php } ?>
    
    <div class="modal" id="imbmerchant_policy_modal" tabindex="-1" role="dialog" aria-labelledby="edit_txn_status" aria-modal="true" >
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form class="async" action="/user/edit_txn_status" method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Terms and Conditions</h5>
          <div class="d-flex flex-row">
            <ul class="nav nav-line nav-tabs nav-light justify-content-end">
              <li class="nav-item">
                <a class="nav-link active" data-toggle="pill" href="#english_info">
                  <span class="nav-link-text">English</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="pill" href="#hindi_info">
                  <span class="nav-link-text">Hindi</span>
                </a>
              </li>
            </ul>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
          </div>
        </div>
        <div class="modal-body">
          <div class="tab-content">
            <div class="tab-pane fade show active" id="english_info">
              <div class="row mb-2 pb-3">
                <div class="col-12">
                  <div class="row">
                    <div class="col-2">
                      <div class="d-flex align-items-center justify-content-center">
                        <svg class="w-100 avatar-img" viewBox="0 0 800 800" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M216.665 666.667H416.665V133.333H216.665C142.999 133.333 83.332 193 83.332 266.667V533.333C83.332 607 142.999 666.667 216.665 666.667Z" fill="#25a6a1"></path>
                          <path d="M600 666.667H500V133.333H600C673.667 133.333 733.333 193 733.333 266.667V533.333C733.333 607 673.667 666.667 600 666.667Z" fill="#25a6a1"></path>
                          <path d="M500 733.333C486.333 733.333 475 722 475 708.333V91.6665C475 77.9998 486.333 66.6665 500 66.6665C513.667 66.6665 525 77.9998 525 91.6665V708.333C525 722 513.667 733.333 500 733.333Z" fill="#1c7088"></path>
                          <path d="M268.591 267H230.409C208.645 267 197 278.455 197 300.409V338.591C197 360.545 208.645 372 230.409 372H268.591C290.355 372 302 360.545 302 338.591V300.409C302 278.455 290.355 267 268.591 267Z" fill="#1c7088"></path>
                          <path d="M268.591 428H230.409C208.645 428 197 439.455 197 461.409V499.591C197 521.545 208.645 533 230.409 533H268.591C290.355 533 302 521.545 302 499.591V461.409C302 439.455 290.355 428 268.591 428Z" fill="#1c7088"></path>
                        </svg>
                      </div>
                    </div>
                    <div class="col-10">
                      <div class="d-flex align-items-center justify-content-start h-100">
                        <p class="text-muted">We generate QR codes on your behalf so that you can accept payments with ease.</p>
                      </div>
                    </div>
                    <div class="col-12 py-3">
                      <div class="d-flex w-100 border-bottom" ></div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-2">
                      <div class="d-flex align-items-center justify-content-center">
                        <svg class="w-100 avatar-img" viewBox="0 0 800 800" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M733.335 258.997V299.997H66.668V251.33C66.668 174.997 128.668 113.33 205.001 113.33H533.335V198.997C533.335 241.33 558.668 266.663 601.001 266.663H699.001C712.335 266.663 723.668 264.33 733.335 258.997Z" fill="#25a6a1"></path>
                          <path d="M66.668 300V548.667C66.668 625 128.668 686.667 205.001 686.667H595.001C671.335 686.667 733.335 625 733.335 548.667V300H66.668ZM266.668 575H200.001C186.335 575 175.001 563.667 175.001 550C175.001 536.333 186.335 525 200.001 525H266.668C280.335 525 291.668 536.333 291.668 550C291.668 563.667 280.335 575 266.668 575ZM483.335 575H350.001C336.335 575 325.001 563.667 325.001 550C325.001 536.333 336.335 525 350.001 525H483.335C497.001 525 508.335 536.333 508.335 550C508.335 563.667 497.001 575 483.335 575Z" fill="#1c7088"></path>
                          <path d="M698.999 33.3335H600.999C558.665 33.3335 533.332 58.6668 533.332 101V199C533.332 241.333 558.665 266.667 600.999 266.667H698.999C741.332 266.667 766.665 241.333 766.665 199V101C766.665 58.6668 741.332 33.3335 698.999 33.3335ZM711.332 183C714.999 187.333 716.999 192 716.999 197.667C716.999 202.333 714.999 207.667 711.332 211.333C703.665 219 690.665 219 682.999 211.333L649.999 178.333L617.332 211.667C609.665 219.333 596.665 219.333 588.665 211.333C580.999 203.667 580.999 190.667 588.665 183L621.999 150.333L588.999 117.333C581.332 109.667 580.999 96.6668 588.665 88.6668C596.332 80.6668 609.332 81.0002 617.332 89.0002L649.999 122L683.332 88.6668C690.999 81.0002 703.999 81.0002 711.665 88.6668C715.332 93.0002 716.999 98.0002 717.332 103.333C717.332 108 715.332 113.333 711.999 117.333L678.665 150.667L711.332 183Z" fill="#1c7088"></path>
                        </svg>
                      </div>
                    </div>
                    <div class="col-10">
                      <div class="d-flex align-items-center justify-content-start h-100">
                        <p class="text-muted">We do not provide payment gateway services, and we are not involved in the payment process in any way.</p>
                      </div>
                    </div>
                    <div class="col-12 py-3">
                      <div class="d-flex w-100 border-bottom" ></div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-2">
                      <div class="d-flex align-items-center justify-content-center">
                        <svg class="w-100 avatar-img" viewBox="0 0 800 800" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path opacity="0.8" d="M712.363 379.67V579.337C712.363 671.337 637.697 746.004 545.697 746.004H254.362C162.362 746.004 87.6953 671.337 87.6953 579.337V382.004C113.029 409.337 149.029 425.004 188.029 425.004C230.029 425.004 270.362 404.004 295.695 370.337C318.362 404.004 357.03 425.004 400.03 425.004C442.697 425.004 480.697 405.004 503.697 371.67C529.363 404.67 569.03 425.004 610.363 425.004C650.697 425.004 687.363 408.67 712.363 379.67Z" fill="#25a6a1"></path>
                          <path d="M745.328 275.667L735.661 183.333C721.661 82.6665 675.995 41.6665 578.328 41.6665H450.328L474.995 291.666C475.328 295 475.661 298.666 475.661 305C477.661 322.333 482.995 338.333 490.995 352.666C514.995 396.666 561.661 425 610.328 425C654.661 425 694.661 405.333 719.661 370.666C739.661 344 748.661 310.333 745.328 275.667Z" fill="#25a6a1"></path>
                          <path d="M219.655 41.6665C121.655 41.6665 76.3217 82.6665 61.9883 184.333L52.9883 276C49.655 311.666 59.3217 346.333 80.3217 373.333C105.655 406.333 144.655 425 187.988 425C236.655 425 283.322 396.667 306.988 353.333C315.655 338.333 321.322 321 322.988 303L348.99 41.9998H219.655V41.6665Z" fill="#25a6a1"></path>
                          <path d="M378.305 555.333C335.971 559.667 303.973 595.667 303.973 638.333V746H495.638V650C495.971 580.333 454.971 547.333 378.305 555.333Z" fill="#1c7088"></path>
                          <path d="M499.647 41.6665H299.645L274.979 287C272.979 309.666 276.312 331 284.645 350.333C303.979 395.666 349.313 425 399.98 425C451.313 425 495.647 396.333 515.647 350.667C521.647 336.333 525.313 319.666 525.647 302.666V296.333L499.647 41.6665Z" fill="#1c7088"></path>
                        </svg>
                      </div>
                    </div>
                    <div class="col-10">
                      <div class="d-flex align-items-center justify-content-start h-100">
                        <p class="text-muted">The payment made by the user through the QR code will be received in your merchant account.</p>
                      </div>
                    </div>
                    <div class="col-12 py-3">
                      <div class="d-flex w-100 border-bottom" ></div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-2">
                      <div class="d-flex align-items-center justify-content-center">
                        <svg class="w-100 avatar-img" viewBox="0 0 800 800" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M733.335 300V500C733.335 616.667 666.668 666.667 566.668 666.667H324.668C330.334 651 333.335 634.333 333.335 616.667C333.335 534 266.335 466.667 183.335 466.667C136.335 466.667 94.0013 488.667 66.668 522.667V300C66.668 183.333 133.335 133.333 233.335 133.333H566.668C666.668 133.333 733.335 183.333 733.335 300Z" fill="#25a6a1"></path>
                          <path d="M400.001 483.333C446.025 483.333 483.335 446.023 483.335 400C483.335 353.977 446.025 316.667 400.001 316.667C353.978 316.667 316.668 353.977 316.668 400C316.668 446.023 353.978 483.333 400.001 483.333Z" fill="#1c7088"></path>
                          <path d="M616.668 491.667C603.001 491.667 591.668 480.333 591.668 466.667V333.333C591.668 319.667 603.001 308.333 616.668 308.333C630.335 308.333 641.668 319.667 641.668 333.333V466.667C641.668 480.333 630.335 491.667 616.668 491.667Z" fill="#1c7088"></path>
                          <path d="M183.332 466.667C136.332 466.667 93.9987 488.667 66.6654 522.667C45.6654 548.333 33.332 581 33.332 616.667C33.332 699.666 100.665 766.667 183.332 766.667C248.665 766.667 304.332 725 324.665 666.667C330.332 651 333.332 634.333 333.332 616.667C333.332 534 266.332 466.667 183.332 466.667ZM271.998 682C270.998 684 269.665 686.333 267.998 688L240.998 714.667C237.665 718.333 232.998 720 228.331 720C223.331 720 218.666 718.333 215.333 714.667C209.333 709 208.665 700 212.332 693.333H136.998C112.998 693.333 93.3317 673.666 93.3317 649.333V645.667C93.3317 635.333 101.665 627.333 111.665 627.333C121.665 627.333 129.999 635.333 129.999 645.667V649.333C129.999 653.333 132.998 656.667 136.998 656.667H212.332C208.665 649.667 209.333 641 215.333 635C222.333 628 233.998 628 240.998 635L267.998 662C269.665 663.667 270.998 665.667 271.998 668C273.664 672.333 273.664 677.333 271.998 682ZM273.332 587.666C273.332 598 264.999 606 254.999 606C244.999 606 236.665 598 236.665 587.666V584C236.665 580 233.666 576.666 229.666 576.666H154.332C157.999 583.666 157.331 592.333 151.331 598.333C147.998 601.667 143.333 603.667 138.333 603.667C133.666 603.667 128.999 601.667 125.666 598.333L98.666 571.333C96.9994 569.666 95.6664 567.666 94.6664 565.333C92.9997 561 92.9997 556 94.6664 551.333C95.6664 549.333 96.9994 547 98.666 545.333L125.666 518.667C132.666 511.333 144.331 511.333 151.331 518.667C157.331 524.333 157.999 533.333 154.332 540H229.666C253.666 540 273.332 559.667 273.332 584V587.666Z" fill="#1c7088"></path>
                        </svg>
                      </div>
                    </div>
                    <div class="col-10">
                      <div class="d-flex align-items-center justify-content-start h-100">
                        <p class="text-muted">We are not liable for any fraudulent activity that takes place with your merchant account.</p>
                      </div>
                    </div>
                    <div class="col-12 py-3">
                      <div class="d-flex w-100 border-bottom" ></div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-2">
                      <div class="d-flex align-items-center justify-content-center">
                        <svg class="w-100 avatar-img" viewBox="0 0 800 800" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M725.351 530.667L512.018 146.667C483.351 94.9998 443.684 66.6665 400.018 66.6665C356.351 66.6665 316.683 94.9998 288.017 146.667L74.6835 530.667C47.6835 579.667 44.6835 626.667 66.3502 663.667C88.0168 700.667 130.683 721 186.683 721H613.351C669.351 721 712.018 700.667 733.685 663.667C755.351 626.667 752.351 579.333 725.351 530.667Z" fill="#25a6a1"></path>
                          <path d="M400 491.667C386.333 491.667 375 480.333 375 466.667V300C375 286.333 386.333 275 400 275C413.667 275 425 286.333 425 300V466.667C425 480.333 413.667 491.667 400 491.667Z" fill="#1c7088"></path>
                          <path d="M400.001 600.003C398.001 600.003 395.668 599.67 393.335 599.337C391.335 599.003 389.335 598.337 387.335 597.337C385.335 596.67 383.335 595.67 381.335 594.337C379.668 593.003 378.001 591.67 376.335 590.337C370.335 584.003 366.668 575.337 366.668 566.67C366.668 558.003 370.335 549.337 376.335 543.003C378.001 541.67 379.668 540.337 381.335 539.003C383.335 537.67 385.335 536.67 387.335 536.003C389.335 535.003 391.335 534.337 393.335 534.003C397.668 533.003 402.335 533.003 406.335 534.003C408.668 534.337 410.668 535.003 412.668 536.003C414.668 536.67 416.668 537.67 418.668 539.003C420.335 540.337 422.001 541.67 423.668 543.003C429.668 549.337 433.335 558.003 433.335 566.67C433.335 575.337 429.668 584.003 423.668 590.337C422.001 591.67 420.335 593.003 418.668 594.337C416.668 595.67 414.668 596.67 412.668 597.337C410.668 598.337 408.668 599.003 406.335 599.337C404.335 599.67 402.001 600.003 400.001 600.003Z" fill="#1c7088"></path>
                        </svg>
                      </div>
                    </div>
                    <div class="col-10">
                      <div class="d-flex align-items-center justify-content-start h-100">
                        <p class="text-muted">We are not responsible for the suspension of your merchant account due to any reasons.</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="hindi_info">
              <div class="row mb-2 pb-3">
                <div class="col-12">
                  <div class="row">
                    <div class="col-2">
                      <div class="d-flex align-items-center justify-content-center">
                        <svg class="w-100 avatar-img" viewBox="0 0 800 800" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M216.665 666.667H416.665V133.333H216.665C142.999 133.333 83.332 193 83.332 266.667V533.333C83.332 607 142.999 666.667 216.665 666.667Z" fill="#25a6a1"></path>
                          <path d="M600 666.667H500V133.333H600C673.667 133.333 733.333 193 733.333 266.667V533.333C733.333 607 673.667 666.667 600 666.667Z" fill="#25a6a1"></path>
                          <path d="M500 733.333C486.333 733.333 475 722 475 708.333V91.6665C475 77.9998 486.333 66.6665 500 66.6665C513.667 66.6665 525 77.9998 525 91.6665V708.333C525 722 513.667 733.333 500 733.333Z" fill="#1c7088"></path>
                          <path d="M268.591 267H230.409C208.645 267 197 278.455 197 300.409V338.591C197 360.545 208.645 372 230.409 372H268.591C290.355 372 302 360.545 302 338.591V300.409C302 278.455 290.355 267 268.591 267Z" fill="#1c7088"></path>
                          <path d="M268.591 428H230.409C208.645 428 197 439.455 197 461.409V499.591C197 521.545 208.645 533 230.409 533H268.591C290.355 533 302 521.545 302 499.591V461.409C302 439.455 290.355 428 268.591 428Z" fill="#1c7088"></path>
                        </svg>
                      </div>
                    </div>
                    <div class="col-10">
                      <div class="d-flex align-items-center justify-content-start h-100">
                        <p class="text-muted">हम आपकी ओर से QR Code जनरेट करते हैं ताकि आप आसानी से भुगतान स्वीकार कर सकें।</p>
                      </div>
                    </div>
                    <div class="col-12 py-3">
                      <div class="d-flex w-100 border-bottom" ></div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-2">
                      <div class="d-flex align-items-center justify-content-center">
                        <svg class="w-100 avatar-img" viewBox="0 0 800 800" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M733.335 258.997V299.997H66.668V251.33C66.668 174.997 128.668 113.33 205.001 113.33H533.335V198.997C533.335 241.33 558.668 266.663 601.001 266.663H699.001C712.335 266.663 723.668 264.33 733.335 258.997Z" fill="#25a6a1"></path>
                          <path d="M66.668 300V548.667C66.668 625 128.668 686.667 205.001 686.667H595.001C671.335 686.667 733.335 625 733.335 548.667V300H66.668ZM266.668 575H200.001C186.335 575 175.001 563.667 175.001 550C175.001 536.333 186.335 525 200.001 525H266.668C280.335 525 291.668 536.333 291.668 550C291.668 563.667 280.335 575 266.668 575ZM483.335 575H350.001C336.335 575 325.001 563.667 325.001 550C325.001 536.333 336.335 525 350.001 525H483.335C497.001 525 508.335 536.333 508.335 550C508.335 563.667 497.001 575 483.335 575Z" fill="#1c7088"></path>
                          <path d="M698.999 33.3335H600.999C558.665 33.3335 533.332 58.6668 533.332 101V199C533.332 241.333 558.665 266.667 600.999 266.667H698.999C741.332 266.667 766.665 241.333 766.665 199V101C766.665 58.6668 741.332 33.3335 698.999 33.3335ZM711.332 183C714.999 187.333 716.999 192 716.999 197.667C716.999 202.333 714.999 207.667 711.332 211.333C703.665 219 690.665 219 682.999 211.333L649.999 178.333L617.332 211.667C609.665 219.333 596.665 219.333 588.665 211.333C580.999 203.667 580.999 190.667 588.665 183L621.999 150.333L588.999 117.333C581.332 109.667 580.999 96.6668 588.665 88.6668C596.332 80.6668 609.332 81.0002 617.332 89.0002L649.999 122L683.332 88.6668C690.999 81.0002 703.999 81.0002 711.665 88.6668C715.332 93.0002 716.999 98.0002 717.332 103.333C717.332 108 715.332 113.333 711.999 117.333L678.665 150.667L711.332 183Z" fill="#1c7088"></path>
                        </svg>
                      </div>
                    </div>
                    <div class="col-10">
                      <div class="d-flex align-items-center justify-content-start h-100">
                        <p class="text-muted">हम Payment Gateway सेवाएं प्रदान नहीं करते हैं, और हम किसी भी तरह से भुगतान प्रक्रिया में शामिल नहीं हैं।</p>
                      </div>
                    </div>
                    <div class="col-12 py-3">
                      <div class="d-flex w-100 border-bottom" ></div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-2">
                      <div class="d-flex align-items-center justify-content-center">
                        <svg class="w-100 avatar-img" viewBox="0 0 800 800" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path opacity="0.8" d="M712.363 379.67V579.337C712.363 671.337 637.697 746.004 545.697 746.004H254.362C162.362 746.004 87.6953 671.337 87.6953 579.337V382.004C113.029 409.337 149.029 425.004 188.029 425.004C230.029 425.004 270.362 404.004 295.695 370.337C318.362 404.004 357.03 425.004 400.03 425.004C442.697 425.004 480.697 405.004 503.697 371.67C529.363 404.67 569.03 425.004 610.363 425.004C650.697 425.004 687.363 408.67 712.363 379.67Z" fill="#25a6a1"></path>
                          <path d="M745.328 275.667L735.661 183.333C721.661 82.6665 675.995 41.6665 578.328 41.6665H450.328L474.995 291.666C475.328 295 475.661 298.666 475.661 305C477.661 322.333 482.995 338.333 490.995 352.666C514.995 396.666 561.661 425 610.328 425C654.661 425 694.661 405.333 719.661 370.666C739.661 344 748.661 310.333 745.328 275.667Z" fill="#25a6a1"></path>
                          <path d="M219.655 41.6665C121.655 41.6665 76.3217 82.6665 61.9883 184.333L52.9883 276C49.655 311.666 59.3217 346.333 80.3217 373.333C105.655 406.333 144.655 425 187.988 425C236.655 425 283.322 396.667 306.988 353.333C315.655 338.333 321.322 321 322.988 303L348.99 41.9998H219.655V41.6665Z" fill="#25a6a1"></path>
                          <path d="M378.305 555.333C335.971 559.667 303.973 595.667 303.973 638.333V746H495.638V650C495.971 580.333 454.971 547.333 378.305 555.333Z" fill="#1c7088"></path>
                          <path d="M499.647 41.6665H299.645L274.979 287C272.979 309.666 276.312 331 284.645 350.333C303.979 395.666 349.313 425 399.98 425C451.313 425 495.647 396.333 515.647 350.667C521.647 336.333 525.313 319.666 525.647 302.666V296.333L499.647 41.6665Z" fill="#1c7088"></path>
                        </svg>
                      </div>
                    </div>
                    <div class="col-10">
                      <div class="d-flex align-items-center justify-content-start h-100">
                        <p class="text-muted">उपयोगकर्ता द्वारा QR Code के माध्यम से किया गया भुगतान आपके मर्चेंट खाते में प्राप्त होगा।</p>
                      </div>
                    </div>
                    <div class="col-12 py-3">
                      <div class="d-flex w-100 border-bottom" ></div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-2">
                      <div class="d-flex align-items-center justify-content-center">
                        <svg class="w-100 avatar-img" viewBox="0 0 800 800" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M733.335 300V500C733.335 616.667 666.668 666.667 566.668 666.667H324.668C330.334 651 333.335 634.333 333.335 616.667C333.335 534 266.335 466.667 183.335 466.667C136.335 466.667 94.0013 488.667 66.668 522.667V300C66.668 183.333 133.335 133.333 233.335 133.333H566.668C666.668 133.333 733.335 183.333 733.335 300Z" fill="#25a6a1"></path>
                          <path d="M400.001 483.333C446.025 483.333 483.335 446.023 483.335 400C483.335 353.977 446.025 316.667 400.001 316.667C353.978 316.667 316.668 353.977 316.668 400C316.668 446.023 353.978 483.333 400.001 483.333Z" fill="#1c7088"></path>
                          <path d="M616.668 491.667C603.001 491.667 591.668 480.333 591.668 466.667V333.333C591.668 319.667 603.001 308.333 616.668 308.333C630.335 308.333 641.668 319.667 641.668 333.333V466.667C641.668 480.333 630.335 491.667 616.668 491.667Z" fill="#1c7088"></path>
                          <path d="M183.332 466.667C136.332 466.667 93.9987 488.667 66.6654 522.667C45.6654 548.333 33.332 581 33.332 616.667C33.332 699.666 100.665 766.667 183.332 766.667C248.665 766.667 304.332 725 324.665 666.667C330.332 651 333.332 634.333 333.332 616.667C333.332 534 266.332 466.667 183.332 466.667ZM271.998 682C270.998 684 269.665 686.333 267.998 688L240.998 714.667C237.665 718.333 232.998 720 228.331 720C223.331 720 218.666 718.333 215.333 714.667C209.333 709 208.665 700 212.332 693.333H136.998C112.998 693.333 93.3317 673.666 93.3317 649.333V645.667C93.3317 635.333 101.665 627.333 111.665 627.333C121.665 627.333 129.999 635.333 129.999 645.667V649.333C129.999 653.333 132.998 656.667 136.998 656.667H212.332C208.665 649.667 209.333 641 215.333 635C222.333 628 233.998 628 240.998 635L267.998 662C269.665 663.667 270.998 665.667 271.998 668C273.664 672.333 273.664 677.333 271.998 682ZM273.332 587.666C273.332 598 264.999 606 254.999 606C244.999 606 236.665 598 236.665 587.666V584C236.665 580 233.666 576.666 229.666 576.666H154.332C157.999 583.666 157.331 592.333 151.331 598.333C147.998 601.667 143.333 603.667 138.333 603.667C133.666 603.667 128.999 601.667 125.666 598.333L98.666 571.333C96.9994 569.666 95.6664 567.666 94.6664 565.333C92.9997 561 92.9997 556 94.6664 551.333C95.6664 549.333 96.9994 547 98.666 545.333L125.666 518.667C132.666 511.333 144.331 511.333 151.331 518.667C157.331 524.333 157.999 533.333 154.332 540H229.666C253.666 540 273.332 559.667 273.332 584V587.666Z" fill="#1c7088"></path>
                        </svg>
                      </div>
                    </div>
                    <div class="col-10">
                      <div class="d-flex align-items-center justify-content-start h-100">
                        <p class="text-muted">आपके मर्चेंट खाते के साथ होने वाली किसी भी धोखाधड़ी गतिविधि के लिए हम उत्तरदायी नहीं हैं।</p>
                      </div>
                    </div>
                    <div class="col-12 py-3">
                      <div class="d-flex w-100 border-bottom" ></div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-2">
                      <div class="d-flex align-items-center justify-content-center">
                        <svg class="w-100 avatar-img" viewBox="0 0 800 800" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M725.351 530.667L512.018 146.667C483.351 94.9998 443.684 66.6665 400.018 66.6665C356.351 66.6665 316.683 94.9998 288.017 146.667L74.6835 530.667C47.6835 579.667 44.6835 626.667 66.3502 663.667C88.0168 700.667 130.683 721 186.683 721H613.351C669.351 721 712.018 700.667 733.685 663.667C755.351 626.667 752.351 579.333 725.351 530.667Z" fill="#25a6a1"></path>
                          <path d="M400 491.667C386.333 491.667 375 480.333 375 466.667V300C375 286.333 386.333 275 400 275C413.667 275 425 286.333 425 300V466.667C425 480.333 413.667 491.667 400 491.667Z" fill="#1c7088"></path>
                          <path d="M400.001 600.003C398.001 600.003 395.668 599.67 393.335 599.337C391.335 599.003 389.335 598.337 387.335 597.337C385.335 596.67 383.335 595.67 381.335 594.337C379.668 593.003 378.001 591.67 376.335 590.337C370.335 584.003 366.668 575.337 366.668 566.67C366.668 558.003 370.335 549.337 376.335 543.003C378.001 541.67 379.668 540.337 381.335 539.003C383.335 537.67 385.335 536.67 387.335 536.003C389.335 535.003 391.335 534.337 393.335 534.003C397.668 533.003 402.335 533.003 406.335 534.003C408.668 534.337 410.668 535.003 412.668 536.003C414.668 536.67 416.668 537.67 418.668 539.003C420.335 540.337 422.001 541.67 423.668 543.003C429.668 549.337 433.335 558.003 433.335 566.67C433.335 575.337 429.668 584.003 423.668 590.337C422.001 591.67 420.335 593.003 418.668 594.337C416.668 595.67 414.668 596.67 412.668 597.337C410.668 598.337 408.668 599.003 406.335 599.337C404.335 599.67 402.001 600.003 400.001 600.003Z" fill="#1c7088"></path>
                        </svg>
                      </div>
                    </div>
                    <div class="col-10">
                      <div class="d-flex align-items-center justify-content-start h-100">
                        <p class="text-muted">किसी भी कारण से आपके मर्चेंट अकाउंट को बंद कर दिया जाता है तो उसके लिए हम ज़िम्मेदार नहीं हैं।</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12 d-flex justify-content-start flex-column">
              <div class="mb-0">
                <p class="text-muted"></p>
                <div class="form-check">
                  <input class="form-check-input" id="accept_warn_tc_btn" type="checkbox" name="accept_warn_tc_btn" checked="checked" required="">
                  <label class="form-check-label" for="accept_warn_tc_btn" style="font-size: 12px; user-select: none;">
    I have read and accept to the 
    <a href="<?= $site_url ?>/terms" target="_blank" style="text-decoration: none; color: blue;">terms and conditions</a>
    of imb Payment Gateway service.
</label>
</div>
      <div class="form-check mt-2">
        <input class="form-check-input" id="accept_privacy_policy_btn" type="checkbox" name="accept_privacy_policy_btn" checked="checked" required="">
        <label class="form-check-label" for="accept_privacy_policy_btn" style="font-size: 12px; user-select: none;">
          Do you agree with our 
          <a href="<?= $site_url ?>/policy" target="_blank" style="text-decoration: none; color: blue;">Privacy Policy</a>.
        </label>

                </div>
                <p></p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary text-white" id="confirm_merchantaddpolicy_btn" type="button">Agree and Continue</button>
        </div>
      </form>
    </div>
  </div>
  <!-- END Vertically Centered Block Modal-->
</div>
    
    
    <main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="fa fa-university"></i> UPI Settings</h1>
          <!-- <p>Start a beautiful journey here</p> -->
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
          <li class="breadcrumb-item"><a href="upisettings">Dashboard</a></li>
        </ul>
      </div>
      <div class="tile mb-4">
        <div class="page-header">
             <div class="row mb-5">
        <div class="col-md-8">
            <div class="mb-lg-0 mb-2 me-8"><h1 class="pg-title">Connect UPI Merchant</h1>
            <p>Connect your UPI Merchant to the imb Payment Gateway and accept payments online.</p>
            </div>
            
            </div>
            <div class="col-md-4" id="hdfc_examp">
				    <a href="" class="merchant_samplevid"><i class="fa fa-youtube-play" aria-hidden="true"></i> HDFC Merchant Setup Video</a>
				</div>    
				<div class="col-md-4" id="paytm_examp">
				    <a href="" class="merchant_samplevid"><i class="fa fa-youtube-play" aria-hidden="true"></i> Paytm Merchant Setup Video</a>
				    
				</div>    
				<div class="col-md-4" id="phonepe_examp">
				    <a href="" class="merchant_samplevid"><i class="fa fa-youtube-play" aria-hidden="true"></i> Phonepe Merchant Setup Video</a>
				    
				</div>    
				<div class="col-md-4" id="sbi_examp">
				    <a href="" class="merchant_samplevid"><i class="fa fa-youtube-play" aria-hidden="true"></i> SBI Merchant Setup Video</a>
				    
				 </div>
      </div>
     </div>
    
    
    <div class="row pb-4" id="select_merchant_box">
  <div class="col-md-8 flex-column">
    <div class="d-flex flex-column ms-2">
      <h1 class="mb-1 content-heading">You are one step</h1>
      <h1 class="mb-1 content-heading">away from accepting</h1>
      <h1 class="mb-3 content-heading">the payment via QR & UPI Apps</h1>
      <p class="text-muted mb-5">Available Merchant: PhonePe Business, HDFC Bank SmartHub Vyapar, YONO SBI Merchant, Paytm for Business</p>
      <div class="d-flex">
        <div class="d-flex" style="position: relative;">
          
          <button class="btn btn-primary" id="select_merchant_btn" type="button">
            <span class="px-3 py-2">
              <span class="btn-text">Select Merchant</span>
              <span class="icon">
                <span class="feather-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                  </svg>
                </span>
              </span>
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="d-flex justify-content-center align-items-center flex-column h-100">
      <img class="mt-5 mt-md-0 border rounded-4 w-100" src="assets/imbpg_img/addmerchant.gif" alt="UPI Logo" style="border-color: #eaeaea !important;position: absolute;
    top: -35%;">
    </div>
  </div>
</div>
    
    
          <div class="row" id="merchantaddbox">
            <div class="col-lg-12">
						<!-- <h4 class="page-title">UPI Settings</h4> -->
						<div class="row row-card-no-pd">
							<div class="col-md-12">
<?php
if(isset($_POST['set_primary'])) {
    $p_type = mysqli_real_escape_string($conn, $_POST['primary_type']);
    $p_id = mysqli_real_escape_string($conn, $_POST['primary_id']);
    $token = $userdata['user_token'];
    
    $upd = "UPDATE users SET primary_merchant_type = '$p_type', primary_merchant_id='$p_id' WHERE user_token='$token'";
    if(mysqli_query($conn, $upd)) {
         echo '<script>window.location.href="upisettings";</script>';
    }
}
if (isset($_POST['delete'])) {

    

    $merchant_type = mysqli_real_escape_string($conn, $_POST['merchant_type']);
    $merchant_id = mysqli_real_escape_string($conn, $_POST['merchant_id']);
    $token = $userdata['user_token'];

    // Initialize the delete and update queries
    $del = "";
    $update = "";

    // Construct the delete and update queries based on merchant type
    if ($merchant_type == 'hdfc') {
        $del = "DELETE FROM hdfc WHERE user_token = '$token' AND id = '$merchant_id'";
        mysqli_query($conn, $del);
        
        $check = $conn->query("SELECT id FROM hdfc WHERE user_token = '$token'");
        if($check->num_rows == 0){
            $update = "UPDATE users SET hdfc_connected = 'No' WHERE user_token = '$token'";
            mysqli_query($conn, $update);
        }
        $del = ""; // Prevent double execution
        $update = ""; // Prevent double execution
    } elseif ($merchant_type == 'phonepe') {
        $del = "DELETE FROM phonepe_tokens WHERE user_token = '$token' AND sl = '$merchant_id'";
        mysqli_query($conn, $del); // Execute delete immediately
        
        $check = $conn->query("SELECT sl FROM phonepe_tokens WHERE user_token = '$token'");
        if($check->num_rows == 0){
            $update = "UPDATE users SET phonepe_connected = 'No' WHERE user_token = '$token'";
             // Add a query to delete from the store_id table as well if needed
             $del_store_id = "DELETE FROM store_id WHERE user_token = '$token'";
             mysqli_query($conn, $del_store_id);
             mysqli_query($conn, $update); // Execute update immediately
        }
        
        // Prevent re-execution at bottom
        $del = ""; 
        $update = "";

    } elseif ($merchant_type == 'paytm') {
        $del = "DELETE FROM paytm_tokens WHERE user_token = '$token' AND id = '$merchant_id'";
        mysqli_query($conn, $del);

        $check = $conn->query("SELECT id FROM paytm_tokens WHERE user_token = '$token'");
        if($check->num_rows == 0){
             $update = "UPDATE users SET paytm_connected = 'No' WHERE user_token = '$token'";
             mysqli_query($conn, $update);
        }
        $del = "";
        $update = "";
    } elseif ($merchant_type == 'freecharge') {
        $del = "DELETE FROM freecharge WHERE user_token = '$token' AND id = '$merchant_id'";
        mysqli_query($conn, $del);
        
        $check = $conn->query("SELECT id FROM freecharge WHERE user_token = '$token'");
        if($check->num_rows == 0){
             $update = "UPDATE users SET freecharge_connected = 'No' WHERE user_token = '$token'";
             mysqli_query($conn, $update);
        }
        $del = "";
        $update = "";
    }elseif ($merchant_type == 'mobikwik') {
        $del = "DELETE FROM mobikwik_token WHERE user_token = '$token' AND id = '$merchant_id'";
        mysqli_query($conn, $del);
        
        $check = $conn->query("SELECT id FROM mobikwik_token WHERE user_token = '$token'");
        if($check->num_rows == 0){
             $update = "UPDATE users SET mobikwik_connected = 'No' WHERE user_token = '$token'";
             mysqli_query($conn, $update);
        }
        $del = "";
        $update = "";
    }elseif ($merchant_type == 'SBI Merchant') {
        $del = "DELETE FROM merchant WHERE user_token = '$token' AND merchant_id = '$merchant_id'";
        mysqli_query($conn, $del);
        
        $check = $conn->query("SELECT merchant_id FROM merchant WHERE user_token = '$token'");
        if($check->num_rows == 0){
             $update = "UPDATE users SET sbi_connected = 'No' WHERE user_token = '$token'";
             mysqli_query($conn, $update);
        }
        $del = "";
        $update = "";
    } elseif ($merchant_type == 'bharatpe') {
        $del = "DELETE FROM bharatpe_tokens WHERE user_token = '$token' AND id = '$merchant_id'";
        mysqli_query($conn, $del);
        
        $check = $conn->query("SELECT id FROM bharatpe_tokens WHERE user_token = '$token'");
        if($check->num_rows == 0){
             $update = "UPDATE users SET bharatpe_connected = 'No' WHERE user_token = '$token'";
             mysqli_query($conn, $update);
        }
        $del = "";
        $update = "";
    }  elseif ($merchant_type == 'googlepay') {
        $del = "DELETE FROM gpay_tokens WHERE user_token = '$token' AND id = '$merchant_id'";
        mysqli_query($conn, $del);
        
        $check = $conn->query("SELECT id FROM gpay_tokens WHERE user_token = '$token'");
        if($check->num_rows == 0){
             $update = "UPDATE users SET googlepay_connected = 'No' WHERE user_token = '$token'";
             mysqli_query($conn, $update);
        }
        $del = "";
        $update = "";
    }  elseif ($merchant_type == 'quintuspay') {
        $del = "DELETE FROM quintus_tokens WHERE user_token = '$token' AND id = '$merchant_id'";
        mysqli_query($conn, $del); // Execute delete immediately

        $check = $conn->query("SELECT id FROM quintus_tokens WHERE user_token = '$token'");
        if($check->num_rows == 0){
             $update = "UPDATE users SET quintuspay_connected = 'No' WHERE user_token = '$token'";
        } else {
             $update = "UPDATE users SET quintuspay_connected = 'Yes' WHERE user_token = '$token'";
        }
        mysqli_query($conn, $update); // Execute update immediately
        
        // Prevent re-execution at bottom
        $del = ""; 
        $update = "";
    }  elseif ($merchant_type == 'amazonpay') {
        
        $del = "DELETE FROM amazon_pay WHERE user_token = '$token' AND id = '$merchant_id'";
        mysqli_query($conn, $del);
        
        $check = $conn->query("SELECT id FROM amazon_pay WHERE user_token = '$token'");
         if($check->num_rows == 0){
            $update = "UPDATE users SET amazonpay_connected = 'No' WHERE user_token = '$token'";
            mysqli_query($conn, $update);
        }
        $del = "";
        $update = "";
    }

    // Execute the delete query
    // Execute the delete query
    $res_del = (!empty($del)) ? mysqli_query($conn, $del) : true;

    // Execute the update query
    $res_update = mysqli_query($conn, $update);

    if ($res_del) {
        // Show SweetAlert2 success message
        
        echo '<script src="js/jquery-3.2.1.min.js"></script>';
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>
        $("#loading_ajax").hide();
    Swal.fire({
        icon: "success",
        title: "Congratulations! Your Merchant Has been Deleted Successfully!",
        showConfirmButton: true,
        confirmButtonText: "Ok!",
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "upisettings";
        }
    });
</script>';
        exit;
    } else {
        // Show SweetAlert2 error message
        echo '<script src="js/jquery-3.2.1.min.js"></script>';
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>
        $("#loading_ajax").hide();
    Swal.fire({
        icon: "error",
        title: "Merchant Not Deleted! Contact Admin",
        showConfirmButton: true,
        confirmButtonText: "Ok!",
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "upisettings";
        }
    });
</script>';
        exit;
    }
}
?>


<?php

if(isset($_POST['addmerchant'])){
    
    
   
    $bbbytemerchant = mysqli_real_escape_string($conn, $_POST['merchant_name']);
    
    if ($bbbytemerchant=="hdfc"){
        
    
    
    $no = mysqli_real_escape_string($conn, $_POST['c_mobile']);
    $check_mobile = $conn->query("SELECT id FROM hdfc WHERE user_token = '" . $userdata['user_token'] . "' AND number = '$no'");
    if($check_mobile->num_rows > 0){
         echo '<script src="js/jquery-3.2.1.min.js"></script>';
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>
        $("#loading_ajax").hide();
    Swal.fire({
        icon: "error",
        title: "This Mobile Number Already Connected",
        showConfirmButton: true,
        confirmButtonText: "Ok!",
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "upisettings";
        }
    });
</script>';
        exit;
    }
    
    $data = "INSERT INTO hdfc(number, seassion, device_id, user_token, pin, upi_hdfc, UPI, tidlist, status, mobile) VALUES ('$no','','','" . $userdata['user_token'] . "','','','','', 'Deactive','$mobile')";
    $insert = mysqli_query($conn, $data);
    }
    
    elseif ($bbbytemerchant=="phonepe"){
        
         if($userdata["phonepe_connected"] == 'Yes'){
         echo '<script src="js/jquery-3.2.1.min.js"></script>';
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>
        $("#loading_ajax").hide();
    Swal.fire({
        icon: "error",
        title: "Phonepe Merchant Already Connected",
        showConfirmButton: true,
        confirmButtonText: "Ok!",
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "upisettings";
        }
    });
</script>';
        exit;
    }  
        
        $no = mysqli_real_escape_string($conn, $_POST['c_mobile']);
        $bbbytetokken=$userdata['user_token'];
        
        $check_mobile = $conn->query("SELECT sl FROM phonepe_tokens WHERE user_token = '$bbbytetokken' AND phoneNumber = '$no'");
        if($check_mobile->num_rows > 0){
             echo '<script src="js/jquery-3.2.1.min.js"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
            echo '<script>
            $("#loading_ajax").hide();
        Swal.fire({
            icon: "error",
            title: "This Mobile Number Already Connected",
            showConfirmButton: true,
            confirmButtonText: "Ok!",
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "upisettings";
            }
        });
    </script>';
            exit;
        }

        $data = "INSERT INTO phonepe_tokens (user_token, phoneNumber, userId, token, refreshToken, name, device_data)
        VALUES ('$bbbytetokken', '$no', '', '', '', '', '')";
$insert = mysqli_query($conn, $data);


    }
    elseif ($bbbytemerchant=="paytm"){
        
    
        
        $no = mysqli_real_escape_string($conn, $_POST['c_mobile']);
        $bbbytetokken=$userdata['user_token'];
        
        $check_mobile = $conn->query("SELECT id FROM paytm_tokens WHERE user_token = '$bbbytetokken' AND phoneNumber = '$no'");
        if($check_mobile->num_rows > 0){
             echo '<script src="js/jquery-3.2.1.min.js"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
            echo '<script>
            $("#loading_ajax").hide();
        Swal.fire({
            icon: "error",
            title: "This Mobile Number Already Connected",
            showConfirmButton: true,
            confirmButtonText: "Ok!",
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "upisettings";
            }
        });
    </script>';
            exit;
        }

        $data = "INSERT INTO paytm_tokens (user_token, phoneNumber, MID, Upiid)
        VALUES ('$bbbytetokken', '$no', '','')";
        $insert = mysqli_query($conn, $data);
    }
    elseif ($bbbytemerchant=="freecharge"){
        
    
    
        
        $no = mysqli_real_escape_string($conn, $_POST['c_mobile']);
        $bbbytetokken=$userdata['user_token'];
        
        $check_mobile = $conn->query("SELECT id FROM freecharge WHERE user_token = '$bbbytetokken' AND number = '$no'");
        if($check_mobile->num_rows > 0){
             echo '<script src="js/jquery-3.2.1.min.js"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
            echo '<script>
            $("#loading_ajax").hide();
        Swal.fire({
            icon: "error",
            title: "This Mobile Number Already Connected",
            showConfirmButton: true,
            confirmButtonText: "Ok!",
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "upisettings";
            }
        });
    </script>';
            exit;
        }

        $data = "INSERT INTO freecharge (user_token, number, cookie, upi_id)
        VALUES ('$bbbytetokken', '$no', '','')";
        $insert = mysqli_query($conn, $data);


    }elseif ($bbbytemerchant=="mobikwik"){
        
    
    
        
        $no = mysqli_real_escape_string($conn, $_POST['c_mobile']);
        $bbbytetokken=$userdata['user_token'];
         $fcuser_id = $userdata['id']; // Assuming the user ID is stored in the session
         
         $check_mobile = $conn->query("SELECT id FROM mobikwik_token WHERE user_token = '$bbbytetokken' AND phoneNumber = '$no'");
        if($check_mobile->num_rows > 0){
             echo '<script src="js/jquery-3.2.1.min.js"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
            echo '<script>
            $("#loading_ajax").hide();
        Swal.fire({
            icon: "error",
            title: "This Mobile Number Already Connected",
            showConfirmButton: true,
            confirmButtonText: "Ok!",
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "upisettings";
            }
        });
    </script>';
            exit;
        }

$data = "INSERT INTO mobikwik_token (user_token, phoneNumber, user_id)
         VALUES ('$bbbytetokken', '$no', '$fcuser_id')";
$insert = mysqli_query($conn, $data);


    }elseif ($bbbytemerchant=="SBI Merchant"){
        
    
         $merchant_username = $_POST['merchant_username'];
         $merchant_password = strip_tags($_POST['merchant_password']);
         $bbbytetokken=$userdata['user_token'];
         
         $check_mobile = $conn->query("SELECT merchant_id FROM merchant WHERE user_token = '$bbbytetokken' AND merchant_username = '$merchant_username'");
        if($check_mobile->num_rows > 0){
             echo '<script src="js/jquery-3.2.1.min.js"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
            echo '<script>
            $("#loading_ajax").hide();
        Swal.fire({
            icon: "error",
            title: "This SBI Username Already Connected",
            showConfirmButton: true,
            confirmButtonText: "Ok!",
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "upisettings";
            }
        });
    </script>';
            exit;
        }
        
        $data = "INSERT INTO `merchant`(`merchant_name`, `merchant_username`, `merchant_password`, `merchant_timestamp`, `merchant_session`, `merchant_csrftoken`, `merchant_token`, `user_token`,`user_id`, `status`) VALUES ('".$bbbytemerchant."','".$merchant_username."','".$merchant_password."','".date("Y-m-d H:i:s")."','','','','$bbbytetokken','".$userdata['id']."','InActive')";
        $insert = mysqli_query($conn, $data);


    }
    
    elseif ($bbbytemerchant=="bharatpe"){
        
    
        $no = mysqli_real_escape_string($conn, $_POST['c_mobile']);
        $bbbytetokken=$userdata['user_token'];
        
        $check_mobile = $conn->query("SELECT id FROM bharatpe_tokens WHERE user_token = '$bbbytetokken' AND phoneNumber = '$no'");
        if($check_mobile->num_rows > 0){
             echo '<script src="js/jquery-3.2.1.min.js"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
            echo '<script>
            $("#loading_ajax").hide();
        Swal.fire({
            icon: "error",
            title: "This Mobile Number Already Connected",
            showConfirmButton: true,
            confirmButtonText: "Ok!",
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "upisettings";
            }
        });
    </script>';
            exit;
        }

        $data = "INSERT INTO bharatpe_tokens (user_token, phoneNumber, token, cookie, merchantId)
        VALUES ('$bbbytetokken', '$no', '', '', '')";
        $insert = mysqli_query($conn, $data);

    }elseif ($bbbytemerchant=="amazonpay"){
        
    
        $no = mysqli_real_escape_string($conn, $_POST['c_mobile']);
        $bbbytetokken=$userdata['user_token'];
        
         $check_mobile = $conn->query("SELECT id FROM amazon_pay WHERE user_token = '$bbbytetokken' AND phoneNumber = '$no'");
        if($check_mobile->num_rows > 0){
             echo '<script src="js/jquery-3.2.1.min.js"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
            echo '<script>
            $("#loading_ajax").hide();
        Swal.fire({
            icon: "error",
            title: "This Mobile Number Already Connected",
            showConfirmButton: true,
            confirmButtonText: "Ok!",
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "upisettings";
            }
        });
    </script>';
            exit;
        }

        $data = "INSERT INTO amazon_pay (user_token, phoneNumber, upi_id, cookie,status)
        VALUES ('$bbbytetokken', '$no', '', '', 'Deactive')";
        $insert = mysqli_query($conn, $data);
     }
     
     elseif ($bbbytemerchant=="googlepay"){
        
    
        $no = mysqli_real_escape_string($conn, $_POST['c_mobile']);
        $bbbytetokken=$userdata['user_token'];
        
        $check_mobile = $conn->query("SELECT id FROM gpay_tokens WHERE user_token = '$bbbytetokken' AND phoneNumber = '$no'");
        if($check_mobile->num_rows > 0){
             echo '<script src="js/jquery-3.2.1.min.js"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
            echo '<script>
            $("#loading_ajax").hide();
        Swal.fire({
            icon: "error",
            title: "This Mobile Number Already Connected",
            showConfirmButton: true,
            confirmButtonText: "Ok!",
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "upisettings";
            }
        });
    </script>';
            exit;
        }

        $data = "INSERT INTO gpay_tokens (user_token, phoneNumber, status)
        VALUES ('$bbbytetokken', '$no', 'Deactive')";
        $insert = mysqli_query($conn, $data);
        
    } elseif ($bbbytemerchant=="quintuspay"){
    
    
        $no = mysqli_real_escape_string($conn, $_POST['c_mobile']);
        $bbbytetokken=$userdata['user_token'];
        
        $check_mobile = $conn->query("SELECT id FROM quintus_tokens WHERE user_token = '$bbbytetokken' AND phoneNumber = '$no'");
        if($check_mobile->num_rows > 0){
             echo '<script src="js/jquery-3.2.1.min.js"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
            echo '<script>
            $("#loading_ajax").hide();
        Swal.fire({
            icon: "error",
            title: "This Mobile Number Already Connected",
            showConfirmButton: true,
            confirmButtonText: "Ok!",
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "upisettings";
            }
        });
    </script>';
            exit;
        }

        $data = "INSERT INTO quintus_tokens (user_token, phoneNumber, status)
        VALUES ('$bbbytetokken', '$no', 'Deactive')";
        $insert = mysqli_query($conn, $data);


    }
    
    
        
    
    
    if($insert){
        
        
        
        
        // Show SweetAlert2 success message
        echo '<script src="js/jquery-3.2.1.min.js"></script>';
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
$("#loading_ajax").hide();
    Swal.fire({
        icon: "success",
        title: "Congratulations! Your Merchant Hasbeen Added Successfully!",
        showConfirmButton: true, // Show the confirm button
        confirmButtonText: "Ok!", // Set text for the confirm button
        allowOutsideClick: false, // Prevent the user from closing the popup by clicking outside
        allowEscapeKey: false // Prevent the user from closing the popup by pressing Escape key
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "upisettings"; // Redirect to "upisettings" when the user clicks the confirm button
        }
    });
</script>';
 exit;
        
    
        
        
    }else{
        
        // Show SweetAlert2 error message
        echo '<script src="js/jquery-3.2.1.min.js"></script>';
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
$("#loading_ajax").hide();
    Swal.fire({
        icon: "error",
        title: "Opps Sorry Merhcant Adding Failure!",
        showConfirmButton: true, // Show the confirm button
        confirmButtonText: "Ok!", // Set text for the confirm button
        allowOutsideClick: false, // Prevent the user from closing the popup by clicking outside
        allowEscapeKey: false // Prevent the user from closing the popup by pressing Escape key
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "upisettings"; // Redirect to "upisettings" when the user clicks the confirm button
        }
    });
</script>';
exit;

    }
}

?>




            
								<div class="row text-end">
								   <div class="col-12 mb-4">
  <div class="card card-border overflow-hidden">
    <div class="card-body">
      <div class="row">
        <div class="col-md-2 col-sm-5 position-absolute d-none d-md-block">
          <div class="d-flex align-items-center justify-content-center w-100">
            <div class="d-flex position-relative w-100"></div>
            <img class="d-flex" style="position: absolute;top: -15px;" width="140px" src="assets/imbpg_img/upiQr02.svg" alt="imb PG">
          </div>
        </div>
        <div class="col-md-2 col-sm-5"></div>
        <div class="col-md-7 col-sm-8">
          <div class="d-flex align-items-start justify-content-center flex-column h-100 py-2">
            <div class="d-flex flex-column align-items-start">
              <h5 class="fw-medium text-dark">Add Merchant: To Receive Payments From Your Customers Using QR Code Or UPI Apps</h5>
              <div class="fs-7">*Available Merchant: PhonePe Business, HDFC Bank SmartHub Vyapar, YONO SBI Merchant, Paytm for Business, BharatPe for Merchants, Freecharge, Mobikwik, Amazon Pay.</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-4">
          <div class="mt-3 mt-md-0 d-md-flex align-items-center justify-content-center flex-column h-100">
           
            	<button type="button" id="addmerchant" class="btn btn-primary btn-block addmerchant">Add Merchant</button> 
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
								   <div class="col-12 mb-4">
  <!--<div class="card card-border overflow-hidden">-->
  <!--  <div class="card-body">-->
  <!--    <div class="row">-->
  <!--      <div class="col-md-2 col-sm-5 position-absolute d-none d-md-block">-->
  <!--        <div class="d-flex align-items-center justify-content-center w-100">-->
  <!--          <div class="d-flex position-relative w-100"></div>-->
  <!--          <img class="d-flex" style="position: absolute;top: -15px;" width="140px" src="assets/imbpg_img/upiQr01.jpg" alt="imb PG">-->
  <!--        </div>-->
  <!--      </div>-->
        <!--<div class="col-md-2 col-sm-5"></div>-->
        <!--<div class="col-md-7 col-sm-8">-->
        <!--  <div class="d-flex align-items-start justify-content-center flex-column h-100 py-2">-->
        <!--    <div class="d-flex flex-column align-items-start">-->
        <!--      <h5 class="fw-medium text-dark">Add Multiple Merchant : Add Same Merchant To Receive Payments From Your Customers In your all merchant account easly.</h5>-->
        <!--      <div class="fs-7">*Available Same Merchant: YONO SBI Merchant, Paytm for Business, Freecharge, Amazon Pay.</div>-->
        <!--    </div>-->
        <!--  </div>-->
        <!--</div>-->
        <!--<div class="col-md-3 col-sm-4">-->
        <!--  <div class="mt-3 mt-md-0 d-md-flex align-items-center justify-content-center flex-column h-100">-->
           
        <!--   <?php if($userdata["plan_id"] < 5){ ?>-->
        <!--    	<a href="subscription" disabled="" class="btn btn-primary btn-block">-->
        <!--    	    <i class="fa fa-lock" aria-hidden="true"></i> Buy Enterprise Plan</a> -->
        <!--   <?php }else{ ?>-->
        <!--    	<button type="button" id="addmerchant" class="btn btn-primary btn-block addmerchant">Add Merchant</button> -->
        <!--    	<span class="badge badge-success">Active</span>-->
            	
        <!--   <?php } ?>-->
        <!--  </div>-->
        </div>
      </div>
    </div>
  </div>
</div>
                    
								</div>
								
							</form>	

<?php
$token = $userdata['user_token'];
$fetchData = "
    SELECT 'HDFC' AS merchant_type, id, number, date, status FROM hdfc WHERE user_token = '$token' 
    UNION ALL 
    SELECT 'PhonePe' AS merchant_type, sl AS id, phoneNumber AS number, date, status FROM phonepe_tokens WHERE user_token = '$token'
    UNION ALL
    SELECT 'Paytm' AS merchant_type, id, phoneNumber AS number, date, status FROM paytm_tokens WHERE user_token = '$token'
    UNION ALL
    SELECT 'Freecharge' AS merchant_type, id, number, date, status FROM freecharge WHERE user_token = '$token'
    UNION ALL
    SELECT 'SBI' AS merchant_type, merchant_id AS id, merchant_username AS number,merchant_timestamp AS date, status FROM merchant WHERE user_token = '$token'
    UNION ALL
    SELECT 'Bharatpe' AS merchant_type, id, phoneNumber AS number, date, status FROM bharatpe_tokens WHERE user_token = '$token'
    UNION ALL
    SELECT 'MOBIKWIK' AS merchant_type, id, phoneNumber AS number, date, status FROM mobikwik_token WHERE user_token = '$token'
    UNION ALL
    SELECT 'Amazonpay' AS merchant_type, id, phoneNumber AS number, date, status FROM amazon_pay WHERE user_token = '$token'
    UNION ALL
    SELECT 'Googlepay' AS merchant_type, id, phoneNumber AS number, date, status FROM gpay_tokens WHERE user_token = '$token'
    UNION ALL
    SELECT 'QuintusPay' AS merchant_type, id, phoneNumber AS number, date, status FROM quintus_tokens WHERE user_token = '$token'
";


$ssData = mysqli_query($conn, $fetchData);
// if (!$ssData) {
//     die("Error in query execution: " . mysqli_error($conn));
// }

                    ?>
                    
                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4 class="page-title">All Merchants</h4>
                                                <p>Merchant list verify and update your merchant details.</p>
                                            </div>
                                        </div>
                                        
                                         <div class="row">
						
        <?php
        if (mysqli_num_rows($ssData) > 0) {
        while ($merchant = mysqli_fetch_array($ssData)) {
            $class = ($merchant['status'] == 'Active' || $merchant['status'] == 'Off') ? 'text-success' : 'text-danger';
        ?>
                    
                    <div class="col-lg-4 col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="fmdbox d-flex align-items-center justify-content-between">
                                <h6>
                                    <img class="icon" style="width: 25px;margin-right: 5px;" src="../application/assets/images/upi_logo/<?= $merchant['merchant_type'] ?>.svg" alt="upi_logo"><?= $merchant['merchant_type'] ?>
                                    </h6>
                                     <div class="switch-container d-flex align-items-center flex-column">
                                         
                     <?php if($userdata["plan_id"] != 1){ ?>   
                    <label class="switch">
            <input type="checkbox" <?php if($merchant['status'] != 'Off'){ echo 'checked'; } ?> class="updatemerchantst_btn" data-mid="<?= $merchant['id']; ?>" data-mtype="<?= $merchant['merchant_type']; ?>">
                        <span class="slider"></span>
                    </label>
                     <?php } ?>   
                                    <p class="card-text <?= $class ?>">
                                    <strong>
                        <?= ($merchant['status'] == 'Active' || $merchant['status'] == 'Off') ? 'Active' : 'Deactive' ?>
                                    </strong>
                                    </p>
                   </div>
                      <!-- PRIMARY BADGE START -->
                      <?php
                        $is_primary = false;
                        if(isset($userdata['primary_merchant_type']) && isset($userdata['primary_merchant_id'])) {
                             if($userdata['primary_merchant_type'] == $merchant['merchant_type'] && $userdata['primary_merchant_id'] == $merchant['id']) {
                                 $is_primary = true;
                             }
                        }
                        if($is_primary) {
                            echo '<span class="badge badge-warning ml-2"><i class="fa fa-star"></i> Primary</span>';
                        }
                      ?>
                      <!-- PRIMARY BADGE END -->
                                </div>
                                <h3>******<?= substr($merchant['number'],-4) ?></h3>
                                
                                <!-- MAKE PRIMARY BUTTON -->
                                <?php if(!$is_primary && ($merchant['status'] == 'Active' || $merchant['status'] == 'Off')) { ?>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display:inline-block; width:100%;">
                                    <input type="hidden" name="primary_type" value="<?= $merchant['merchant_type'] ?>">
                                    <input type="hidden" name="primary_id" value="<?= $merchant['id'] ?>">
                                    <button class="btn btn-warning btn-sm btn-block mb-2" name="set_primary" title="Set as Primary for Single Merchant Mode"> <i class="fa fa-star-o"></i> Set Primary</button>
                                </form>
                                <?php } ?>
                               
                                                          <?php
                            if ($merchant['merchant_type'] == 'HDFC') {
                                // HDFC specific actions
                                ?>
                                <div class="fmdbox d-flex align-items-center justify-content-between">
                                    
                                <form action="send_hdfcotp" method="post">
                                    <input type="hidden" name="hdfc_mobile" value="<?php echo $merchant['number']; ?>">
                                    <button class="btn btn-info btn-xs mb-2 mt-1" name="Verify">Verify</button>
                                </form>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="hdfc_mobile" value="<?php echo $merchant['number']; ?>">
                                    <input type="hidden" name="merchant_type" value="hdfc">
                                    <input type="hidden" name="merchant_id" value="<?php echo $merchant['id']; ?>">
                                    
                                    <button class="btn btn-danger btn-xs mb-2 mt-1" name="delete">Delete</button>
                                </form>
                                </div>
                                <?php
                            } elseif ($merchant['merchant_type'] == 'PhonePe') {
                                // Phonepe specific actions
                                ?>
                                 <div class="fmdbox d-flex align-items-center justify-content-between">
                                    
                                <form action="send_phonepeotp" method="post">
                                    <input type="hidden" name="phonepe_mobile" value="<?php echo $merchant['number']; ?>">
                                    <input type="hidden" name="merchant_id" value="<?php echo $merchant['id']; ?>">
                                    <button class="btn btn-info btn-xs mb-2 mt-1" name="Verify">Verify</button>
                                </form>
                                
                                <form action="#" method="post">
                                    <button type="button" class="btn btn-success btn-xs mb-2 mt-1 updateupibtn" data-mname="phonepe"  data-mno="<?php echo $merchant['number']; ?>">Manage UPI</button>
                                </form>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="phonepe_mobile" value="<?php echo $merchant['number']; ?>">
                                    <input type="hidden" name="merchant_id" value="<?php echo $merchant['id']; ?>">
                                    <input type="hidden" name="merchant_type" value="phonepe">
                                    <button class="btn btn-danger btn-xs mb-2 mt-1" name="delete">Delete</button>
                                </form>
                                </div>
                                
                                <?php
                            } elseif ($merchant['merchant_type'] == 'Paytm') {
                                // Paytm specific actions
                                ?>
                                 <div class="fmdbox d-flex align-items-center justify-content-between">
                                    
                        <button class="btn btn-info btn-xs mb-2 mt-1 verifypaytmbtn" data-mid="<?php echo $merchant['id']; ?>" data-mobile="<?php echo $merchant['number']; ?>" name="Verify">Verify</button>
                                
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="paytm_mobile" value="<?php echo $merchant['number']; ?>">
                                    <input type="hidden" name="merchant_id" value="<?php echo $merchant['id']; ?>">
                                    <input type="hidden" name="merchant_type" value="paytm">
                                    <button class="btn btn-danger btn-xs mb-2 mt-1" name="delete">Delete</button>
                                </form>
                                </div>
                                <?php
                            } elseif ($merchant['merchant_type'] == 'Freecharge') {
                                // Paytm specific actions
                                ?>
                                 <div class="fmdbox d-flex align-items-center justify-content-between">
                                    
                                <form action="send_freechargeotp" method="post">
                                    <input type="hidden" name="freecharge_mobile" value="<?php echo $merchant['number']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="merchant_id" value="<?php echo $merchant['id']; ?>">
                                    <button class="btn btn-info btn-xs mb-2 mt-1" name="Verify">Verify</button>
                                </form>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="freecharge_mobile" value="<?php echo $merchant['number']; ?>">
                                    <input type="hidden" name="merchant_id" value="<?php echo $merchant['id']; ?>">
                                    <input type="hidden" name="merchant_type" value="freecharge">
                                    <button class="btn btn-danger btn-xs mb-2 mt-1" name="delete">Delete</button>
                                </form>
                                </div>
                                
                                <?php
                                } elseif ($merchant['merchant_type'] == 'MOBIKWIK') {
                                ?>
                                
                                 <div class="fmdbox d-flex align-items-center justify-content-between">
                                    
                                <form action="send_mobikwikotp" method="post">
                                <input type="hidden" name="mobikwik_mobile" value="<?php echo $merchant['number']; ?>">
                                <input type="hidden" name="merchant_id" value="<?php echo $merchant['id']; ?>">
                                <button class="btn ripple btn-primary px-2" name="Verify">Verify</button>
                                </form>
                                
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="mobikwik_mobile" value="<?php echo $merchant['number']; ?>">
                                    <input type="hidden" name="merchant_id" value="<?php echo $merchant['id']; ?>">
                                    <input type="hidden" name="merchant_type" value="mobikwik">
                                    <button class="btn btn-danger btn-xs mb-2 mt-1" name="delete">Delete</button>
                                </form>
                                </div>
                                
                                <?php
                            } elseif ($merchant['merchant_type'] == 'SBI') {
                                // Paytm specific actions
                                ?>
                                 <div class="fmdbox d-flex align-items-center flex-wrap justify-content-between">
                                <button class="btn btn-info btn-xs mb-2 mt-1" title="Verify" onclick="get_merchant_otp('<?=$merchant['id']?>')">Verify</button>
							  <button class="btn btn-success btn-xs mb-2 mt-1" title="View" onclick="get_merchant_view('<?=$merchant['id']?>')">View</button>
							   <form action="#" method="post">
                                    <button type="button" class="btn btn-success btn-xs mb-2 mt-1 updateupibtn" data-mname="sbi"  data-mno="<?php echo $merchant['number']; ?>">Manage UPI</button>
                                </form>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="sbi_mobile" value="<?php echo $merchant['number']; ?>">
                                    <input type="hidden" name="merchant_id" value="<?php echo $merchant['id']; ?>">
                                    <input type="hidden" name="merchant_type" value="SBI Merchant">
                                    <button class="btn btn-danger btn-xs mb-2 mt-1" name="delete">Delete</button>
                                </form>
                                    
                                </div>
                                <?php
                            } elseif ($merchant['merchant_type'] == 'Bharatpe') {
                                // Bharatpe specific actions
                                ?>
                                 <div class="fmdbox d-flex align-items-center justify-content-between">
                                    
                                <form action="send_bharatpeotp" method="post">
                                    <input type="hidden" name="bharatpe_mobile" value="<?php echo $merchant['number']; ?>">
                                    <input type="hidden" name="merchant_id" value="<?php echo $merchant['id']; ?>">
                                    <button class="btn btn-info btn-xs mb-2 mt-1" name="Verify">Verify</button>
                                </form>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="bharatpe_mobile" value="<?php echo $merchant['number']; ?>">
                                    <input type="hidden" name="merchant_id" value="<?php echo $merchant['id']; ?>">
                                    <input type="hidden" name="merchant_type" value="bharatpe">
                                    <button class="btn btn-danger btn-xs mb-2 mt-1" name="delete">Delete</button>
                                </form>
                                </div>
                                <?php
                            } elseif ($merchant['merchant_type'] == 'Amazonpay') {
                                // Bharatpe specific actions
                                ?>
                                 <div class="fmdbox d-flex align-items-center justify-content-between">
                                    
                                <form action="send_amazonpayotp" method="post">
                                    <input type="hidden" name="amazonpay_mobile" value="<?php echo $merchant['number']; ?>">
                                    <input type="hidden" name="merchant_id" value="<?php echo $merchant['id']; ?>">
                                    <button class="btn btn-info btn-xs mb-2 mt-1" name="Verify">Verify</button>
                                </form>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="amazonpay_mobile" value="<?php echo $merchant['number']; ?>">
                                    <input type="hidden" name="merchant_id" value="<?php echo $merchant['id']; ?>">
                                    <input type="hidden" name="merchant_type" value="amazonpay">
                                    <button class="btn btn-danger btn-xs mb-2 mt-1" name="delete">Delete</button>
                                </form>
                                </div>
                                <?php
                            } elseif ($merchant['merchant_type'] == 'Googlepay') {
                        ?>
                         <div class="fmdbox d-flex align-items-center justify-content-between">
                        <form action="send_googlepayotp" method="post">
                            <input type="hidden" name="gpay_mobile" value="<?php echo $merchant['number']; ?>">
                            <input type="hidden" name="merchant_id" value="<?php echo $merchant['id']; ?>">
                            
                            <button class="btn btn-info btn-xs mb-2 mt-1" name="Verify">Verify</button>
                        </form>
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="googlepay_mobile" value="<?php echo $merchant['number']; ?>">
                            
                            <input type="hidden" name="merchant_id" value="<?php echo $merchant['id']; ?>">
                            <input type="hidden" name="merchant_type" value="googlepay">
                            <button class="btn btn-danger btn-xs mb-2 mt-1" name="delete">Delete</button>
                        </form>
                        </div>
                        <?php
                          } elseif ($merchant['merchant_type'] == 'QuintusPay') {
                        ?>
                         <div class="fmdbox d-flex align-items-center justify-content-between">
                        <form action="send_quintuspayotp" method="post">
                            <input type="hidden" name="quintus_mobile" value="<?php echo $merchant['number']; ?>">
                            <input type="hidden" name="merchant_id" value="<?php echo $merchant['id']; ?>">
                            
                            <button class="btn btn-info btn-xs mb-2 mt-1" name="Verify">Verify</button>
                        </form>
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="quintus_mobile" value="<?php echo $merchant['number']; ?>">
                            
                            <input type="hidden" name="merchant_id" value="<?php echo $merchant['id']; ?>">
                            <input type="hidden" name="merchant_type" value="quintuspay">
                            <button class="btn btn-danger btn-xs mb-2 mt-1" name="delete">Delete</button>
                        </form>
                        </div>
                        <?php
                    }
                    ?>
                                                            
                   </div>
                </div>
            </div>
                           
            <?php
        }
    }
    ?>


					</div>
				</div>
				
			     </div>
			   	</div>
			</div>
		</div>

            
				</main>
				
	<div class="modal fade" id="addmerchantmodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
     
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body" style="padding: 25px 30px;">
          <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mb-2">

								<div class="row">
								    
								     <div class="col-md-6 mb-2" id="amvideotut">
								        <img src="assets/imbpg_img/addmerchant.gif" width="250px" alt="merchant">
								    </div>
								   
								    <div class="col-md-6 mb-2">
								        <h5 class="modal-title">Add UPI Merchant</h5>
								        <p>Add your UPI business account with us less than 2 mins.</p>
								        
								        <div class="row" id="merchant">
								         <div class="col-md-12 mb-2">
    									<label>Merchant Name</label>
    									<select type="number" name="merchant_name" class="form-control" onchange="get_merchant(this.value,'#merchant')" required>
    									    <option value="" selected >Select Merchant</option>
    									    <option value="hdfc">HDFC Smart hub Vyapar</option>
    									    <option value="phonepe">Phonepe For Business</option>
    									    <option value="paytm">Paytm For Business</option>
    									    <option value="SBI Merchant">Yono SBI Merchant</option>
    									    <option value="bharatpe">BharatPe Merchant</option>
    									    <option value="freecharge">Freecharge (Direct Bank Link)</option>
    									    <option value="mobikwik">Mobikwik (Direct Bank Link)</option>
    									    <option value="amazonpay">Amazon Pay (Direct Bank Link)</option>
    									    <option value="googlepay">GooglePay Business</option>
    									    <option value="quintuspay">QuintusPay</option>
    									   
    									</select>
    								</div>
    								<div class="col-md-12 mb-2"> 
        								<label>Cashier Mobile Number</label> 
        								<input type="number" name="c_mobile" placeholder="Enter Mobile Number" class="form-control" onkeypress="if(this.value.length==10) return false;" required=""> 
    								</div>
                                    <div class="col-md-12 mb-2"> 
        								<label>&nbsp;</label> 
        								
        								<button type="submit" name="addmerchant" class="btn btn-primary btn-block">Add</button> 
        							</div>
        							
								</div>
							</div>
								   
								   
								</div>
								
							</form>	
        </div>
       
      </form>
      
    </div>
  </div>
</div>


  <!--confirm Modal -->
<div class="modal fade" id="confirmpverifymodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-confirm modal-lg" style="width: 40vw;">
    <div class="modal-content" id="confirmbox">
      <div class="modal-header">
          <div class="icon-box">
				<i class="fa fa-question fa-lg"></i>
				</div>						
				<h4 class="modal-title w-100">How you verify?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-6" style="border-right: 1px solid #d5d4d4;">
           
        <form action="paytm_verify" method="post">
            <input type="hidden" name="paytm_mobile" class="mp_pmobile">
            <input type="hidden" name="merchant_id" class="mp_mid">
            <button class="btn btn-info btn-xs mb-2 mt-1" name="Verify">Auto Verify</button>
        </form>
       <p style="margin: 15px auto;">1.Auto is verify through OTP</p>
            </div>
            <div class="col-md-6">
        <form action="send_paytmotp" method="post">
            <input type="hidden" name="paytm_mobile" class="mp_pmobile">
            <input type="hidden" name="merchant_id" class="mp_mid">
            <button class="btn btn-info btn-xs mb-2 mt-1" name="Verify">Manual Verify</button>
        </form>
        <p style="margin: 15px auto;">2.Manual is verify using manual details.</p>
            </div>
        </div>  
      </div>
     
    </div>
    
  </div>
</div>
  


	<div class="modal fade" id="upiidupdatemodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
     
        <div class="modal-header">
          <h5 class="modal-title">Manage UPI Id</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body">
          	<form method="POST" id="updateupiidform" class="mb-2">
			<input type="hidden" name="mname" id="mname"> 

			<div class="row" id="merchant">
			   
				<div class="col-md-6 mb-2"> 
					<label>UPI Id</label> 
					<input type="text" name="upi_id" id="upi_id" placeholder="Enter UPI Id" class="form-control" required=""> 
				</div>
                <div class="col-md-4 mb-2"> 
					<label>&nbsp;</label> 
					
					<button type="submit" name="updateupibtn" class="btn btn-primary btn-block">Update UPI</button> 
				</div>
			</div>
			
		</form>	
        </div>
       
      </form>
      
    </div>
  </div>
</div>
				
			

<script>
    let usid = <?= $userdata["id"] ?>;
</script>
    <!-- Essential javascripts for application to work-->
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/mainscript.js?<?=time()?>"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="js/plugins/pace.min.js"></script>
    <!-- Page specific javascripts-->
    <script src="assets/js/imb Pay.js"></script>
    <script src="assets/js/merchant.js?<?=time()?>"></script>
  
    <script>
    $(".addmerchant").click( () => {
       $("#addmerchantmodal").modal("show"); 
    });
    
    $(document).on('click','.verifypaytmbtn',function(){
        let mid = $(this).data("mid");
        let mobile = $(this).data("mobile");
        $(".mp_mid").val(mid);
        $(".mp_pmobile").val(mobile);
       $("#confirmpverifymodal").modal("show"); 
    });
    </script>
    
  </body>
</html>

