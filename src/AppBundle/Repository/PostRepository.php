<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PostRepository extends EntityRepository {
	public function findByKeyword(string $keyword) {
		$repository = $this->getEntityManager()->getRepository('AppBundle:Post');
		$query = $repository->createQueryBuilder('p')
			->where('p.content LIKE :query')
			->setParameter('query', '%'.$keyword.'%')
			->orderBy('p.uploadedAt', 'DESC')
			->getQuery();

		return $query->getResult();
	}
}
