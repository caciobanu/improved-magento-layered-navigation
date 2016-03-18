<?php

/**
 * Catalin Ciobanu
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License (MIT)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @package     Catalin_Seo
 * @copyright   Copyright (c) 2016 Catalin Ciobanu
 * @license     https://opensource.org/licenses/MIT  MIT License (MIT)
 */
class Catalin_SEO_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Delimiter for multiple filters
     */

    const MULTIPLE_FILTERS_DELIMITER = ',';
    const REL_NOFOLLOW = 'rel="nofollow"';

    /**
     * Check if module is enabled or not
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag('catalin_seo/catalog/enabled');
    }

    /**
     * Check if ajax is enabled
     *
     * @return boolean
     */
    public function isAjaxEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return Mage::getStoreConfigFlag('catalin_seo/catalog/ajax_enabled');
    }

    /**
     * Check if multiple choice filters is enabled
     *
     * @return boolean
     */
    public function isMultipleChoiceFiltersEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return Mage::getStoreConfigFlag('catalin_seo/catalog/multiple_choice_filters');
    }

    /**
     * Check if price slider is enabled
     *
     * @return boolean
     */
    public function isPriceSliderEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return Mage::getStoreConfigFlag('catalin_seo/catalog/price_slider');
    }

    /**
     * Retrieve routing suffix
     *
     * @return string
     */
    public function getRoutingSuffix()
    {
        return '/' . Mage::getStoreConfig('catalin_seo/catalog/routing_suffix');
    }

    /**
     * Getter for layered navigation params
     * If $params are provided then it overrides the ones from registry
     *
     * @param array $params
     * @return array|null
     */
    public function getCurrentLayerParams(array $params = null)
    {
        $layerParams = Mage::registry('layer_params');

        if (!is_array($layerParams)) {
            $layerParams = array();
        }

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                if ($value === null) {
                    unset($layerParams[$key]);
                } else {
                    $layerParams[$key] = $value;
                }
            }
        }

        unset($layerParams['isLayerAjax']);

        // Sort by key - small SEO improvement
        ksort($layerParams);
        return $layerParams;
    }

    /**
     * Method to get url for layered navigation
     *
     * @param array $filters      array with new filter values
     * @param boolean $noFilters  to add filters to the url or not
     * @param array $q            array with values to add to query string
     * @return string
     */
    public function getFilterUrl(array $filters, $noFilters = false, array $q = array())
    {
        $query = array(
            'isLayerAjax' => null, // this needs to be removed because of ajax request
            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
        );
        $query = array_merge($query, $q);

        $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
        $params = array(
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => $query,
            '_escape' => true,
        );

        $url = Mage::getUrl('*/*/*', $params);
        $urlPath = '';

        if (!$noFilters) {
            // Add filters
            $layerParams = $this->getCurrentLayerParams($filters);
            foreach ($layerParams as $key => $value) {
                // Encode and replace escaped delimiter with the delimiter itself
                $value = str_replace(urlencode(self::MULTIPLE_FILTERS_DELIMITER), self::MULTIPLE_FILTERS_DELIMITER, urlencode($value));
                $urlPath .= "/{$key}/{$value}";
            }
        }

        // Skip adding routing suffix for links with no filters
        if (empty($urlPath)) {
            return $url;
        }

        $urlParts = explode('?', $url);

        $urlParts[0] = $this->getUrlBody($suffix, $urlParts[0]);

        // Add the suffix to the url - fixes when coming from non suffixed pages
        // It should always be the last bits in the URL
        $urlParts[0] .= $this->getRoutingSuffix();

        $url = $urlParts[0] . $urlPath;
        $url = $this->appendSuffix($url, $suffix);
        if (!empty($urlParts[1])) {
            $url .= '?' . $urlParts[1];
        }

        return $url;
    }

    /**
     * Get the url path, including the base url, minus the suffix.
     * Checks for Enterprise and if it is, checks for the dot
     * before returning
     * @param  string $suffix
     * @param  string $urlParts
     * @return string
     */
    public function getUrlBody($suffix, $urlParts) {
        if (Mage::getEdition() == Mage::EDITION_ENTERPRISE) {
            $lenSuffix = (strlen($suffix) > 0 ? strlen($suffix) + 1 : 0);
            return substr($urlParts, 0, strlen($urlParts) - $lenSuffix);
        } else {
            return substr($urlParts, 0, strlen($urlParts) - strlen($suffix));
        }
    }

    /**
     * Appends the suffix to the url, if applicable.
     * Checks for Enterprise and if it is, adds the dot
     * before returning
     *
     * @param  string $url
     * @param  string $suffix
     * @return string
     */
    public function appendSuffix($url, $suffix) {
        if (strlen($suffix) == 0) {
            return $url;
        }
        if (Mage::getEdition() == Mage::EDITION_ENTERPRISE ? $ds = "." : $ds="");
        return $url . $ds . $suffix;
    }

    /**
     * Get the url to clear all layered navigation filters
     *
     * @return string
     */
    public function getClearFiltersUrl()
    {
        return $this->getFilterUrl(array(), true);
    }

    /**
     * Get url for layered navigation pagination
     *
     * @param array $query
     * @return string
     */
    public function getPagerUrl(array $query)
    {
        return $this->getFilterUrl(array(), false, $query);
    }

    /**
     * Check if we are in the catalog search
     *
     * @return boolean
     */
    public function isCatalogSearch()
    {
        $pathInfo = $this->_getRequest()->getPathInfo();
        if (stripos($pathInfo, '/catalogsearch/result') !== false) {
            return true;
        }
        return false;
    }

    /**
     * Check if a string has utf8 characters in it
     *
     * @param  string $string
     * @return boolean
     */
    public function seemsUtf8($string)
    {
        $length = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            if (ord($string[$i]) < 0x80) {
                continue; # 0bbbbbbb
            } elseif ((ord($string[$i]) & 0xE0) == 0xC0) {
                $n = 1; # 110bbbbb
            } elseif ((ord($string[$i]) & 0xF0) == 0xE0) {
                $n = 2; # 1110bbbb
            } elseif ((ord($string[$i]) & 0xF8) == 0xF0) {
                $n = 3; # 11110bbb
            } elseif ((ord($string[$i]) & 0xFC) == 0xF8) {
                $n = 4; # 111110bb
            } elseif ((ord($string[$i]) & 0xFE) == 0xFC) {
                $n = 5; # 1111110b
            } else {
                return false; # Does not match any model
            }
            for ($j = 0; $j < $n; $j++) { # n bytes matching 10bbbbbb follow ?
                if ((++$i == strlen($string)) || ((ord($string[$i]) & 0xC0) != 0x80)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * US-ASCII transliterations of Unicode text
     * Warning: you should only pass this well formed UTF-8!
     * Be aware it works by making a copy of the input string which it appends transliterated
     * characters to - it uses a PHP output buffer to do this - it means, memory use will increase,
     * requiring up to the same amount again as the input string
     *
     * @param string $str UTF-8 string to convert
     * @param string $unknown Character use if character unknown
     * @return string US-ASCII string
     */
    public function utf8ToAscii($str, $unknown = '?')
    {
        static $UTF8_TO_ASCII;

        if (strlen($str) == 0) {
            return;
        }

        preg_match_all('/.{1}|[^\x00]{1,1}$/us', $str, $ar);
        $chars = $ar[0];

        foreach ($chars as $i => $c) {
            $ud = 0;
            if (ord($c{0}) >= 0 && ord($c{0}) <= 127) {
                continue;
            } // ASCII - next please
            if (ord($c{0}) >= 192 && ord($c{0}) <= 223) {
                $ord = (ord($c{0}) - 192) * 64 + (ord($c{1}) - 128);
            }
            if (ord($c{0}) >= 224 && ord($c{0}) <= 239) {
                $ord = (ord($c{0}) - 224) * 4096 + (ord($c{1}) - 128) * 64 + (ord($c{2}) - 128);
            }
            if (ord($c{0}) >= 240 && ord($c{0}) <= 247) {
                $ord = (ord($c{0}) - 240) * 262144 + (ord($c{1}) - 128) * 4096 + (ord($c{2}) - 128) * 64 + (ord($c{3}) - 128);
            }
            if (ord($c{0}) >= 248 && ord($c{0}) <= 251) {
                $ord = (ord($c{0}) - 248) * 16777216 + (ord($c{1}) - 128) * 262144 + (ord($c{2}) - 128) * 4096 + (ord($c{3}) - 128) * 64 + (ord($c{4}) - 128);
            }
            if (ord($c{0}) >= 252 && ord($c{0}) <= 253) {
                $ord = (ord($c{0}) - 252) * 1073741824 + (ord($c{1}) - 128) * 16777216 + (ord($c{2}) - 128) * 262144 + (ord($c{3}) - 128) * 4096 + (ord($c{4}) - 128) * 64 + (ord($c{5}) - 128);
            }
            if (ord($c{0}) >= 254 && ord($c{0}) <= 255) {
                $chars{$i} = $unknown;
                continue;
            } //error

            $bank = $ord >> 8;

            if (!array_key_exists($bank, (array) $UTF8_TO_ASCII)) {
                $bankfile = __DIR__ . '/data/' . sprintf("x%02x", $bank) . '.php';
                if (file_exists($bankfile)) {
                    include $bankfile;
                } else {
                    $UTF8_TO_ASCII[$bank] = array();
                }
            }

            $newchar = $ord & 255;
            if (array_key_exists($newchar, $UTF8_TO_ASCII[$bank])) {
                $chars{$i} = $UTF8_TO_ASCII[$bank][$newchar];
            } else {
                $chars{$i} = $unknown;
            }
        }
        return implode('', $chars);
    }

    /**
     * Uses transliteration tables to convert any kind of utf8 character
     *
     * @param string $text
     * @param string $separator
     * @return string $text
     */
    public function transliterate($text, $separator = '-')
    {
        if (preg_match('/[\x80-\xff]/', $text) && $this->validUtf8($text)) {
            $text = $this->utf8ToAscii($text);
        }
        return $this->postProcessText($text, $separator);
    }

    /**
     * Tests a string as to whether it's valid UTF-8 and supported by the
     * Unicode standard
     *
     * @param string $str UTF-8 encoded string
     * @return boolean true if valid
     */
    public function validUtf8($str)
    {
        $mState = 0;     // cached expected number of octets after the current octet
        // until the beginning of the next UTF8 character sequence
        $mUcs4 = 0;     // cached Unicode character
        $mBytes = 1;     // cached expected number of octets in the current sequence

        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $in = ord($str{$i});
            if ($mState == 0) {
                // When mState is zero we expect either a US-ASCII character or a
                // multi-octet sequence.
                if (0 == (0x80 & ($in))) {
                    // US-ASCII, pass straight through.
                    $mBytes = 1;
                } elseif (0xC0 == (0xE0 & ($in))) {
                    // First octet of 2 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x1F) << 6;
                    $mState = 1;
                    $mBytes = 2;
                } elseif (0xE0 == (0xF0 & ($in))) {
                    // First octet of 3 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x0F) << 12;
                    $mState = 2;
                    $mBytes = 3;
                } elseif (0xF0 == (0xF8 & ($in))) {
                    // First octet of 4 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x07) << 18;
                    $mState = 3;
                    $mBytes = 4;
                } elseif (0xF8 == (0xFC & ($in))) {
                    /* First octet of 5 octet sequence.
                     *
                     * This is illegal because the encoded codepoint must be either
                     * (a) not the shortest form or
                     * (b) outside the Unicode range of 0-0x10FFFF.
                     * Rather than trying to resynchronize, we will carry on until the end
                     * of the sequence and let the later error handling code catch it.
                     */
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x03) << 24;
                    $mState = 4;
                    $mBytes = 5;
                } elseif (0xFC == (0xFE & ($in))) {
                    // First octet of 6 octet sequence, see comments for 5 octet sequence.
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 1) << 30;
                    $mState = 5;
                    $mBytes = 6;
                } else {
                    /* Current octet is neither in the US-ASCII range nor a legal first
                     * octet of a multi-octet sequence.
                     */
                    return false;
                }
            } else {
                // When mState is non-zero, we expect a continuation of the multi-octet
                // sequence
                if (0x80 == (0xC0 & ($in))) {
                    // Legal continuation.
                    $shift = ($mState - 1) * 6;
                    $tmp = $in;
                    $tmp = ($tmp & 0x0000003F) << $shift;
                    $mUcs4 |= $tmp;
                    /**
                     * End of the multi-octet sequence. mUcs4 now contains the final
                     * Unicode codepoint to be output
                     */
                    if (0 == --$mState) {
                        /*
                         * Check for illegal sequences and codepoints.
                         */
                        // From Unicode 3.1, non-shortest form is illegal
                        if (((2 == $mBytes) && ($mUcs4 < 0x0080)) ||
                            ((3 == $mBytes) && ($mUcs4 < 0x0800)) ||
                            ((4 == $mBytes) && ($mUcs4 < 0x10000)) ||
                            (4 < $mBytes) ||
                            // From Unicode 3.2, surrogate characters are illegal
                            (($mUcs4 & 0xFFFFF800) == 0xD800) ||
                            // Codepoints outside the Unicode range are illegal
                            ($mUcs4 > 0x10FFFF)
                        ) {
                            return false;
                        }
                        //initialize UTF8 cache
                        $mState = 0;
                        $mUcs4 = 0;
                        $mBytes = 1;
                    }
                } else {
                    /**
                     * ((0xC0 & (*in) != 0x80) && (mState != 0))
                     * Incomplete multi-octet sequence.
                     */
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Cleans up the text and adds separator
     *
     * @param string $text
     * @param string $separator
     * @return string
     */
    protected function postProcessText($text, $separator)
    {
        if (function_exists('mb_strtolower')) {
            $text = mb_strtolower($text);
        } else {
            $text = strtolower($text);
        }

        // Remove all none word characters
        $text = preg_replace('/\W/', ' ', $text);

        // More stripping. Replace spaces with dashes
        $text = strtolower(preg_replace('/[^A-Z^a-z^0-9^\/]+/', $separator, preg_replace('/([a-z\d])([A-Z])/', '\1_\2', preg_replace('/([A-Z]+)([A-Z][a-z])/', '\1_\2', preg_replace('/::/', '/', $text)))));

        return trim($text, $separator);
    }

    public function getSkinJsUrl()
    {
        if(Mage::getEdition() == Mage::EDITION_ENTERPRISE){
            return "js/catalin_seo/handler-ee-rwd.js";
        }

        return "js/catalin_seo/handler.js";
    }

    public function getNofollow()
    {
        if(Mage::getStoreConfigFlag('catalin_seo/catalog/nofollow')){
            return self::REL_NOFOLLOW;
        }
    }

}
