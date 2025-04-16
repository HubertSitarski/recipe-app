<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Your comment',
                'attr' => [
                    'placeholder' => 'Share your thoughts about this recipe...',
                    'rows' => 5,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a comment',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Your comment should be at least {{ limit }} characters',
                        'max' => 1000,
                        'maxMessage' => 'Your comment cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Add Comment',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
