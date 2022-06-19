<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Bug;

class BugType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // 更新の時だけ、Statusを設定可能とする
        // 新規の時はStatusは"OPEN"をセットする為
        if ($builder->getData()->getId() !== null) {
            $builder->add('status', ChoiceType::class, [
                'choices' => Bug::STATUSES,
                'choice_label' => function ($value, $key, $index) {
                    return $value;
                },
            ]);
        }
         
        $builder
            ->add('description', TextareaType::class)
            ->add('engineer', EntityType::class, [
                'class' => 'AppBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
                'choice_label' => 'name',
                'placeholder' => '担当者を選択してください',
            ])
            ->add('reporter', EntityType::class, [
                'class' => 'AppBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
                'choice_label' => 'name',
                'placeholder' => '報告者を選択してください',
            ])
            ->add('products', EntityType::class, [
                'class' => 'AppBundle:Product',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Bug'
        ));
    }
}