<?php

namespace Madways\KommunalomatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Madways\KommunalomatBundle\Entity\Party;
use Madways\KommunalomatBundle\Entity\Question;
use Madways\KommunalomatBundle\Entity\PartyAnswer;

class PartyController extends Controller
{
    /**
    * @Template()
    */
    public function overviewAction() 
    {

        // TODO: Check if all answers were given (count_question*count_parties=count_PartyAnswers)
        $parties = $this->getDoctrine()->getRepository('MadwaysKommunalomatBundle:Party')->findAll();;

        if (!$parties) {
            return $this->redirect($this->generateUrl('MadwaysKommunalomatBundlePartyCreate'));
        }

        return array('parties' => $parties);
    }

    /**
    * @Template()
    */
    public function indexAction($id) 
    {

        $em = $this->getDoctrine()->getManager();

        $party = $em->find('MadwaysKommunalomatBundle:Party', $id);

        if (!$party) {
            throw new NotFoundHttpException("Invalid party.");
        }

        return array('party' => $party,
                    'answers' => $party->getAnswersSorted());
    }

    /**
    * @Template()
    */
    public function formAction(Request $request, $id = NULL)
    {
        $em = $this->getDoctrine()->getManager();

        if (isset($id)) {
            $party = $em->find('MadwaysKommunalomatBundle:Party', $id);

            if (!$party) {
                throw new NotFoundHttpException("Invalid party.");
            }
        } else {
            $party = new Party();
        }

        $form = $this->createFormBuilder($party)
                ->add('name', 'text')
                ->add('description')
                ->add('image', 'text')
                ->add('url', 'text')         // TODO: make it possible to upload a picture
                ->add('save', 'submit')
                ->getForm();



        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($party);
            $em->flush();

            return $this->redirect($this->generateUrl("MadwaysKommunalomatBundlePartyOverview"));
        }

        return array('form' => $form->createView());
    }

    /**
    * 
    * @Template()
    */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $party = $em->find('MadwaysKommunalomatBundle:Party', $id);

        if (!$party) {
            throw new NotFoundHttpException("Invalid Party.");
        }

        $answers = $em->getRepository('MadwaysKommunalomatBundle:PartyAnswer')->findByParty($party);

        foreach($answers as $answer) {
            $em->remove($answer);
        }
        $em->remove($party);
        $em->flush();

        return $this->redirect($this->generateUrl('MadwaysKommunalomatBundlePartyOverview'));
    }

    /**
    * @Template()
    */
    public function answerAction($id, $weight, Request $request) 
    {

        $em = $this->getDoctrine()->getManager();

        $party = $em->find('MadwaysKommunalomatBundle:Party', $id);

        if (!$party) {
            throw new NotFoundHttpException("Invalid Party.");
        }

        $em = $this->getDoctrine()->getManager();

        // Find the question by weight
        $question = $em->getRepository('MadwaysKommunalomatBundle:Question')->findOneByWeight($weight);
        $question_count = $em->getRepository('MadwaysKommunalomatBundle:Question')->getCount();

        if (!$question) {
            return $this->redirect($this->generateUrl('MadwaysKommunalomatBundlePartyOverview'));
        }

        // Find already given answer
        $answer = $em->getRepository('MadwaysKommunalomatBundle:PartyAnswer')->findOneBy(array('party' => $party, 'question' => $question));
        if (!$answer) {
            $answer = new PartyAnswer();
        }

        $form = $this->createFormBuilder($answer)
                ->add('question', 'hidden', array('mapped' => false, 'data' => $question->getID() ))
                ->add('explanation', 'text', array('required'  => false))
                /*->add('answer', 'choice', 
                    array('choices' => array('0' => 'approve', 
                                            '1' => 'neutral', 
                                            '2' => 'disapprove'),
                            'multiple' => false,
                            'expanded' => true ))
                ->add('submit', 'submit')*/
                ->add('approve', 'submit', array('label'  => 'Zustimmung'))
                ->add('neutral', 'submit', array('label'  => 'Neutral'))
                ->add('disapprove', 'submit', array('label'  => 'Ablehnung'))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $answer->setQuestion($em->getReference('MadwaysKommunalomatBundle:Question', $form->get('question')->getData() ));

            // set user by id
            $answer->setParty($party);

            $answer->setAnswerAsString($form->getClickedButton()->getName());

            $em->persist($answer);
            $em->flush();

            return $this->redirect($this->generateUrl('MadwaysKommunalomatBundlePartyAnswer', array('id' => $party->getId(), 'weight' => $weight+1 )));
        }

        return array('party' => $party,
                     'question' => $question,
                     'form' => $form->createView(),
                     'question_count' => $question_count);
        
    }
}
