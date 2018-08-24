<?php

namespace Modules\Location;

class Location {
    private $locale;
    
    public function __construct($locale = 'en') {
        $this->locale = $locale;
    }
    
    public function getLocale() {
        return $this->locale;
    }
    
}
