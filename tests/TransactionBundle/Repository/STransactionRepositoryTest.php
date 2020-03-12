<?php
namespace tests\TransactionBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class STransactionRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }
    
    public function testGetFromTo()
    {
        $em = $this->em;
        //As setup in the fixture
        $startDate = new \DateTime("1-10-2014");
        $finalDate = new \DateTime("1-10-2014");
        
        
        $stransactions = $em->getRepository('TransactionBundle:STransaction')
                            ->getFromTo($startDate, $finalDate);
        
        $this->assertEquals(2, count($stransactions));
    }

    public function testGetforToday()
    {
        $em = $this->em;
        //Test for branch 1
        $branch = $em->getRepository('KmBundle:Branch')->find(1);
        $this->assertEquals($branch->getName(), 'BATA');

        $stransactions = $em->getRepository('TransactionBundle:STransaction')
            ->getForTodayByBranch($branch)
        ;

        $this->assertCount(1, $stransactions['results']);
        $this->assertEquals(1000, $stransactions['results'][0]->getTotalAmount());
        //$this->assertEquals(108500, $stransactions['results'][1]->getTotalAmount());
        $this->assertEquals(1000, $stransactions['sale']);
        $this->assertEquals(null, $stransactions['profit']);
        $this->assertEquals(null, $stransactions['balance']);
        $this->assertEquals(null, $stransactions['expenditure']);

        //Test for branch 2
        $branch2 = $em->getRepository('KmBundle:Branch')->find(2);
        $this->assertEquals($branch2->getName(), 'VALLEY');

        $stransactions2 = $em->getRepository('TransactionBundle:STransaction')
                             ->getForTodayByBranch($branch2);

        //$this->assertCount()

    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}