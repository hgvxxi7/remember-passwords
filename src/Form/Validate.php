<?php

namespace PasswordManager\Form;

class Validate
{

    public function characterValidator($name, $min, $max)
    {
        $countName = strlen($name);
        //$arrayBand = range(6, 20);
        //return in_array($countName, $arrayBand);
        /*if (in_array($countName, $arrayBand))
        {
            return true;
        }
            return false;*/
        if ($countName >= $min && $countName <= $max)
        {
            return true;
        }
        return false;
    }

}