<?php
 
namespace AppBundle\Entity;
 
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
 
/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BugRepository")
 * @ORM\Table(name="bug")
 */
class Bug
{
    const STATUS_OPEN = "OPEN";
    const STATUS_CLOSE = "CLOSE";
    const STATUSES = [self::STATUS_OPEN, self::STATUS_CLOSE];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;
 
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $description;
 
    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $created;
 
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $status;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="assignedBugs")
     * @var User
     */
    protected $engineer;
 
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reportedBugs")
     * @var User
     */
    protected $reporter;
 
    /**
     * @ORM\ManyToMany(targetEntity="Product")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\Count(min="1")
     * @var Product[]
     */
    protected $products = null;
     
     
    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

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
     * Set description
     *
     * @param string $description
     *
     * @return Bug
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Bug
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Bug
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set engineer
     *
     * @param \AppBundle\Entity\User $engineer
     *
     * @return Bug
     */
    public function setEngineer(\AppBundle\Entity\User $engineer = null)
    {
        $this->engineer = $engineer;

        return $this;
    }

    /**
     * Get engineer
     *
     * @return \AppBundle\Entity\User
     */
    public function getEngineer()
    {
        return $this->engineer;
    }

    /**
     * Set reporter
     *
     * @param \AppBundle\Entity\User $reporter
     *
     * @return Bug
     */
    public function setReporter(\AppBundle\Entity\User $reporter = null)
    {
        $this->reporter = $reporter;

        return $this;
    }

    /**
     * Get reporter
     *
     * @return \AppBundle\Entity\User
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Add product
     *
     * @param \AppBundle\Entity\Product $product
     *
     * @return Bug
     */
    public function addProduct(\AppBundle\Entity\Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \AppBundle\Entity\Product $product
     */
    public function removeProduct(\AppBundle\Entity\Product $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    public function close()
    {
        $this->setStatus(self::STATUS_CLOSE);
    }
 
    public function isClose()
    {
        return $this->getStatus() == self::STATUS_CLOSE;
    }
}