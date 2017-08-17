<?php

namespace AppBundle\Form;

use AppBundle\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PostType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('content', TextareaType::class, [
				'attr' => [
					'placeholder' => 'Start typing...',
					'spellcheck' => 'false',
					'class' => 'new-post-content autoExpand'
				],
				'label' => false
			])
			->add('imageFile', VichImageType::class, [ 'label' => 'Add Image' ]);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'data_class' => Post::class,
			'required' => false
		]);
	}
}