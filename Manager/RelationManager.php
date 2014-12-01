<?php

namespace Success\RelationBundle\Manager;

use Doctrine\ORM\Query;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Success\RelationBundle\Model\RelationInterface;
use Success\RelationBundle\Model\RelationManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Success\RelationBundle\Event\RelationEvent;
use Success\RelationBundle\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/*
  // CREAR RELACION
  $manager = $this->container->get('success.relation_follow.relation_manager');
  $manager->create($userLogged, $userContact, 'follow');


  // BORRAR RELACION
  $manager = $this->container->get('success.relation_follow.relation_manager');
  $manager->remove($userLogged, $userContact, 'follow');

 */

/**
 * RelationManager.
 *
 * @uses RelationManagerInterface
 * @author Gaston Caldeiro <chugas488@gmail.com>
 */
class RelationManager implements RelationManagerInterface {

  /**
   * @var ObjectManager
   */
  protected $em;
  protected $class;
  protected $relation;
  protected $dispatcher;

  /**
   * @param EntityManager $em         Entity manager service
   * @param string        $repository Repository name
   */
  public function __construct(EntityManager $em, $class, EventDispatcherInterface $dispatcher) {
    $this->em = $em;
    $this->class = $class;
    $this->dispatcher = $dispatcher;
  }

  /**
   * Returns if relation exists or not.
   *
   * @return boolean
   */
  public function exists($entity1, $entity2) {
    $this->relation = $this->getRelation($entity1, $entity2);

    return !empty($this->relation);
  }

  /**
   * Create relation.
   *
   * @return RelationInterface|null
   */
  public function create($entity1, $entity2, $name = 'follow') {
    if ($this->exists($entity1, $entity2)) {
      return true;
    }

    $class = $this->class;
    $relation = new $class();
    $relation->setName($name);
    $relation->setEntity1($entity1);
    $relation->setEntity2($entity2);

    return $this->addRelation($relation);
  }

  /**
   * Create relation.
   *
   * @return RelationInterface|null
   */
  public function remove($entity1, $entity2) {
    if (!$this->exists($entity1, $entity2)) {
      return false;
    }

    return $this->removeRelation($this->relation);
  }

  /**
   * {@inheritdoc}
   */
  public function addRelation(RelationInterface $relation) {
    $event = new RelationEvent($relation);
    $this->dispatcher->dispatch(Events::RELATION_PRE_PERSIST, $event);
    
    $this->em->persist($relation);
    $this->em->flush();

    return $relation;
  }

  public function removeRelation(RelationInterface $relation) {
    // Notificar Evento de borrado
    $event = new RelationEvent($relation);
    $this->dispatcher->dispatch(Events::RELATION_PRE_REMOVE, $event);

    $this->em->remove($relation);
    $this->em->flush();

    return true;
  }

  /**
   * {@inheritdoc}
   */
  public function getRelation($entity1, $entity2) {
    $entity_1_id = (is_object($entity1) ? $entity1->getId() : $entity1);
    $entity_2_id = (is_object($entity2) ? $entity2->getId() : $entity2);
    
    $qb = $this->__getRepository()
            ->createQueryBuilder('r')
            ->select('partial r.{id, name}')
            ->where('IDENTITY(r.entity1) = :entity1')    
            ->andWhere('IDENTITY(r.entity2) = :entity2');

    $qb->setParameters(array(
        'entity1' => $entity_1_id,
        'entity2' => $entity_2_id
    ));

    $q = $qb->getQuery();

    return $q->getArrayResult();
  }

  public function getFollowers($entity, $limit = null) {
    $q = $this->getFollowersQuery($entity);

    if (!is_null($limit)) {
      $q->setMaxResults($limit);
    }

    $relations = $q->getResult();

    $collection = new ArrayCollection();
    foreach ($relations as $relation) {
      $collection->add($relation->getEntity2());
    }

    return $collection;
  }

  public function getFollowings($entity, $limit = null, $hydration = Query::HYDRATE_OBJECT) {
    $q = $this->getFollowingsQuery($entity);

    if (!is_null($limit)) {
      $q->setMaxResults($limit);
    }

    $relations = $q->getResult();

    $collection = new ArrayCollection();
    foreach ($relations as $relation) {
      $collection->add($relation->getEntity2());
    }

    return $collection;
  }

  public function getFollowersQuery($entity) {
    $q = $this->__getRepository()
            ->createQueryBuilder('r')
            ->select('r', 'e1')
            ->innerJoin('r.entity1', 'e1')
            ->where('r.entity2 = :entity2')
            ->orderBy('r.createdAt', 'DESC');

    $q->setParameter('entity2', $entity);

    return $q->getQuery();
  }

  public function getFollowingsQuery($entity) {
    $q = $this->__getRepository()
            ->createQueryBuilder('r')
            ->select('r', 'e2')
            ->innerJoin('r.entity2', 'e2')
            ->where('r.entity1 = :entity1')
            ->orderBy('r.createdAt', 'DESC');

    $q->setParameter('entity1', $entity);

    return $q->getQuery();
  }
  
  public function getStatusOf($user_from_id, $user_to_ids) {
    $qb = $this->__getRepository()
            ->createQueryBuilder('r')
            ->select('partial r.{id, name}')
            ->where('IDENTITY(r.entity1) = :entity1');

    $qb->andWhere($qb->expr()->in('IDENTITY(r.entity2)', $user_to_ids));
    $qb->setParameter('entity1', $user_from_id);

    $q = $qb->getQuery();
    
    $q->useResultCache(true, 600, __METHOD__ . serialize($q->getParameters()));
    $q->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true);
    return $q->getArrayResult();
  }

  /**
   * Get entity repository.
   *
   * @return EntityRepository
   */
  protected function __getRepository() {
    return $this->em->getRepository($this->class);
  }

}
