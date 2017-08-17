<?php

namespace AppBundle\Form;

use AppBundle\Entity\Ban;
use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BanType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('targetUser', TextType::class, [ 'label' => 'User'] )
			->add('submit', SubmitType::class, [ 'label' => 'Ban', 'attr' => [
				'class' => 'btn-info'
			] ]);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([

		]);
	}
}