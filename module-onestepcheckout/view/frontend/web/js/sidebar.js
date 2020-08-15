/**
 * Copyright 2016 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    // todo: consider remove after sidebar implemented
    $.widget('mage.awOscSidebar', {
        options: {
            offsetTop: 5 // todo: get this from margin-top property
        },

        /**
         * Initialize widget
         */
        _create: function () {
            this._bind();
        },

        /**
         * Event binding
         */
        _bind: function () {
            // todo
            if (!this._isMobile()){
                jQuery(window).scroll(function(){
                    var scrollPosition = jQuery(window).scrollTop();
                    var sidebarPosition = 107;

                    if (scrollPosition > sidebarPosition){
                        var difference = scrollPosition - sidebarPosition + 10;
                        if (difference < 50) difference = 0;
                        jQuery('#aw-onestep-sidebar').css('margin-top',difference + 'px');
                    }
                    else{
                        jQuery('#aw-onestep-sidebar').css('margin-top','0px');
                    }
                })
            }

        },

        _isMobile: function(){
            return ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent));
        },

        /**
         * Adjust element position
         */
        _adjust: function () {
            if ($(window).scrollTop() > this.options.offsetTop) {
                this.element.css({
                    'position': 'fixed',
                    'top': this.options.offsetTop
                });
            } else {
                this.element.css('position', 'static');
            }
        }
    });

    return $.mage.awOscSidebar;
});
