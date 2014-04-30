<?php

namespace Madways\KommunalomatBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

class QuestionRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy(array(), array('weight' => 'ASC'));
    }

    /*
    * Helper function to get the count of all questions
    */
    public function getCount() {

        return  $this->createQueryBuilder("q")
                                        ->select('count(q.id)')
                                        ->getQuery()->getSingleScalarResult();
    }
}
