<?php
class Calculate_search_scores extends Trongate {

    private $points = [
        'h1_match' => 1000,
        'h1_partial_match' => 300,
        'h1_partial_match_sounds_like' => 100,
        'h2_partial_match' => 100,
        'h2_partial_match_sounds_like' => 33,
        'h3_partial_match' => 50,
        'h3_partial_match_sounds_like' => 17,
        'entire_document_partial_match' => 2,
        'entire_document_partial_match_sounds_like' => 1
    ];

    private $debug_mode = true;
    private $debug_info = [];
    private $page_score = 0;

    public function __construct() {
        parent::__construct();
        $this->parent_module = 'documentation';
        $this->child_module = 'calculate_search_scores';
    }

    public function _get_element_text_by_selector(string $html, string $selector, bool $first = false): array|string {
        if (empty($html) || empty($selector)) {
            return $first ? '' : [];
        }
        
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors(false);
        
        $xpath = new \DOMXPath($dom);
        $xpath_selector = $this->css_to_xpath($selector);
        
        if (empty($xpath_selector)) {
            return $first ? '' : [];
        }
        
        $elements = $xpath->query($xpath_selector);
        
        if ($elements === false || $elements->length === 0) {
            return $first ? '' : [];
        }
        
        if ($first) {
            // For first element only, get its direct text content
            $node = $elements->item(0);
            $text = '';
            foreach ($node->childNodes as $child) {
                if ($child->nodeType === XML_TEXT_NODE) {
                    $text .= $child->nodeValue;
                }
            }
            return trim($text);
        }
        
        // For all elements, get their direct text content
        $results = [];
        foreach ($elements as $element) {
            $text = '';
            foreach ($element->childNodes as $child) {
                if ($child->nodeType === XML_TEXT_NODE) {
                    $text .= $child->nodeValue;
                }
            }
            $text = trim($text);
            if ($text !== '') {
                $results[] = $text;
            }
        }
        
        return $results;
    }

    private function css_to_xpath(string $selector): string {
        $selector = trim($selector);
        if (empty($selector)) {
            return '';
        }

        try {
            // Element selector (e.g., "div", "h1")
            if (preg_match('/^[a-zA-Z0-9_-]+$/', $selector)) {
                return "//$selector";
            }

            // ID selector (e.g., "#main")
            if (preg_match('/^#([a-zA-Z0-9_-]+)$/', $selector, $matches)) {
                return "//*[@id='" . $this->escape_xpath_string($matches[1]) . "']";
            }

            // Class selector (e.g., ".header")
            if (preg_match('/^\.([a-zA-Z0-9_-]+)$/', $selector, $matches)) {
                return "//*[contains(concat(' ', normalize-space(@class), ' '), ' " . 
                       $this->escape_xpath_string($matches[1]) . " ')]";
            }

            // Element with class selector (e.g., "div.header")
            if (preg_match('/^([a-zA-Z0-9_-]+)\.([a-zA-Z0-9_-]+)$/', $selector, $matches)) {
                return "//{$matches[1]}[contains(concat(' ', normalize-space(@class), ' '), ' " . 
                       $this->escape_xpath_string($matches[2]) . " ')]";
            }

            return '';
        } catch (\Exception $e) {
            // Log error if needed
            return '';
        }
    }

    /**
     * Escapes special characters in strings used within XPath expressions
     */
    private function escape_xpath_string(string $str): string {
        if (str_contains($str, "'")) {
            // If string contains single quotes, use concat() to handle them
            $parts = explode("'", $str);
            return "concat('" . implode("', \"'\", '", $parts) . "')";
        }
        return $str;
    }



    public function __destruct() {
        $this->parent_module = '';
        $this->child_module = '';
    }

}