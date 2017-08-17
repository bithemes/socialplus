<?php

namespace AppBundle\Form;

use AppBundle\Entity\Website;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class WebsiteType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('title', TextType::class)
			->add('subtitle', TextType::class)
			->add('posts_per_page', IntegerType::class)
			->add('main_color', TextType::class)
			->add('brandFile', VichImageType::class, [ 'label' => 'Brand Image' ])
			->add('submit', SubmitType::class, [
				'attr' => [
					'class' => 'btn btn-info'
				]
			]);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'data_class' => Website::class,
			'required' => false
		]);
	}
}