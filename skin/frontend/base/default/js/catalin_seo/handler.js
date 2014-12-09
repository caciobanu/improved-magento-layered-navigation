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
                    }
                });
            })(window.History);
        });
    }
}