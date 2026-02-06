<?php
namespace App\Form;

use App\Entity\Quiz;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter quiz title',
                ],
                'label' => 'Title',
                'label_attr' => ['class' => 'form-label fw-bold'],
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Enter quiz description',
                ],
                'label' => 'Description',
                'label_attr' => ['class' => 'form-label fw-bold'],
            ])
            ->add('duration', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter duration in minutes',
                ],
                'label' => 'Duration',
                'label_attr' => ['class' => 'form-label fw-bold'],
            ])
            ->add('totalscore', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter total score',
                ],
                'label' => 'Total Score',
                'label_attr' => ['class' => 'form-label fw-bold'],
            ])
            ->add('creationdate', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Creation Date',
                'label_attr' => ['class' => 'form-label fw-bold'],
            ])
            ->add('author', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter author name',
                ],
                'label' => 'Author',
                'label_attr' => ['class' => 'form-label fw-bold'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}