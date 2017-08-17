<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PersonalType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('location', TextType::class)
			->add('birthday', BirthdayType::class)
			->add('website', UrlType::class)
			->add('workplace', TextType::class)
			->add('profession', TextType::class)
			->add('school', TextType::class)
			->add('language', LanguageType::class)
			->add('backgroundFile', VichImageType::class, [ 'label' => 'Background' ])
			->add('avatarFile', VichImageType::class, [ 'label' => 'Avatar' ]);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'data_class' => User::class,
			'required' => false
		]);
	}
}