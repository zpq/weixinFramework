<?php

namespace app\container;
/**
 * Description of Container
 *
 * @author Administrator
 */
class Container {
    
    private $instance = [];
    
    private $alise = [];

    public function __construct() {
        
    }
    
    public function get($key, $alise = false) {
        if ($alise) {
            if (isset($this->alise[$alise])) {
                $a = $this->alise[$alise];
                if (isset($this->instance[$a])) {
                    return $this->instance[$a];
                } else {
                    return false;
                }
            }
        }
        
        if (isset($this->instance[$key])) {
            return $this->instance[$key];
        } else {
            return null;
        }
    }
    
    public function set($key, $value) {
        if (isset($this->alise[$key])) {
            return false;
        } else {
            $this->alise[$key] = $value;
            return true;
        }
    }
}

?>
