<?php

namespace Madways\KommunalomatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Madways\KommunalomatBundle\Entity\Question as Question;
use Madways\KommunalomatBundle\Entity\UserAnswer as UserAnswer;
use Madways\KommunalomatBundle\Entity\User as User;

class QuestionController extends Controller
{
    /**
    * @Template()
    */
    public function answerAction($weight, Request $request) 
    {
        $last_question = false;

        $user = new User(); //TODO: should be done per sessions
        $user->setSession("abc");

        // Initiate Repository
        $repository = $this->getDoctrine()
                ->getRepository('MadwaysKommunalomatBundle:Question');

        // Find the question by weight
        $question = $repository->findOneByWeight($weight);

        if (!$question) {
            $question = new Question();
            $last_question = true;
        }

        // empty Object
        $answer = new UserAnswer(); // TODO: find existing entry if there is one 


        // Create the Form
        $form = $this->createFormBuilder($answer)
                ->setAction($this->generateUrl("MadwaysKommunalomatBundleQuestionAnswer", array('weight' => $weight+1)))
                ->add('question', 'hidden', array('mapped' => false, 'data' => $question->getID() ))
                ->add('approve', 'submit', array('label'  => 'Zustimmung', 'attr'=> array('class'=>'small success')))
                ->add('neutral', 'submit', array('label'  => 'Neutral', 'attr'=> array('class'=>'small')))
                ->add('disapprove', 'submit', array('label'  => 'Ablehnung', 'attr'=> array('class'=>'small alert')))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // set it to the right question given by the form
            $answer->setQuestion($em->getReference('MadwaysKommunalomatBundle:Question', $form->get('question')->getData() ));

            // set user by id
            $answer->setUser($em->getReference('MadwaysKommunalomatBundle:User', 61 ));

            // set the answer by clicked button
            $answer->setAnswerAsString($form->getClickedButton()->getName());

            $em->persist($answer);
            $em->flush();


            // more or less dirty work around to reset the form and display the next question
            return $this->forward('MadwaysKommunalomatBundle:Question:Answer', array('weight' => $weight, 'request' => new Request()));
        }

        if($last_question) {
            return $this->redirect($this->generateUrl('welcome'));
        }

        return array('question' => $question,
                     'form' => $form->createView(),
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

            return $this->redirect($this->generateUrl("MadwaysKommunalomatBundleQuestionCreate"));
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
