<?php

namespace Madways\KommunalomatBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

class QuestionRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy(array(), array('weight' => 'ASC'));
    }

    public function findAllWithPartyAnswers() {
        return $this->createQuery('SELECT q FROM kommunalomat_question JOIN PartyAnswer');
    }
}
