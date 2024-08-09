/**
 * lc-js-events.js - Micro vanilla javascript (ES6) class making swipe events easy!
 * Version: v1.0
 * Author: Luca Montanari (LCweb)
 * Website: https://lcweb.it
 * Licensed under the MIT license
 */


(function() {
	"use strict";

    /* prevent multiple script inits */ 
    if(typeof(window.lc_swiper) == 'function') {
        return true;
    }
    
    
    
    /* plugin class */
    window.lc_swiper = function(attachTo, callback) {
        if(!attachTo) {
            return console.error('You must provide a valid selector or DOM object as first argument');
        }
        if(typeof(callback) != 'function') {
            return console.error('please use a valid callback');    
        }

        this.$elements  = [];
        this.uniqid     = Math.random().toString(36).substr(2, 9);
        
        
        
        /* get elements to attach event to */
        this.get_elems = function(selector) {
            if(typeof(selector) != 'string') {
                return (selector instanceof Element) ? [selector] : Object.values(selector);   
            }

            // clean problematic selectors
            (selector.match(/(#[0-9][^\s:,]*)/g) || []).forEach(function(n) {
                selector = selector.replace(n, '[id="' + n.replace("#", "") + '"]');
            });

            return document.querySelectorAll(selector);
        };
        
        
        
        /* initialize touch tracking */
        this.init = function() {
            const $this = this;
            this.$elements = this.get_elems(attachTo);   
            
            this.$elements.forEach(function($el) {
                
                // cache trick for easy destruction
                if(typeof($el.lcswiper_cb) == 'undefined') {
                    $el.lcswiper_cb = {};        
                }
                $el.lcswiper_cb[ $this.uniqid ] = callback;

                // track first touch
                $el.addEventListener('touchstart', (e) => {
                    $el.lcswiper_xDown = e.touches[0].clientX;
                    $el.lcswiper_yDown = e.touches[0].clientY;
                });   
                
                // track touch end
                $el.addEventListener('touchend', (e) => {
                    $this.handleTouchDiff($el, e);
                });
            });
        };
        
        
        
        /* handle touchend values and eventually triggers callback passing directions object and $el */
        this.handleTouchDiff = function($el, e) {
            if(
                typeof($el.lcswiper_xDown) == 'undefined' || !$el.lcswiper_xDown || 
                typeof($el.lcswiper_yDown) == 'undefined' || !$el.lcswiper_yDown ||
                typeof($el.lcswiper_cb[ this.uniqid ]) == 'undefined'
            ) {
                return;
            }

            const xUp = e.changedTouches[0].clientX,
                  yUp = e.changedTouches[0].clientY,
                  
                  xDiff = parseInt($el.lcswiper_xDown - xUp, 10),
                  yDiff = parseInt($el.lcswiper_yDown - yUp, 10);

            if(Math.abs(xDiff) !== 0 || Math.abs(yDiff) !== 0) {
                const to_return = {
                    up      : (yDiff > 0) ? yDiff : 0,
                    right   : (xDiff < 0) ? Math.abs(xDiff) : 0,
                    down    : (yDiff < 0) ? Math.abs(yDiff) : 0,
                    left    : (xDiff > 0) ? xDiff : 0,
                };
                $el.lcswiper_cb[ this.uniqid ].call(this, to_return, $el);
            }
            
            // reset
            $el.lcswiper_xDown = 0; 
            $el.lcswiper_yDown = 0; 
        };
        
        
        
        /* destroy public method to stop tracking event with specific callback */
        this.destroy = function(spec_elems) {
            const $this = this,
                  $to_destroy = (!spec_elems) ? this.$elements : this.get_elems(spec_elems);
            
            $to_destroy.forEach(function($el) {
                if(typeof($el.lcswiper_cb) != 'undefined' && typeof($el.lcswiper_cb[ $this.uniqid ]) != 'undefined') {
                    delete $el.lcswiper_cb[ $this.uniqid ];
                }
            });
        };
            
        
        
        // init and return class
        this.init();
        return this;
    };
})();