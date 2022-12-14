define([
    'jquery',
    'utility',
    'slick',
    'mage/accordion',
    'domReady!'
], function ($, utility, slick) {
    /**
     * add comment here for you function
     * 1.Mobile footer accordion function
     * 2.currency dropdown
     * 3.PLP title align
     * 4.PLP mobile button alignment
     * 5.PLP mobile filter Open
     * 6.PDP hover effect
     * 7.Cart Mobile back to shopping and clear button position changed
     * 8.Cart update button position changed (default:disabled)
     * 9.Cart Discount Code Container position changed
     * 10.PDP Specification content set
     * 11.PDP No review Section set
     * 12.My account mobile view navigation list
     * 13.My account mobile view order drop down
     * 14.My account action toolbar alignment
     * 15.Hide Invoice tab when order is Pending Status
     * 16.Home page recently viewed products mobile slider
     * 17.minicart onHover Option
     * 18.Product Widget slider
     * 19.Login password show/hide
     */

    /**
     * 1.Mobile footer accordion function
     */
    if(utility.isMobile()  || utility.isTablet) {
        $('.menu-section').accordion({
            header: 'p',
            content: 'ul',
            active: false,
            collapsible: true
        });
    }

    /**
     * 2.currency dropdown
     */
    $('.language-currency-block').on('click', function () {
        $('.language-currency-block').toggleClass('open');
        $('.language-currency-dropdown').slideToggle();
    });

    /**
     * 3.PLP title align
     */
    function setPLPTitle() {
        let content = $('.catalog-category-view .page-title-wrapper').html();
        let content2 = $('.catalogsearch-result-index .page-title-wrapper').html();
        if(!$('.toolbar-products .page-title span').text()){
            $($('.toolbar-products')[0]).prepend(content, content2);
        }
    }

    setPLPTitle();

    /**
     * 4.PLP mobile button alignment
     */
    function setPLPButton() {
        if (utility.isMobile()) {
            let content = $('<div class="mobile-filer">Filter</div>')
            $(content).insertBefore('.toolbar-sorter.sorter');
        }
    }

    setPLPButton();

    $('body').on('submitFilterCompleted' , setPLPTitle);

    /**
     * 5.PLP mobile filter Open
     */
    $('#layer-product-list').on('click', '.mobile-filer', function () {
        $('body').addClass('filter-active');
        $('#layered-filter-block').addClass('active');
    });

    /**
     * 6.PDP hover effect
     */
    $(document).on('mouseover', '.fotorama__nav__frame', function () {
        var $fotoramaDiv = $(this).parents('.fotorama'),
            fotoramaApi = $fotoramaDiv.data('fotorama');
        fotoramaApi.show({
            index: $('.fotorama__nav__frame', $fotoramaDiv).index(this)
        });
    });

    /**
     * 7.Cart Mobile back to shopping and clear button position changed
     */
    $(window).resize(function () {
        if ($(document.body).width() < 767) {
            $(".main.actions .mobile-cart-buttons").insertAfter($(".cart-container .form-cart"));
        } else {
            $(".cart-container .mobile-cart-buttons").insertBefore($(".main.actions .action.update"));
        }
    })

    /**
     * 8.Cart update button position changed (default:disabled)
     */
    function moveClearButton() {
        $(function () {
            if ($(document.body).width() > 767) $('.mobile-cart-buttons').after($('.cart.actions .action.clear'));
        });
    }

    moveClearButton();

    /**
     * 9.Cart Discount Code Container position changed
     */
    function discountCode() {
        if ($('.checkout-cart-index .cart-container .cart-summary').length) {
            $(".checkout-cart-index .cart-container .cart-summary .discount").insertAfter($(".checkout-cart-index .cart-container .cart-summary .checkout.checkout-methods-items"));
        }
    }

    discountCode();

    /**
     * 10.PDP Specification content set
     */
    // function specificationContent() {
    //     $('.product.attribute.description').append('<div class="specification"></div>')
    //     let title = $('#tab-label-additional').html();
    //     let content = $('#additional').html();
    //     $('#tab-label-additional').hide();
    //     $('#additional').hide();
    //     $('.specification').append(title).append(content);
    // }
    //
    // specificationContent();

    /**
     * 11.PDP No review Section set
     */
    function setNoReviewSection() {
        if ($('.product-reviews-summary').hasClass('empty')) {
            $('.product-reviews-summary').prepend('<div class="rating-summary">' +
                '<span class="label">' +
                '<span>Rating:</span>' +
                '</span>' +
                '<div class="rating-result" title="">' +
                '<span style="width:0%">' +
                '<span>' +
                '<span itemprop="ratingValue">60</span>% of ' +
                '<span itemprop="bestRating">100</span>' +
                '</span>' +
                '</span>' +
                '</div>' +
                '</div>');
        }
    }

    setNoReviewSection();

    /**
     * 12.My account mobile view navigation list
     */
    function setMyAccountMobileNav() {
        if (utility.isMobile()) {
            let currentText = $('.sidebar-main').find('.current').text();
            let actionContent = '<li class="nav item action"><strong>' + currentText + '</strong></li>';
            $('.sidebar-main').find('.items').prepend(actionContent);
            $('.sidebar-main').find('.item').not('.action').hide();
            $($('.sidebar-main')[0]).insertBefore('.page-title-wrapper');
        }
    }

    $('.sidebar-main').on('click', '.action', function () {
        if (utility.isMobile()) {
            $(this).siblings().toggle();
            $(this).toggleClass('active');
        }
    });

    if($('body').hasClass("account")) {
        setMyAccountMobileNav();
    }

    /**
     * 13.My account mobile view order drop down
     */
    function setMyOrderNavigation() {
        if (utility.isMobile()) {
            let text = $('.order-links .current strong').text();
            $('.order-links').prepend('<li class="nav item action"><strong>' + text + '</strong></li>');
        }

    }

    setMyOrderNavigation();

    $('.order-links').on('click', '.action', function () {
        $(this).siblings().toggle();
        $(this).toggleClass('active');
    });

    /**
     * 14.My account action toolbar alignment
     */
    function setActionToolbarAlign() {
        if (utility.isMobile()) {
            let content = $('.order-actions-toolbar')[0];
            $(content).insertBefore('.block-order-details-view');
        }
    }

    setActionToolbarAlign();

    /**
     * 15.Hide Invoice tab when order is Pending Status
     */
    if ($('.order-status.pending').length) {
        $('.nav.item a[href*=sales\\/order\\/invoice]').parent().hide();
    }

    /**
     * 16.Home page recently viewed products mobile slider
     */
    if (utility.isMobile()) {
        setTimeout(function () {
                $('.recently-viewed-products-container .product-items').slick({
                    arrows: false,
                    dots: false,
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    centerMode: false,
                    centerPadding: '4%'
                });
                $('.slick-list').css('padding', '0 4% 0 0');

            },
            3000);
    }

    /**
     * 17.minicart onHover Option
     * @type {string}
     */
    let action = 'a.action.showcart';
    let wrapper = 'div.minicart-wrapper';
    let dialog = 'div.mage-dropdown-dialog';

    $(action).mouseenter(function () {
        showMinicart();
    });
    $(wrapper).mouseleave(function (e) {
        hideMinicart();
    });

    var showMinicart = function () {
        if (!$(wrapper).hasClass('active')) {
            $(wrapper).addClass('active');
        }
        if (!$(action).hasClass('active')) {
            $(action).addClass('active');
        }
        $(dialog).show();
    };
    var hideMinicart = function () {
        if ($(wrapper).hasClass('active')) {
            $(wrapper).removeClass('active');
        }
        if ($(action).hasClass('active')) {
            $(action).removeClass('active');
        }
        $(dialog).hide();
    }

    if(utility.isMobile()){
        $('.mobile-sorter-option').show();
    }

    /**
     * 18.Product Widget slider
     */
    $('.block .products-grid .product-items').slick({
        infinite: false,
        slidesToShow: 6,
        slidesToScroll: 1,
        arrows: true,
        dots: false,
        swipeToSlide: true,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2,
                    arrows: false
                }
            },
            {
                breakpoint: 768,
                settings: {
                    arrows: false,
                    dots: false,
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 300,
                settings: "unslick" // destroys slick
            }]
    });

    // $(".block .products-grid .product-items").slick({
    //     /**
    //      * normal options...
    //      */
    //     infinite: true,
    //     slidesToShow: 6,
    //     rows: 1,
    //     slidesToScroll: 1,
    //     arrows: true,
    //     centerMode: false,
    //     responsive: [
    //         {
    //             breakpoint: 1023,
    //             settings: {
    //                 slidesToShow: 6
    //             }
    //         },
    //         {
    //             breakpoint: 767,
    //             settings: {
    //                 arrows: false,
    //                 dots: false,
    //                 slidesToShow: 2,
    //                 rows: 2,
    //                 slidesToScroll: 1,
    //                 centerMode: false,
    //                 centerPadding: '0 0 25.5%'
    //             }
    //         },
    //         {
    //             breakpoint: 300,
    //             settings: "unslick"
    //         }]
    // })

    /**
     * 19.Login password show/hide
     */
    const $doc = $(document);
    $doc.on('click', '.fa-eye-slash', function () {
        $('.fa-eye').show();
        $('.fa-eye-slash').hide();
        $('#social_login_pass').attr('type','text');
    });
    $doc.on('click', '.fa-eye', function () {
        $('.fa-eye').hide();
        $('.fa-eye-slash').show();
        $('#social_login_pass').attr('type','password');
    });

    /**
     * 20.Category Widget slider
     */
    $(".block .category-top-grid .category-top-items").slick({
        /**
         * normal options...
         */
        infinite: true,
        slidesToShow: 4,
        rows: 2,
        slidesToScroll: 1,
        arrows: true,
        centerMode: false,
        responsive: [
            {
                breakpoint: 1023,
                settings: {
                    slidesToShow: 2,
                    arrows: false
                }
            },
            {
                breakpoint: 767,
                settings: "unslick"
            },
            {
                breakpoint: 300,
                settings: "unslick"
            }]
    });

    /**
     * 21. Dropdown Overlay
     */

    $("#menu-main-menu > .menu-parent-item").hover(function(){
        $('.modals-overlay').fadeToggle();
    });

    /**
     * 22. wrap the review title
     */

    $( ".product-section-title" ).wrapInner( "<span></span>");


    /**
     * 22. move button in my account wishlist
     */

    if (utility.isMobile()) {
        $('.page-title-wrapper .tocart').prependTo( $('.actions-toolbar') );
    }

    /**
     * 23. overlay background when hovaring main menu and search popup is open
     */

    $('.navbar.navbar-default.navigation').mouseover(function () {
        if($('.category-item.level-top').hasClass('menu-active')){
            $('body').addClass('active');
        }
        if(!$('.category-item.level-top').hasClass('menu-active')){
            $('body').removeClass('active');
        }
    });

    $("#search").focusin(
        function(){
            $('body').addClass('active2');
        }).focusout(
        function(){
            $('body').removeClass('active2');
        });

    /**
     * 24. mega menu hide 2nd column if there is no 2nd level menu
     */

    $('.level1.category-item > a').mouseover(function () {
        if(!$(this).parent().find(".megamenu-inner").length > 0) {
            $(".megamenu-wrapper.tabs-menu.vertical").attr('style', 'width: 310px !important');
        } else {
            $(".megamenu-wrapper.tabs-menu.vertical").attr('style', 'width: unset');
        }
    });

    /**
     * 25. PLP sorter dropdown on/off state
     */

    $(".qty-selector").click(function() {
        if(!$(".sorter-options.qty-selector-dropdown:visible").length > 0) {
            $(".qty-selector").addClass("dropdown-active");
        } else {
            $(".qty-selector").removeClass("dropdown-active");
        }
    });

    /**
     * 26. move Page Title in News Details page
     */

    $('.columns .mp-blog-view .post-view').prepend( $('.page-title-wrapper') );

    /**
     * 27. move header search in mobile
     */

    if (utility.isMobile() || utility.isTablet()) {
        $('.header-left .logo').before( $('.header-right .top-link') );
        $('.header-left .logo').appendTo( $('.header') );
        $('.header .block-search').prependTo( $('.header .header-right') );
    }

    /**
     * 28. add main-item class to mobile compare
     */

    if (utility.isMobile()) {
        $( "ul li.compare" ).addClass( "main-item" );
    }

    /**
    * 29. mobile sub menu not opaning fix
    */

    let globalIsMobileMenuOpen = false;

    // if (utility.isMobile()) {
    if ($(document.body).width() < 1024) {
        $(".action.nav-toggle").click(function () {
            if (!globalIsMobileMenuOpen) {
                $(".megamenu-wrapper").css("display", "none");
                globalIsMobileMenuOpen = true;
            }
        });
    }

    /**
     * 30. Checkout Select location popup BG color change
     */

    $doc.on('click', '.select-location input', function () {
        $('.select-location input').parent().closest('.row.location').removeClass('check');

        if( $('.select-location input').is(':checked') ){
            $(this).parent().closest('.row.location').addClass('check');
        } else {
            $(this).parent().closest('.row.location').removeClass('check');
        }
    });

    /**
     * 31. corporate-order page brands show hide
     */

    $('#corporate-brand-container li:gt(17)').hide();
    $('#show-more').click(function() {
        $('#corporate-brand-container li:gt(17)').show();
        $(this).parent('.primary').addClass('show-items');
        $(this).parent('.primary').removeClass('hide-items');
    });

    $('#show-less').click(function() {
        $('#corporate-brand-container li:gt(17)').hide();
        $(this).parent('.primary').removeClass('show-items');
        $(this).parent('.primary').addClass('hide-items');
    });

    if (utility.isMobile()) {
        $('#corporate-brand-container li:gt(6)').hide();

        $('#show-more').click(function() {
            $('#corporate-brand-container li:gt(6)').show();
        });

        $('#show-less').click(function() {
            $('#corporate-brand-container li:gt(6)').hide();
        });

    }


});
