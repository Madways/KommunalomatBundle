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

    public function getAllWithAnswersByUser($id) {
        return $this->_em->createQueryBuilder()
                    ->select('q as question','ua.answer as user_answer', 'ua.count_double', 'p', 'pa')
                    ->from('MadwaysKommunalomatBundle:Question', 'q')
                    ->leftJoin('q.user_answers', 'ua WITH ua.user = :user')
                    ->leftJoin('q.party_answers', 'pa')
                    ->leftJoin('pa.party', 'p')
                    ->orderBy('p.id', 'ASC')
                    ->orderBy('q.weight', 'ASC')
                    ->setParameter('user', $id)
                    ->getQuery()
                    ->getArrayResult();
    }
}
