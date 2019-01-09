<?php

namespace AppBundle\Entity;

use Doctrine\DBAL\Types\BooleanType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessageRepository")
 */
class Message
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     *
     * @Assert\NotBlank(message="И-мейлът е задължителен.")
     * @Assert\Email(message="Невалиден email.")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     *
     * @Assert\NotBlank(message="Полето не може да бъде празно.")
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     *
     * @Assert\NotBlank(message="Полето не може да бъде празно.")
     * @Assert\Regex(
     *     pattern="/^[\d]{10}$/",
     *     message="Номерът трябва да се състои от 10 цифри."
     * )
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     *
     * @Assert\NotBlank(message="Полето не може да бъде празно.")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime")
     */
    private $dateAdded;

    /**
     * @var BooleanType
     * @ORM\Column(name="is_new", type="boolean")
     */
    private $isNew;


    public function __construct()
    {
        $this->dateAdded = new \DateTime("now");
        $this->isNew = true;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Message
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Message
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Message
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }


    public function getDateAdded()
    {
        return $this->dateAdded;
    }


    public function setDateAdded(\DateTime $dateAdded)
    {
        $this->dateAdded = $dateAdded;
    }

    public function getFormatedDate()
    {
        $date = $this->getDateAdded();
        return date_format($date, 'Y-m-d H:i:s');
    }


    public function getisNew()
    {
        return $this->isNew;
    }


    public function setIsNew($isNew)
    {
        $this->isNew = $isNew;
    }


    public function getShortSubject()
    {
        $ful = $this->getSubject();
        $intro = $ful;
        if (strlen($ful) > 50) {

            $intro = substr($ful, 0, 50);
            $intro = $intro . "...";
        }
        return $intro;
    }
}

