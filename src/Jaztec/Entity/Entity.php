<?php

namespace Jaztec\Entity;

abstract class Entity {
    
    /**
     * Probeer de waarden van een array toe te voegen aan de entity
     * 
     * @param array $array
     * @return \Jaztec\Entity\Entity
     */
    public function updateFromArray(array $array) {
        foreach($array as $key => $value) {
            $method = 'set' . ucfirst($key);
            if(method_exists($this, $method))
                $this->$method($value);
        }
        return $this; 
    }
    
    /**
     * Geef een array terug met de waarden van de entity
     * 
     * @return array
     */
    abstract public function serialize();
}