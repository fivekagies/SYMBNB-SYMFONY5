<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ApplicationType;
use Faker\Provider\ar_JO\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('title',TextType::class,
              $this->getConfiguration('Titre','Taper un super titre pour votre annonce !'))
            ->add('slug',TextType::class,
        $this->getConfiguration('Adresse web','Taper l\'adresse web (automatique) !',['required' => false]))
            ->add('coverImage',UrlType::class,
                $this->getConfiguration('URL de l\'image principale','Donner une image qui donne vraiment envie !'))
            ->add('introduction',TextType::class,
                $this->getConfiguration('Introduction web','Donner une description globale de l\'annonce'))
            ->add('content',TextareaType::class,
                $this->getConfiguration('Description detaillée','Taper une description qui donne envie de venir chez vous !'))
            ->add('rooms',IntegerType::class,
                $this->getConfiguration('Nombre de chambre','Le nombre de chambre disponible'))
            ->add('price',MoneyType::class,
                $this->getConfiguration('Prix par nuit','Indiquer le prix que vous voulez pour une nuit'))
            ->add(
                'images',
                CollectionType::class,// on aimerai que notre collectionType se repete qu'on va le demander=> on va donner un tableau
                [
                    'entry_type' => ImageType::class,    //preciser le champ ou le formulaire à repeter
                    'allow_add' => true,    //OPTION 'ALLOW_ADD" preciser si on a le droit d'ajouter de nouveaux elements
                    'allow_delete' => true
                ]
            )
            ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
