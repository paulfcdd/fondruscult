<?php

namespace AppBundle\Form;


use AppBundle\Form\Type\FileUploadType;
use Symfony\Component\Form\Extension\Core\Type as CoreType;
use Symfony\Component\Form\FormBuilderInterface;

class NewsType extends AbstractFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('files', FileUploadType::class)
            ->add('publishStartDate', CoreType\DateType::class, [
                'label' => 'Старт публикации',
				'widget' => 'single_text',
				'format' => 'yyyy-MM-dd',
            ])
            ->add('publishEndDate', CoreType\DateType::class, [
                'label' => 'Конец публикации',
				'widget' => 'single_text',
				'format' => 'yyyy-MM-dd',
            ]);

    }
}