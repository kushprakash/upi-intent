
const canMakePaymentCache = 'canMakePaymentCache';
    /** Launches payment request flow when user taps on buy button. */
    function onBuyClicked() {
      if (!window.PaymentRequest) {
        console.log('Web payments are not supported in this browser.');
        return;
      }
     //insahallah20141@ybl
      // Create supported payment method.
      const supportedInstruments = [  
        {
          supportedMethods: ['https://tez.google.com/pay'],
          data: {
            pa: upiid,
            pn: mname,
            tr: txnid,  // Your custom transaction reference ID
            url: redirect_url,
            mc: '4722', //Your merchant category code
            tn: 'Purchase in Merchant',
          },
        }
      ];

      // Create order detail data.
      const details = {
        total: {
          label: 'Total',
          amount: {
            currency: 'INR',
            value: amt, // sample amount
          },
        },
        displayItems: [{
          label: 'Original Amount',
          amount: {
            currency: 'INR',
            value: amt,
          },
        }],
      };

      const options = {
        requestShipping: true,
        requestPayerName: true,
        requestPayerPhone: true,
        requestPayerEmail: true,
        requestPayerPassWord: true,
        shippingType: 'shipping',
      };

      // Create payment request object.
      let request = null;
      try {
        request = new PaymentRequest(supportedInstruments, details);
      } catch (e) {
        console.log('Payment Request Error: ' + e.message);
        return;
      }
      if (!request) {
        console.log('Web payments are not supported in this browser.');
        return;
      }

      var canMakePaymentPromise = checkCanMakePayment(request);
      canMakePaymentPromise
        .then((result) => {
          showPaymentUI(request, result);
        })
        .catch((err) => {
          console.log('Error calling checkCanMakePayment: ' + err);
        });
    }

    function checkCanMakePayment(request) {
      // Checks canMakePayment cache, and use the cache result if it exists.
      if (sessionStorage.hasOwnProperty(canMakePaymentCache)) {
        return Promise.resolve(JSON.parse(sessionStorage[canMakePaymentCache]));
      }

      // If canMakePayment() isn't available, default to assuming that the method is
      // supported.
      var canMakePaymentPromise = Promise.resolve(true);

      // Feature detect canMakePayment().
      if (request.canMakePayment) {
        canMakePaymentPromise = request.canMakePayment();
      }

      return canMakePaymentPromise
        .then((result) => {
          // Store the result in cache for future usage.
          sessionStorage[canMakePaymentCache] = result;
          return result;
        })
        .catch((err) => {
          console.log('Error calling canMakePayment: ' + err);
        });
    }
    
    function showPaymentUI(request, canMakePayment) {
      if (!canMakePayment) {
        handleNotReadyToPay();
        return;
      }

      // Set payment timeout.
      let paymentTimeout = window.setTimeout(function () {
        window.clearTimeout(paymentTimeout);
        request.abort()
          .then(function () {
            console.log('Payment timed out after 20 minutes.');
          })
          .catch(function () {
            console.log('Unable to abort, user is in the process of paying.');
          });
      }, 5 * 60 * 1000); /* 20 minutes */


      request.show()
        .then(function (instrument) {

          window.clearTimeout(paymentTimeout);
          processResponse(instrument); // Handle response from browser.
        })
        .catch(function (err) {
          console.log(err);
        });
    }


    /** Handle Google Pay not ready to pay case. */
    function handleNotReadyToPay() {
      alert('Google Pay is not ready to pay.');
    }
    
    