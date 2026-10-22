function popup(status, title, msg) {
    Swal.fire({
        icon: status,
        title: title,
        text: msg,
    }).then(() => {
        location.reload();
    });
}

function popup_error(status, title, msg) {
    Swal.fire({
        icon: status,
        title: title,
        text: msg,
    }).then(() => {
        // location.reload();
    });
}

// Real error handler
function showRealError(xhr, defaultMsg = "Some internal error occurred, we are fixing it") {
    let msg = defaultMsg;
    try {
        if(xhr && xhr.responseText) {
            let res = JSON.parse(xhr.responseText);
            msg = res.msg || (res.api && res.api.message) || defaultMsg;
        }
    } catch(e) {}
    Swal.fire({
        icon: "error",
        title: "OOPS..!",
        text: msg,
    });
}

$(window).on('load', function() {
    // Hide the loader
    $('#loading_ajax').fadeOut();
});

// merchant aadhar verify js code here
$("#aadhar_verify").click(function() {
    $("#aadhar_modal").modal("show");
});

$("#aadhar_verify_form").submit(function(e) {
    e.preventDefault();
    $('#loading_ajax').fadeIn();
    $.ajax({
        url: 'backend/MerchantAadharVerify.php',
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(data, status) {
            $('#loading_ajax').fadeOut();
            let d = JSON.parse(data);
            if (d.status && d.redirect) {
                window.location.href = d.redirect;  // redirect
            } else {
                alert(d.msg || 'Failed');
            }
        },
        error: function(xhr) {
            $('#loading_ajax').fadeOut();
            showRealError(xhr);
        }
    });
});

$("#aadhar_otp_verifyForm").submit(function(e) {
    e.preventDefault();
    $('#loading_ajax').fadeIn();
    $.ajax({
        url: 'backend/MerchantAadharVerify.php',
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(data, status) {
            $('#loading_ajax').fadeOut();
            let rslt = JSON.parse(data);
            let aadhardata = rslt.aadhar_data;
            if (rslt.res_code == 200) {
                $("#user_aadhar_data").html(`<div class="user-image">
                    <img src="data:image/jpeg;base64,${aadhardata.image}" alt="User Image">
                </div>
                <div class="user-info" style="text-align: left;margin-left: 20px;">
                    <h6><strong>Name:  </strong>${aadhardata.name}</h6>
                    <h6>${aadhardata.so}</h6>
                    <p><strong>Gender:  </strong> ${aadhardata.gender}</p>
                    <p><strong>DOB:  </strong> ${aadhardata.dob}</p>
                    <p><strong>Address:  </strong>${aadhardata.address}</p>
                    <p><strong>Pincode:  </strong> ${aadhardata.pincode}</p>
                </div>`);
                $("#aadhar_modal").modal("hide");
                $("#aadhardetails_modal").modal("show");
            } else {
                popup_error('error', 'OopS!', rslt.msg);
            }
        },
        error: function(xhr) {
            $('#loading_ajax').fadeOut();
            showRealError(xhr);
        }
    });
});

$("#chargesetupform").submit(function(e) {
    e.preventDefault();
    $.ajax({
        url: 'Backend/ApiPartnersController.php',
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(data, status) {
            if (data == 1) {
                popup('success', 'Hurray!', 'Commission charge Plan Created Successfully!');
            } else {
                popup_error('error', 'OopS!', data);
            }
        },
        error: function(xhr) {
            showRealError(xhr);
        }
    });
});

$(document).on('change','.update2fa_btn',function() {
    var srno = $(this).data("srno");
    var status;
    if ($(this).is(':checked')) {
        status = 1;
    } else {
        status = 0;
    }
    $.ajax({
        url: 'backend/user_settings.php',
        type: 'POST',
        data: { status, srno, type:'two_factor_change' },
        success: function(response) {
            let rslt = JSON.parse(response);
            if(rslt.status == 1) {
                popup('success' , 'Hurray!' , rslt.msg);
            } else {
                popup_error('error' , 'OopS!' , rslt.msg);
            }
        },
        error: function(xhr) {
            showRealError(xhr);
        }
    });
});

$(document).on('change','.updatemerchantst_btn',function() {
    var mid = $(this).data("mid");
    var mtype = $(this).data("mtype");
    var status;
    if ($(this).is(':checked')) {
        status = 1;
    } else {
        status = 0;
    }
    $.ajax({
        url: 'backend/MerchantAuthController.php',
        type: 'POST',
        data: { status, mid, mtype, type:'updateMerchantSt' },
        success: function(response) {
            let rslt = JSON.parse(response);
            if(rslt.status == 1) {
                popup('success' , 'Hurray!' , rslt.msg);
            } else {
                popup_error('error' , 'OopS!' , rslt.msg);
            }
        },
        error: function(xhr) {
            showRealError(xhr);
        }
    });
});

$(document).on('change','.updateservice_btn',function() {
    var service = $(this).data("service");
    var srno = $(this).data("srno");
    var status;
    if ($(this).is(':checked')) {
        status = 1;
    } else {
        status = 0;
    }
    $.ajax({
        url: 'backend/pg_settings.php',
        type: 'POST',
        data: { status, srno, service, type:'updatepgservice' },
        success: function(response) {
            let rslt = JSON.parse(response);
            if(rslt.status == 1) {
                popup('success' , 'Hurray!' , rslt.msg);
            } else {
                popup_error('error' , 'OopS!' , rslt.msg);
            }
        },
        error: function(xhr) {
            showRealError(xhr);
        }
    });
});

// manage upi id js code
$(document).on('click','.updateupibtn',function(e) {
    e.preventDefault();
    $('#loading_ajax').fadeIn();
    let mno = $(this).data("mno");
    let mname = $(this).data("mname");
    $.ajax({
        url: 'backend/MerchantAuthController.php',
        type: 'POST',
        data: {mno, mname},
        success: function(data, status) {
            $('#loading_ajax').fadeOut();
            let rslt = JSON.parse(data);
            if (rslt.status == 1) {
                $("#mname").val(mname);
                $("#upi_id").val(rslt.upiid);
                $("#upiidupdatemodal").modal("show");
            } else {
                popup_error('error', 'OopS!', data);
            }
        },
        error: function(xhr) {
            $('#loading_ajax').fadeOut();
            showRealError(xhr);
        }
    });
});

$("#updateupiidform").submit(function(e) {
    e.preventDefault();
    $('#loading_ajax').fadeIn();
    $.ajax({
        url: 'backend/MerchantAuthController.php',
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(data, status) {
            $('#loading_ajax').fadeOut();
            let rslt = JSON.parse(data);
            if (rslt.status == 1) {
                popup('success', 'Hurray!', 'Upi Id Updated Successfully!');
            } else {
                popup_error('error', 'OopS!', rslt.msg);
            }
        },
        error: function(xhr) {
            $('#loading_ajax').fadeOut();
            showRealError(xhr);
        }
    });
});

// seen notification js code
$("#checknotifseen").on("click",function(e) {
    $('#loading_ajax').fadeIn();
    let nid = $(this).data("nid");
    $.ajax({
        url: 'backend/MerchantAuthController.php',
        type: 'POST',
        data: {nid, 'type':'seennotif'},
        success: function(data, status) {
            $('#loading_ajax').fadeOut();
            let rslt = JSON.parse(data);
            if (rslt.status == 200) {
                // window.location.href = 'notification';
            }
        }
    });
});
