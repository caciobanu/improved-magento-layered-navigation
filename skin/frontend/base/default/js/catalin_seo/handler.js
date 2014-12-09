var CatalinSeoHandler = {
    listenersBinded: false,
    isAjaxEnabled: false,
    priceSlider: {
        urlTemplate: '',
        minPrice: 0,
        maxPrice: 0,
        currentMinPrice: 0,
        currentMaxPrice: 0
    },
    handlePriceEvent: function (val) {
        var self = this;
        if (val) {
            var url = self.priceSlider.urlTemplate.replace('__PRICE_VALUE__', val);
            if (self.isAjaxEnabled) {
                self.handleEvent(url);
            } else {
                window.location.href = url;
            }
        }
    },
    handleEvent: function (el, event) {
        var url, fullUrl;
        var self = this;
        if (typeof el === 'string') {
            url = el;
        } else if (el.tagName.toLowerCase() === 'a') {
            url = $(el).readAttribute('href');
        } else if (el.tagName.toLowerCase() === 'select') {
            url = $(el).getValue();
        }

        // Add this to query string for full page caching systems
        if (url.indexOf('?') != -1) {
            fullUrl = url + '&isLayerAjax=1';
        } else {
            fullUrl = url + '?isLayerAjax=1';
        }

        $('loading').show();
        $('ajax-errors').hide();

        self.pushState(null, url, false);

        new Ajax.Request(fullUrl, {
            method: 'get',
            onSuccess: function (transport) {
                if (transport.responseJSON) {
                    $('catalog-listing').update(transport.responseJSON.listing);
                    $('layered-navigation').update(transport.responseJSON.layer);
                    self.pushState({
                        listing: transport.responseJSON.listing,
                        layer: transport.responseJSON.layer
                    }, url, true);
                    self.ajaxListener();
                    self.toggleContent();
                    self.alignProductGridActions();
                    self.blockCollapsing();
                } else {
                    $('ajax-errors').show();
                }
                $('loading').hide();
            }
        });

        if (event) {
            event.preventDefault();
        }
    },
    pushState: function (data, link, replace) {
        var History = window.History;
        if (!History.enabled) {
            return false;
        }

        if (replace) {
            History.replaceState(data, document.title, link);
        } else {
            History.pushState(data, document.title, link);
        }
    },
    ajaxListener: function () {
        var self = this;
        var els;
        els = $$('div.pager a').concat(
            $$('div.sorter a'),
            $$('div.pager select'),
            $$('div.sorter select'),
            $$('div.block-layered-nav a')
        );
        els.each(function (el) {
            if (el.tagName.toLowerCase() === 'a') {
                $(el).observe('click', function (event) {
                    self.handleEvent(this, event);
                });
            } else if (el.tagName.toLowerCase() === 'select') {
                $(el).setAttribute('onchange', '');
                $(el).observe('change', function (event) {
                    self.handleEvent(this, event);
                });
            }
        });
    },
    bindPriceSlider: function () {
        var self = this;
        new Control.Slider([$('price-min'), $('price-max')], 'price-range', {
                range: $R(self.priceSlider.minPrice, self.priceSlider.maxPrice),
                sliderValue: [self.priceSlider.currentMinPrice, self.priceSlider.currentMaxPrice],
                values: $R(self.priceSlider.minPrice, self.priceSlider.maxPrice),

                restricted: true,
                onChange: function (val) {
                    if (val[0] != self.priceSlider.currentMinPrice || val[1] != self.priceSlider.currentMaxPrice) {
                        $('button-price-slider').value = val.join('-');
                    }
                },
                onSlide: function (val) {
                    $('price-max-display').innerHTML = val[1];
                    $('price-min-display').innerHTML = val[0];
                }
            }
        );
    },
    bindListeners: function () {
        var self = this;
        if (self.listenersBinded || !self.isAjaxEnabled) {
            return false;
        }
        self.listenersBinded = true;
        document.observe("dom:loaded", function () {
            self.ajaxListener();

            (function (History) {
                if (!History.enabled) {
                    return false;
                }

                self.pushState({
                    listing: $('catalog-listing').innerHTML,
                    layer: $('layered-navigation').innerHTML
                }, document.location.href, true);

                // Bind to StateChange Event
                History.Adapter.bind(window, 'popstate', function (event) {
                    if (event.type == 'popstate') {
                        var State = History.getState();
                        $('catalog-listing').update(State.data.listing);
                        $('layered-navigation').update(State.data.layer);
                        self.ajaxListener();
                        self.toggleContent();
                        self.alignProductGridActions();
                        self.blockCollapsing();
                    }
                });
            })(window.History);
        });
    },
    toggleContent: function() {
        // ==============================================
        // UI Pattern - Toggle Content (tabs and accordions in one setup)
        // ==============================================

        $j('.toggle-content').each(function () {
            var wrapper = jQuery(this);

            var hasTabs = wrapper.hasClass('tabs');
            var hasAccordion = wrapper.hasClass('accordion');
            var startOpen = wrapper.hasClass('open');

            var dl = wrapper.children('dl:first');
            var dts = dl.children('dt');
            var panes = dl.children('dd');
            var groups = new Array(dts, panes);

            //Create a ul for tabs if necessary.
            if (hasTabs) {
                var ul = jQuery('<ul class="toggle-tabs"></ul>');
                dts.each(function () {
                    var dt = jQuery(this);
                    var li = jQuery('<li></li>');
                    li.html(dt.html());
                    ul.append(li);
                });
                ul.insertBefore(dl);
                var lis = ul.children();
                groups.push(lis);
            }

            //Add "last" classes.
            var i;
            for (i = 0; i < groups.length; i++) {
                groups[i].filter(':last').addClass('last');
            }

            function toggleClasses(clickedItem, group) {
                var index = group.index(clickedItem);
                var i;
                for (i = 0; i < groups.length; i++) {
                    groups[i].removeClass('current');
                    groups[i].eq(index).addClass('current');
                }
            }

            //Toggle on tab (dt) click.
            dts.on('click', function (e) {
                //They clicked the current dt to close it. Restore the wrapper to unclicked state.
                if (jQuery(this).hasClass('current') && wrapper.hasClass('accordion-open')) {
                    wrapper.removeClass('accordion-open');
                } else {
                    //They're clicking something new. Reflect the explicit user interaction.
                    wrapper.addClass('accordion-open');
                }
                toggleClasses(jQuery(this), dts);
            });

            //Toggle on tab (li) click.
            if (hasTabs) {
                lis.on('click', function (e) {
                    toggleClasses(jQuery(this), lis);
                });
                //Open the first tab.
                lis.eq(0).trigger('click');
            }

            //Open the first accordion if desired.
            if (startOpen) {
                dts.eq(0).trigger('click');
            }

        });
    },
    alignProductGridActions: function() {
        // ==============================================
        // Product Listing - Align action buttons/links
        // ==============================================

        // Since the number of columns per grid will vary based on the viewport size, the only way to align the action
        // buttons/links is via JS

        if ($j('.products-grid').length) {

            var alignProductGridActions = function () {
                // Loop through each product grid on the page
                $j('.products-grid').each(function(){
                    var gridRows = []; // This will store an array per row
                    var tempRow = [];
                    productGridElements = $j(this).children('li');
                    productGridElements.each(function (index) {
                        // The JS ought to be agnostic of the specific CSS breakpoints, so we are dynamically checking to find
                        // each row by grouping all cells (eg, li elements) up until we find an element that is cleared.
                        // We are ignoring the first cell since it will always be cleared.
                        if ($j(this).css('clear') != 'none' && index != 0) {
                            gridRows.push(tempRow); // Add the previous set of rows to the main array
                            tempRow = []; // Reset the array since we're on a new row
                        }
                        tempRow.push(this);

                        // The last row will not contain any cells that clear that row, so we check to see if this is the last cell
                        // in the grid, and if so, we add its row to the array
                        if (productGridElements.length == index + 1) {
                            gridRows.push(tempRow);
                        }
                    });

                    $j.each(gridRows, function () {
                        var tallestProductInfo = 0;
                        $j.each(this, function () {
                            // Since this function is called every time the page is resized, we need to remove the min-height
                            // and bottom-padding so each cell can return to its natural size before being measured.
                            $j(this).find('.product-info').css({
                                'min-height': '',
                                'padding-bottom': ''
                            });

                            // We are checking the height of .product-info (rather than the entire li), because the images
                            // will not be loaded when this JS is run.
                            var productInfoHeight = $j(this).find('.product-info').height();
                            // Space above .actions element
                            var actionSpacing = 10;
                            // The height of the absolutely positioned .actions element
                            var actionHeight = $j(this).find('.product-info .actions').height();

                            // Add height of two elements. This is necessary since .actions is absolutely positioned and won't
                            // be included in the height of .product-info
                            var totalHeight = productInfoHeight + actionSpacing + actionHeight;
                            if (totalHeight > tallestProductInfo) {
                                tallestProductInfo = totalHeight;
                            }

                            // Set the bottom-padding to accommodate the height of the .actions element. Note: if .actions
                            // elements are of varying heights, they will not be aligned.
                            $j(this).find('.product-info').css('padding-bottom', actionHeight + 'px');
                        });
                        // Set the height of all .product-info elements in a row to the tallest height
                        $j.each(this, function () {
                            $j(this).find('.product-info').css('min-height', tallestProductInfo);
                        });
                    });
                });
            }
            alignProductGridActions();
        }
    },
    blockCollapsing: function() {
        // ==============================================
        // Block collapsing (on smaller viewports)
        // ==============================================

        enquire.register('(max-width: ' + bp.medium + 'px)', {
            setup: function () {
                this.toggleElements = $j(
                    // This selects the menu on the My Account and CMS pages
                    '.col-left-first .block:not(.block-layered-nav) .block-title, ' +
                    '.col-left-first .block-layered-nav .block-subtitle--filter, ' +
                    '.sidebar:not(.col-left-first) .block .block-title'
                );
            },
            match: function () {
                this.toggleElements.toggleSingle();
            },
            unmatch: function () {
                this.toggleElements.toggleSingle({destruct: true});
            }
        });
    }
}