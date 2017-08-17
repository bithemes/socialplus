<?php

namespace AppBundle\Form;

use AppBundle\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('username', TextType::class, [
				'attr' => [
					'placeholder' => 'Username'
				],
				'label' => false
			])
			->add('content', TextareaType::class, [
				'attr' => [
					'placeholder' => 'Start typing...',
					'spellcheck' => 'false',
					'class' => 'new-msg-content autoExpand'
				],
				'label' => false
			])
			->add('submit', SubmitType::class, [
				'attr' => [
					'class' => 'btn btn-info'
				]
			]);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'data_class' => Message::class,
			'required' => false,
			'error_mapping' => [
				'.' => 'username'
			],
		]);
	}
}