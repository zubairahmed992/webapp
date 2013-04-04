<?php

namespace LoveThatFit\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * LoveThatFit\AdminBundle\Entity\SurveyQuestion
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SurveyQuestion
{    
    
    // ...

    /**
     * @ORM\OneToMany(targetEntity="SurveyAnswer", mappedBy="question")
     */
    protected $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $question
     *
     * @ORM\Column(name="question", type="text")
     */
    private $question;


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
     * Set question
     *
     * @param string $question
     * @return SurveyQuestion
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    
        return $this;
    }

    /**
     * Get question
     *
     * @return string 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Add answer
     *
     * @param LoveThatFit\AdminBundle\Entity\SurveyAnswer $answer
     * @return SurveyQuestion
     */
    public function addAnswer(\LoveThatFit\AdminBundle\Entity\SurveyAnswer $answer)
    {
        $this->answer[] = $answer;
    
        return $this;
    }

    /**
     * Remove answer
     *
     * @param LoveThatFit\AdminBundle\Entity\SurveyAnswer $answer
     */
    public function removeAnswer(\LoveThatFit\AdminBundle\Entity\SurveyAnswer $answer)
    {
        $this->answer->removeElement($answer);
    }

    /**
     * Get answer
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getAnswers()
    {
        return $this->answers;
    }
}