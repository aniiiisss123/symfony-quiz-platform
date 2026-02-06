<?php
namespace App\Form;

use App\Entity\Exercice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class ExerciceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Enter the question'
                ],
                'label_attr' => ['class' => 'form-label fw-bold']
            ])
            ->add('options', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Option 1, Option 2, Option 3',
                    'rows' => 3
                ],
                'help' => 'Enter options separated by commas',
                'label_attr' => ['class' => 'form-label fw-bold']
            ])
            ->add('score', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 100
                ],
                'label_attr' => ['class' => 'form-label fw-bold']
            ])
            ->add('correctAnswer', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter the correct answer'
                ],
                'label_attr' => ['class' => 'form-label fw-bold']
            ])
            ->add('imagePath', FileType::class, [
                'required' => false,
                'mapped' => false, 
                'attr' => ['class' => 'form-control'],
            ])
            ->add('is_mandatory', CheckboxType::class, [
                'required' => false,
                'label' => 'Is this exercise mandatory?',
                'label_attr' => ['class' => 'form-check-label'],
                'attr' => ['class' => 'form-check-input']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exercice::class,
        ]);
    }
}