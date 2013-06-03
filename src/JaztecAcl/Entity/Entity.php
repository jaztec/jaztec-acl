<?php

namespace JaztecAcl\Entity;

abstract class Entity
{
    /**
     * Probeer de waarden van een array toe te voegen aan de entity
     *
     * @param  array                    $array
     * @return \JaztecAcl\Entity\Entity
     */
    public function updateFromArray(array $array)
    {
        foreach ($array as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method))
                $this->$method($value);
        }

        return $this;
    }

    /**
     * Return an array with the values of the Entity
     *
     * @return array
     */
    abstract public function serialize();
}
