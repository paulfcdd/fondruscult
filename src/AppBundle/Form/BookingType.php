<?php

namespace AppBundle\Form;


use AppBundle\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phone', TextType::class, [
                'attr' => [
                    'class' => 'form-control no-border-radius',
                    'placeholder' => 'Телефон'
                ]
            ])
            ->add('date', TextType::class, [
                'attr' => [
                    'class' => 'form-control no-border-radius',
                    'placeholder' => 'Дата',
                    'autocomplete' => 'off',
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control no-border-radius',
                    'placeholder' => 'Email'
                ]
            ])
            ->add('guests', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control no-border-radius',
                    'placeholder' => 'Кол-во человек'
                ]
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control no-border-radius no-resize',
                    'placeholder' => 'Расскажите кратко о меропритяии',
                    'cols' => 4,
                    'rows' => 4
                ]
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Отправить',
                'attr' => [
                    'class' => 'btn btn-success no-border-radius'
                ]
            ]);
    }
}