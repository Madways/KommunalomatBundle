<?php

namespace Madways\KommunalomatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
            return $this->redirect($this->generateUrl('MadwaysKommunalomatBundleResult', array('id' => $this->_getUser()->getId())));
        }

        // Find already given answer
        $answer = $em->getRepository('MadwaysKommunalomatBundle:UserAnswer')->findOneBy(array('user' => $this->_getUser(), 'question' => $question->getId() ));
        if (!$answer) {
            $answer = new UserAnswer();
        }


        // Create the Form
        $form = $this->createFormBuilder($answer)
                ->add('question', 'hidden', array('mapped' => false, 'data' => $question->getID() ))
                ->add('count_double', 'checkbox', array('label'     => 'doppelt gewichten?',
                                                        'required'  => false))
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
                ->add('explanation', 'textarea', array('required' => false))
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

        $question_del = $em->find('MadwaysKommunalomatBundle:Question', $id);

        if (!$question_del) {
            throw new NotFoundHttpException("Invalid question.");
        }

        foreach ($question_del->getPartyAnswers() as $party_answer) {
            $em->remove($party_answer);
        }
        foreach ($question_del->getUserAnswers() as $user_answer) {
            $em->remove($user_answer);
        }
        $weight = $question_del->getWeight();
        $em->remove($question_del);

        $em->flush();

        // resort all question weights
        $questions = $em->getRepository('MadwaysKommunalomatBundle:Question')->findAll();

        foreach ($questions as $question) {
            if ($question->getWeight() > $weight ) {
                $question->setWeight($question->getWeight()-1);
            }
        }

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

    /**
    * 
    * @Template()
    */
    public function resultAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if(!$id) {
            $id = $this->_getUser();
        }

        $user = $em->find('MadwaysKommunalomatBundle:User', $id);

        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }

        $parties = $em->getRepository('MadwaysKommunalomatBundle:Party')->findAll();

        $result = array();

        foreach($parties as $party) {
            $result[$party->getId()] = array('party' => $party, 'points' => 0);
        }

        $max_points = 0;

        foreach($user->getAnswers() as $answer) {
            $party_answers = $em->getRepository('MadwaysKommunalomatBundle:PartyAnswer')->findByQuestion($answer->getQuestion());
            $max_points += $answer->getCountDouble()? 4 : 2;

            foreach ($party_answers as $party_answer) {
                $result[$party_answer->getParty()->getId()]['points'] += $this->evaluateAnswer($answer->getAnswer(), $party_answer->getAnswer(), $answer->getCountDouble());
            }
        }

        if ($max_points == 0) {
            return $this->redirect($this->generateUrl('MadwaysKommunalomatBundleQuestionAnswer', array('weight' => 1 )));
        }

        usort($result, array($this, "cmp_points"));

        return array('user' => $user,
                     'results' => $result,
                     'max_points' => $max_points);

    }

    private function evaluateAnswer($user_answer, $party_answer, $double) {
        $points = 2 - abs($user_answer - $party_answer);
        return ($double? $points*2 : $points);
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

        // TODO: move to SessionController as Service class
        // TODO: add possibility to start a new Session
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        if ( !$session->has('kommunalomat_user_id') ) {
            $user = new User();
            $user->setSession( $session->getId() );
            $user->setTimeFirstOnline(new \DateTime("now"));
            $em->persist($user);
            $em->flush();
            $session->set('kommunalomat_user_id', $user->getId());
            return $user;
        }

        return $em->getReference('MadwaysKommunalomatBundle:User', $session->get('kommunalomat_user_id') );
    }

    private function cmp_points($a, $b) {
        return ($a['points'] < $b['points']);
    }
}
