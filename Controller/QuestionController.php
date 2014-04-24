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
    public function indexAction() 
    {

        $em = $this->getDoctrine()->getManager();

        $questions = $em->getRepository('MadwaysKommunalomatBundle:Question')->findAll();

        if (!$questions) {
            return $this->redirect($this->generateUrl("MadwaysKommunalomatBundleQuestionCreate"));
        }

        return array('questions' => $questions,
                     'question_count' => $this->_getQuestionCount());
    }

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
    public function formAction(Request $request, $id = NULL)
    {
        $em = $this->getDoctrine()->getManager();

        if (isset($id)) {
            $question = $em->find('MadwaysKommunalomatBundle:Question', $id);

            if (!$question) {
                throw new NotFoundHttpException("Invalid question.");
            }
        } else {
            $question = new Question();
            $question->setWeight($this->_getQuestionCount()+1);
        }

        $form = $this->createFormBuilder($question)
                ->add('title', 'text')
                ->add('explanation')
                ->add('weight', 'hidden')
                ->add('save', 'submit')
                ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($question);
            $em->flush();

            return $this->redirect($this->generateUrl("MadwaysKommunalomatBundleQuestionCreate"));
        }

        return array('form' => $form->createView(),
                     'question_count' => $this->_getQuestionCount() );
    }

    /**
    * 
    * @Template()
    */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $question = $em->find('MadwaysKommunalomatBundle:Question', $id);

        if (!$question) {
            throw new NotFoundHttpException("Invalid question.");
        }

        $em->remove($question);
        $em->flush();

        // TODO: resort all question weights
        // TODO: ask for confirmation

        return $this->redirect($this->generateUrl('MadwaysKommunalomatBundleQuestion'));
    }

    /**
    * 
    * @Template()
    */
    public function moveAction($id, $direction)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $question_moved = $em->find('MadwaysKommunalomatBundle:Question', $id);

        if (!$question_moved) {
            throw new NotFoundHttpException("no questions to move");
        }

        if ($direction == "up") {
            $question_before = $em->getRepository('MadwaysKommunalomatBundle:Question')->findOneByWeight($question_moved->getWeight()-1);
            $question_before->setWeight($question_moved->getWeight());
            $question_moved->setWeight($question_moved->getWeight()-1);
        } elseif ($direction == "down") {
            $question_after = $em->getRepository('MadwaysKommunalomatBundle:Question')->findOneByWeight($question_moved->getWeight()+1);
            $question_after->setWeight($question_moved->getWeight());
            $question_moved->setWeight($question_moved->getWeight()+1);
        } else {
            throw new NotFoundHttpException("unknown direction");
        }

        $em->flush();

        return $this->redirect($this->generateUrl('MadwaysKommunalomatBundleQuestion'));
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
