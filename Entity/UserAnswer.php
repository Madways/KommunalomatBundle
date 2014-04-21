<?php
namespace Madways\KommunalomatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="kommunalomat_user_answer")
 * 
 * This table maps the selected value for an answer to the user 
 */
class UserAnswer
{

    /** 
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="User") 
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false) 
     */
    protected $user;

    /** 
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Question") 
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=false) 
     */
    protected $question;

    /**
     * @ORM\Column(type="integer")
     *
     * this should be some kind of enum field (http://stackoverflow.com/questions/14933228/how-to-generate-entities-and-schemas-for-enum-in-symfony)
     * for now i set 0=approve, 1= neutral, 2=disapprove
     * this may confuse in aspect to the points calculation
     */
    protected $answer;


    /**
     * Set answer
     *
     * @param integer $answer
     * @return UserAnswer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Set answer as string
     *
     * @param string $answer
     * @return UserAnswer
     */
    public function setAnswerAsString($answer)
    {
        switch ($answer) {
            case "approve":
                $this->answer = 0;
                break;
            case "disapprove":
                $this->answer = 2;
                break;
            default:
                $this->answer = 1;
        }

        return $this;
    }

    /**
     * Get answer
     *
     * @return integer 
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return UserAnswer
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set question
     *
     * @param Question $question
     * @return UserAnswer
     */
    public function setQuestion(Question $question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return Question 
     */
    public function getQuestion()
    {
        return $this->question;
    }
}
