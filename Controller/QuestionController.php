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

        $em = $this->getDoctrine()->getManager();

        // Find the question by weight
        $question = $em->getRepository('MadwaysKommunalomatBundle:Question')->findOneByWeight($weight);

        if (!$question) {
            return $this->redirect($this->generateUrl('welcome'));
        }

        // Find already given answer
        $answer = $em->getRepository('MadwaysKommunalomatBundle:UserAnswer')->findOneBy(array('user' => $this->_getUser(), 'question' => $question->getId() ));
        if (!$answer) {
            $answer = new UserAnswer();
        }


        // Create the Form
        $form = $this->createFormBuilder($answer)
                ->add('question', 'hidden', array('mapped' => false, 'data' => $question->getID() ))
                ->add('approve', 'submit', array('label'  => 'Zustimmung'))
                ->add('neutral', 'submit', array('label'  => 'Neutral'))
                ->add('disapprove', 'submit', array('label'  => 'Ablehnung'))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            // set it to the right question given by the form
            $answer->setQuestion($em->getReference('MadwaysKommunalomatBundle:Question', $form->get('question')->getData() ));

            // set user by id
            $answer->setUser($this->_getUser());

            // set the answer by clicked button
            $answer->setAnswerAsString($form->getClickedButton()->getName());

            $em->persist($answer);
            $em->flush();

            return $this->redirect($this->generateUrl('MadwaysKommunalomatBundleQuestionAnswer', array('weight' => $weight+1 )));
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

    /*
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

    /*
    * Helper function to get the current User from the Session and if not set create a new one
    */
    private function _getUser() {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        if ( !$session->has('user_id') ) {
            $user = new User();
            $user->setSession( $session->getId() );
            $em->persist($user);
            $em->flush();
            $session->set('user_id', $user->getId());
            return $user;
        }

        return $em->getReference('MadwaysKommunalomatBundle:User', $session->get('user_id') );
    }
}
