define([
    'jquery'
], function ($) {
    return function (configData) {
        const options = {
            minicartSelector: '[data-block="minicart"]',
            messagesSelector: '[data-placeholder="messages"]'
        };

        window['twn-starter-config'].on['twn.add-to-cart'] = function (event) {
            addToCart(event.data.itemno);
        };

        /**
         * @param {string} tweakwiseProductId
         */
        function addToCart (tweakwiseProductId) {
            const addToCartButtonSelector = '#twn-' + tweakwiseProductId + ' button';
            const productId = getProductId(tweakwiseProductId);
            const uenc = btoa(window.location.href);
            const formData = new FormData();
            formData.append('product', productId);
            formData.append('form_key', configData.formKey);
            formData.append('uenc', uenc);

            $(addToCartButtonSelector).prop('disabled', true);
            $(options.minicartSelector).trigger('contentLoading');

            const postUrl = `${BASE_URL}checkout/cart/add/uenc/` + uenc + '/product/' + productId + '/';
            $.ajax({
                url: postUrl,
                data: formData,
                type: 'post',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,

                success: function (res) {
                    if (res.backUrl) {
                        window.location.href = res.backUrl;
                        return;
                    }
                },

                complete: function (res) {
                    $(addToCartButtonSelector).prop('disabled', false);
                    if (res.state() === 'rejected') {
                        location.reload();
                    }
                }
            });
        }

        /**
         * @param {string} tweakwiseProductId
         * @returns {string}
         */
        function getProductId(tweakwiseProductId) {
            return tweakwiseProductId.replace('1' + configData.storeId.padStart(4,0), '');
        }
    }
});
