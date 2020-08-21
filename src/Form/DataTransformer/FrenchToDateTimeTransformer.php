<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchToDateTimeTransformer implements DataTransformerInterface  {

    public function transform($date) //$date de type DateTime
    {
        if($date === null){
            return '';
        }
        return $date->format('d/m/Y');
    }

    public function reverseTransform($frenchDate)
    {
            if($frenchDate === null){
                //Exception  il y a une exception créer par symfony si notre transformation ne marche pas TransformationFailed
                throw new TransformationFailedException("vous devez fournir une date !");
            }

            $date = \DateTime::createFromFormat('d/m/Y',$frenchDate);

            if($date === false){
                //Exception
                throw new TransformationFailedException("Le format de la date n'est pas le bon !");
            }
            return $date;
    }

}