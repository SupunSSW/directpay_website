var stepper = new Stepper($('.bs-stepper')[0], {
    linear: true,
    animation: true
});
//stepper.next(); //temp
//readyPayment();
var load = document.getElementById('load');
var poBtnLoad = document.getElementById('poBtnLoad');
var cardContainer = document.getElementById("card_container");
load.style.display = 'none';
// btnSecondaryCard.style.display = 'none';
poBtnLoad.style.display = 'none';

$('#consent').change(function () {
    if ($(this).is(":checked")) {
        statusChanger(9);
        readyPayment();
        $("#consent").attr('readonly', 'readonly');
        $("#consent").attr('disabled', 'disabled');
        cardContainer.scrollIntoView();
    }
});

window.validatePolicy = function validatePolicy() {
    var policeNo = $('#InputPoNo').val();
    $(".btn").attr("disabled", true);
    poBtnLoad.style.display = 'block';
    statusChanger(6);
    if (policeNo) {
        $.ajax({
            url: _pac,
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                poNo: policeNo,
            },
            success: function (e) {
                //console.log(e);
                poBtnLoad.style.display = 'none';
                $(".btn").attr("disabled", false);
                if (e.status === 200) {
                    statusChanger(7);
                    stepper.next();
                } else {
                    statusChanger(8);
                    Swal.fire(
                        e.data.error,
                        '',
                        'warning'
                    )
                }
            },
            error: function (e) {
                //console.log("payment_update_load!", e.responseJSON)
                statusChanger(8);
                poBtnLoad.style.display = 'none';
                $(".btn").attr("disabled", false);
            }
        });
    } else {
        poBtnLoad.style.display = 'none';
        $(".btn").attr("disabled", false);
        Swal.fire(
            'Policy no mandatory!',
            '',
            'warning'
        )
    }
}

window.readyPayment = function readyPayment() {
    var ref = _dpjs._ref;
    var dataAmount = _dpjs._prAm;
    var recurrAmount = _dpjs._rcAm;
    var paymentType = _dpjs._isR;
    var mId = _dpjs._dpM;
    var debug = _dpjs._deb;
    var doFirst = _dpjs._doF;
    stepper.next();

    DirectPayCardPayment.init({
        container: "card_container", //<div id="card_container"></div>
        merchantId: mId,
        amount: dataAmount,
        refCode: ref, //unique ref code form merchant
        currency: 'LKR',
        type: paymentType ? 'RECURRING' : 'ONE_TIME_PAYMENT',
        recurring: {
            startPaymentDate: _dpjs._pas,
            lastPaymentDate: '',
            interval: _dpjs._intr,
            isRetry: true,
            retryAttempts: 0,
            doFirstPayment: doFirst,
            recurringAmount: recurrAmount
        },
        customerEmail: _dpjs._cuEm,
        customerMobile: _dpjs._cuMo,
        debug: debug === "1",
        responseCallback: responseCallback,
        errorCallback: errorCallback,
        logo: '',
        description: 'AIA Insurence',
        apiKey: 'bfhjfbejbfhj324b3b43k2j4b324j32bk4j3bjhbdfdj'
    });
};

//response callback.
window.responseCallback = function responseCallback(result) {
    //console.log("successCallback-Client", result);
    cardContainer.scrollIntoView();
    load.style.display = 'block';
    if (result.status === 200) {
        // btnSecondaryCard.style.display = 'block';
        var shcId = result.data.scheduledId;
        var paymentType = _dpjs._isR;
        var doFirst = _dpjs._doF;

        if (result.data.status === 'SUCCESS' && paymentType) {
            $("#consentDiv").fadeOut();
            $("#btnSecondaryCard").append('<button type="button" onclick="addCard(' + shcId + ', _dpjs._cuMo)" class="btn btn-primary"><i class="fa fa-credit-card"></i> Add secondary card</button>');

            if(doFirst == 'false'){
                $("#succssMsg").fadeIn();
            }
        }

        $.ajax({
            url: _py,
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                data: result.data,
            },
            success: function (e) {
                //console.log(e);
                load.style.display = 'none';
            },
            error: function (e) {
                load.style.display = 'none';
                console.log("payment_update_load!", e.responseJSON)
            }
        });
    } else {
        btnSecondaryCard.style.display = 'block';
    }
}

window.addCard = function addCard(schId, mob) {
    var url = _adc;
    url += '?_s=' + schId + '&_m=' + encodeURIComponent('+' + mob);
    window.location.href = url;
}

//error callback
window.errorCallback = function errorCallback(result) {
    //console.log("successCallback-Client", result);
    cardContainer.scrollIntoView();
    var linkId = _dpjs._itemId;
    $.ajax({
        url: _fsRes,
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            linkId: linkId,
            reason: result
        },
        success: function (e) {
            //console.log(e);
        },
        error: function (e) {
            //console.log("payment_update_load!", e.responseJSON)
        }
    });
}

window.statusChanger = function statusChanger(statusId) {
    var linkId = _dpjs._itemId;
    $.ajax({
        url: _stup,
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            linkId: linkId,
            statusId: statusId
        },
        success: function (e) {
            //console.log(e);
        },
        error: function (e) {
            //console.log("payment_update_load!", e.responseJSON)
        }
    });
}
