<?php

namespace Madways\KommunalomatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Madways\KommunalomatBundle\Entity\Question as Question;

class QuestionController extends Controller
{
    /**
    * @Template()
    */
    public function indexAction($weight)
    {

        // Initiate Repository
        $repository = $this->getDoctrine()
                ->getRepository('MadwaysKommunalomatBundle:Question');

        // Find the question by weight
        $question = $repository->findOneByWeight($weight);

        if (!$question) {
            throw $this->createNotFoundException(
                'No question found for weight '.$weight
            );
        }

        // get count of all questions
        $question_count = $repository->createQueryBuilder("q")
                                        ->select('count(q.id)')
                                        ->getQuery()->getSingleScalarResult();

        return array('title' => $question->getTitle(),
                     'weight' => $question->getWeight(),
                     'question_count' => $question_count);
    }

    public function createAction($weight)
    {
        $question = new Question();
        $question->setTitle('A Foo Bar');
        $question->setWeight($weight);

        $em = $this->getDoctrine()->getManager();
        $em->persist($question);
        $em->flush();

        return new Response('Created question id '.$question->getId());
    }
}
