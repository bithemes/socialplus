<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository {

	public function findByKeyword(string $keyword) {
		$repository = $this->getEntityManager()->getRepository('AppBundle:User');
		$query = $repository->createQueryBuilder('u')
		                    ->where('u.username LIKE :query OR u.name LIKE :query OR u.surname LIKE :query')
		                    ->setParameter('query', '%'.$keyword.'%')
		                    ->getQuery();

		return $query->getResult();
	}
}
