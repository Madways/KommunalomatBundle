<?php

namespace Madways\KommunalomatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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

        return array('question' => $question,
                     'question_count' => $this->_getQuestionCount());
    }

    /**
    * @Template()
    */
    public function createAction(Request $request)
    {
        $question = new Question();

        $form = $this->createFormBuilder($question)
                ->add('title', 'text')
                ->add('explanation')
                ->add('save', 'submit')
                ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $question->setWeight($this->_getQuestionCount()+1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($question);
            $em->flush();

            return $this->redirect($this->generateUrl("MadwaysKommunalomatBundleQuestionAdd"));
        }

        return array('form' => $form->createView(),
                     'question_count' => $this->_getQuestionCount() );
    }

    /**
    * Helper function to get the count of all questions
    */
    private function _getQuestionCount() {
        // Initiate Repository
        $repository = $this->getDoctrine()
                ->getRepository('MadwaysKommunalomatBundle:Question');
        // get count of all questions
        $question_count = $repository->createQueryBuilder("q")
                                        ->select('count(q.id)')
                                        ->getQuery()->getSingleScalarResult();
        return $question_count;
    }
}
