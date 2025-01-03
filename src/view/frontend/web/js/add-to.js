define([
    'jquery'
], function ($) {
    return function (configData) {
        const options = {
            minicartSelector: '[data-block="minicart"]',
            messagesSelector: '[data-placeholder="messages"]',
            tweakwiseProductId: '',
            productId: ''
        };

        window['twn-starter-config'].on['twn.add-to-cart'] = function (event) {
            setProductData(event.data.itemno);
            addToCart();
        };

        window['twn-starter-config'].on['twn.add-to-favorites'] = function (event) {
            setProductData(event.data.itemno);
            addToWishlist();
        };

        /**
         * Function to add product to the cart
         */
        function addToCart () {
            $(options.minicartSelector).trigger('contentLoading');
            addTo(getAddToCartUrl());
        }

        /**
         * Function to add product to the wishlist
         */
        function addToWishlist () {
            addTo(getAddToWishlistUrl());
        }

        /**
         * @param {string} url
         */
        function addTo (url) {
            const addToButtonSelector = getAddToButtonSelector();
            $(addToButtonSelector).prop('disabled', true);

            $.ajax({
                url: url,
                data: getFormData(),
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
                    $(addToButtonSelector).prop('disabled', false);
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

        /**
         * @returns {string}
         */
        function getAddToButtonSelector() {
            return '#twn-' + options.tweakwiseProductId + ' button';
        }

        /**
         * @returns {FormData}
         */
        function getFormData() {
            const formData = new FormData();
            formData.append('product', options.productId);
            formData.append('form_key', configData.formKey);
            formData.append('uenc', btoa(window.location.href));

            return formData;
        }

        /**
         * @returns {string}
         */
        function getAddToCartUrl() {
            return `${BASE_URL}checkout/cart/add/uenc/` + btoa(window.location.href) + '/product/' + options.productId + '/';
        }

        /**
         * @returns {string}
         */
        function getAddToWishlistUrl() {
            return `${BASE_URL}wishlist/index/add/`;
        }

        /**
         * @param {string} tweakwiseProductId
         */
        function setProductData(tweakwiseProductId) {
            options.tweakwiseProductId = tweakwiseProductId;
            options.productId = getProductId(tweakwiseProductId);
        }
    }
});
