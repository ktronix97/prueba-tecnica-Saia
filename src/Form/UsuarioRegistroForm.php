<?php
// src/Form/UsuarioRegistrationFormType.php
namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    TextType,
    EmailType,
    PasswordType,
    RepeatedType,
    SubmitType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UsuarioRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('primerNombre', TextType::class, [
                'label'       => 'Primer nombre',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'El primer nombre es obligatorio.']),
                    new Assert\Length([
                        'max'        => 50,
                        'maxMessage' => 'Máximo {{ limit }} caracteres.',
                    ]),
                ],
            ])
            ->add('segundoNombre', TextType::class, [
                'label'       => 'Segundo nombre',
                'required'    => false,
                'constraints' => [
                    new Assert\Length([
                        'max'        => 50,
                        'maxMessage' => 'Máximo {{ limit }} caracteres.',
                    ]),
                ],
            ])
            ->add('primerApellido', TextType::class, [
                'label'       => 'Primer apellido',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'El primer apellido es obligatorio.']),
                    new Assert\Length([
                        'max'        => 50,
                        'maxMessage' => 'Máximo {{ limit }} caracteres.',
                    ]),
                ],
            ])
            ->add('segundoApellido', TextType::class, [
                'label'    => 'Segundo apellido',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max'        => 50,
                        'maxMessage' => 'Máximo {{ limit }} caracteres.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label'       => 'Correo electrónico',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'El email es obligatorio.']),
                    new Assert\Email(['message' => 'Formato de email inválido.']),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'mapped'          => false,
                'first_options'   => ['label' => 'Contraseña'],
                'second_options'  => ['label' => 'Repetir contraseña'],
                'invalid_message' => 'Las contraseñas no coinciden.',
                'constraints'     => [
                    new Assert\NotBlank(['message' => 'La contraseña es obligatoria.']),
                    new Assert\Length([
                        'min'        => 8,
                        'minMessage' => 'Debe tener al menos {{ limit }} caracteres.',
                        'max'        => 100,
                    ]),
                ],
            ])
            ->add('register', SubmitType::class, [
                'label' => 'Crear cuenta'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => Usuario::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'usuario_registration',
        ]);
    }
}
