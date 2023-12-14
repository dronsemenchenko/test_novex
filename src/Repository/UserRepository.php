<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private ContainerBagInterface $params)
    {
        parent::__construct($registry, User::class);
    }

    public function getSliceItems(Request $request): array|null
    {
        
        if (!$request->query->get('limit') or is_null($request->query->get('limit'))) {
            $limit = $this->params->get('paginator_limit');
        }else{
            $limit = $request->query->get('limit');
        }

        $qb = $this->createQueryBuilder('o');
        $qb->setMaxResults($limit);
        if (!is_null($request->query->get('offset'))) {
            $qb->setFirstResult($request->query->get('offset'));
        }

        return $qb->getQuery()->getResult();
    }

    public function getTotal(): int
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select('count(o.id)');
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function create(Request $request): User
    {
        $em = $this->getEntityManager();
        $data = json_decode($request->getContent(), true);

        $User = new User();

        $User->setEmail($data['email']);
        $User->setName($data['name']);
        $User->setSex($data['sex']);
        $User->setBirthday(new \DateTime($data['birthday']));
        $User->setPhone($data['phone']);

        $this->save($User, 1);
        return $User;
    }

    public function update(Request $request, User $User): User
    {
        $em = $this->getEntityManager();
        $data = json_decode($request->getContent(), true);
        
        if(isset($data['email'])){
            $User->setEmail($data['email']);
        }

        if(isset($data['name'])){
            $User->setName($data['name']);
        }

        if(isset($data['sex'])){
            $User->setSex($data['sex']);
        }

        if(isset($data['birthday'])){
            $User->setBirthday(new \DateTime($data['birthday']));
        }

        if(isset($data['phone'])){
            $User->setPhone($data['phone']);
        }

        $this->save($User, 1);
        return $User;
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
