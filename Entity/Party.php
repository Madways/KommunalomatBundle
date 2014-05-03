<?php
namespace Madways\KommunalomatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="kommunalomat_party")
 */
class Party
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     *
     * @ORM\OneToMany(targetEntity="PartyAnswer", mappedBy="party")
     */
    private $answers;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $image;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $url;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Party
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Party
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add answers
     *
     * @param \Madways\KommunalomatBundle\Entity\PartyAnswer $answers
     * @return Party
     */
    public function addAnswer(\Madways\KommunalomatBundle\Entity\PartyAnswer $answers)
    {
        $this->answers[] = $answers;

        return $this;
    }

    /**
     * Remove answers
     *
     * @param \Madways\KommunalomatBundle\Entity\PartyAnswer $answers
     */
    public function removeAnswer(\Madways\KommunalomatBundle\Entity\PartyAnswer $answers)
    {
        $this->answers->removeElement($answers);
    }

    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    private static function cmp($a, $b) {
        return ($a->getWeight() > $b->getWeight())? 1 : -1;
    }

    public function getAnswersSorted()
    {
        $arr = $this->answers->toArray();
        //print_r(usort($arr, array($this, 'cmp')));
        usort($arr, array($this, 'cmp'));
        return $arr;
    }


    /**
     * Set image
     *
     * @param string $image
     * @return Party
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Party
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }
}
