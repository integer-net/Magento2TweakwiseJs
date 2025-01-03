define([
    'jquery'
], function ($) {
    return function (configData) {
        window['twn-starter-config'].on['twn.add-to-favorites'] = function (event) {
            addToWishlist(event.data.itemno);
        };

        /**
         * @param {string} tweakwiseProductId
         */
        function addToWishlist (tweakwiseProductId) {
            const addToWishlistButtonSelector = '#twn-' + tweakwiseProductId + ' button';
            const productId = getProductId(tweakwiseProductId);
            const uenc = btoa(window.location.href);
            const formData = new FormData();
            formData.append('product', productId);
            formData.append('form_key', configData.formKey);
            formData.append('uenc', uenc);

            $(addToWishlistButtonSelector).prop('disabled', true);

            const postUrl = `${BASE_URL}wishlist/index/add/`;
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
                    $(addToWishlistButtonSelector).prop('disabled', false);
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
