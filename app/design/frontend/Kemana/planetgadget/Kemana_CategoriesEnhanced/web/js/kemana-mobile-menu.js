define([
   'jquery',
   'catutility'
],function($,catutility){

    $.widget('kemana.mobileMenu',{

        options:{},

        _create:function(){
          let self = this;
          if($(document.body).width() < 1024){
              self._setupMenu();
              self._bindevent();
          }
        },

        _bindevent:function(){
            let self = this;
            $('.nav-toggle').on('click',function(){
               $('body').toggleClass('nav-before-open nav-open');
            });
        },

        _setupMenu:function(){
            let self = this;
            let levelOneBackButton = '<li class="level0 back-button">back</li>';
            let levelTwoBackButton = '<li class="level1 back-button">back</li>';
            let levelThreeBackButton = '<li class="level1 back-button">back</li>';
            self.element.find('.default-menu').hide();

            self.element.find('.level0.parent').children('a').on('click',function(e){
                e.preventDefault();
                self.element.find('.level-top').children('.level-top').hide();
                self.element.find('.parent').children('.submenu').hide();
                self.element.find('.level1 .megamenu-wrapper').hide();
                if($(this).siblings().find('.level0.submenu').children('.back-button').length == 0 ) {
                    $(this).siblings().find('.level0.submenu').prepend(levelOneBackButton);
                }
                if($(this).siblings('.default-menu').children('.back-button').length == 0){
                    $(this).siblings('.default-menu').prepend(levelOneBackButton);
                }
                $(this).hide();
                $(this).siblings('.megamenu-wrapper').show();
                $(this).siblings('.default-menu').show();
                $('.mobile-menu-inner').hide();

            });

            self.element.find('.level1.parent').children('a').on('click',function(e){
                e.preventDefault();
                $(this).parent('.level1').siblings().hide();
                $(this).hide();
                if($(this).siblings().children('.back-button').length == 0 ) {
                    $(this).siblings().prepend(levelTwoBackButton);
                }
                $(this).siblings('.submenu').show();
            });

            self.element.find('.level2.parent').children('a').on('click',function(e){
                e.preventDefault();
                $(this).parent('.level2').siblings().hide();
                $(this).hide();
                if($(this).siblings().children('.back-button').length == 0 ) {
                    $(this).siblings().prepend(levelThreeBackButton);
                }
                $(this).siblings('.submenu').show();
            });

            self.element.find('.topmenu').on('click','.back-button',function(){
                if($(this).hasClass('level2')) {
                    $(this).parent().hide();
                    $(this).parent().siblings().show();
                    $(this).parent().parent().siblings().show();
                }
                else if($(this).hasClass('level1')) {
                    $(this).parent().hide();
                    $(this).parent().siblings().show();
                    $(this).parent().parent().siblings().show();
                }else{
                    $(this).parents('.megamenu-wrapper').hide();
                    $(this).parent('.default-menu').hide();
                    self.element.find('.level-top').children('.level-top').show();
                    $('.mobile-menu-inner').show();
                }
            });
        },

    });

    return $.kemana.mobileMenu;
});
