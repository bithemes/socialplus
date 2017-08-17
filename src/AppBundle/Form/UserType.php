<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class UserType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', TextType::class)
			->add('surname', TextType::class)
			->add('email', EmailType::class)
			->add('username', TextType::class)
			->add('plainPassword', RepeatedType::class, [
				'type' => PasswordType::class,
				'first_options'  => ['label' => 'Password'],
				'second_options' => ['label' => 'Repeat Password']
			])
			->add('gender', ChoiceType::class, [
				'choices' => [
					'Male' => 1,
					'Female' => 0
				],
	            'data' => true,
	            'placeholder' => false])
			->add('country', CountryType::class);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'data_class' => User::class,
			'validation_groups' => ['register'],
			'required' => true
		]);
	}
}