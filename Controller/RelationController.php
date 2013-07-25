<?php

namespace Success\RelationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class RelationController extends Controller {
  
  protected $haveToPaginate = false;
  
  public function buttonsAction($id) {
    $user = $this->getEntityUser($id);
    $response = $this->render('RelationBundle:Relations:_buttons.html.twig', array('user' => $user));
    return $response;
  }
    
  public function addContactAction($id) {
    $userLogged = $this->getUser();
    if(is_null($userLogged)) {
      throw new NotFoundHttpException('Usuario no encontrado 1.');
    } else {
      $userContact = $this->getEntityUser($id);
      if (!is_object($userContact)) {
        throw new NotFoundHttpException('Usuario no encontrado 2.');
      }
    }
    
    $manager = $this->container->get('success.relation.manager');
    $manager->create($userLogged, $userContact, 'follow');

    $response = new JsonResponse(array('status' => 200), 200, array());

    return $response;
  }
  
  public function removeContactAction($id) {
    $userLogged = $this->getUser();
    if(is_null($userLogged)) {
      throw new NotFoundHttpException('Usuario no encontrado 1.');
    } else {
      $userContact = $this->getEntityUser($id);
      if (!is_object($userContact)) {
        throw new NotFoundHttpException('Usuario no encontrado 2.');
      }
    }
    
    $manager = $this->container->get('success.relation.manager');
    $manager->remove($userLogged, $userContact);

    $response = new JsonResponse(array('status' => 200), 200, array());

    return $response;
  }
  
  public function contactsAction($id) {
    $owner = $this->getEntityUser($id);
    
    $manager = $this->container->get('success.relation.manager');
    $users = $manager->getFollowings($owner, 5);

    $response = $this->render('RelationBundle:Relations:contacts.html.twig', array(
        'owner' => $owner,
        'users' => $users
    ));

    return $response;
  }

  public function myListAction() {
    $user = $this->getUser();
    $users = $this->getPager($user);

    $response = $this->render('RelationBundle:Relations:list.html.twig', array(
        'owner' => $user, 
        'users' => $users, 
        'haveToPaginate' => $this->haveToPaginate
    ));
    
    $response->setMaxAge(5 * 60);
    return $response;
  }  
  
  public function listAction($id) {
    $user = $this->getEntityUser($id);
    $users = $this->getPager($user);

    $response = $this->render('RelationBundle:Relations:list.html.twig', array(
        'owner' => $user, 
        'users' => $users, 
        'haveToPaginate' => $this->haveToPaginate
    ));

    $response->setMaxAge(5 * 60);

    return $response;
  }
  
  public function pageAction($id, $page = 1) {
    $user = $this->getEntityUser($id);
    $users = $this->getPager($user, $page);
    
    if ($this->getRequest()->isXmlHttpRequest()) {

      $html = $this->renderView('RelationBundle:Relations/Pager:_pager.html.twig', array(
          'owner' => $user,
          'users' => $users
      ));
      
      $data = array('responseCode' => 200, 'response' => $html, 'haveToPaginate' => $this->haveToPaginate);
      $response = new JsonResponse($data, 200);

    } else {

      $response = $this->render('RelationBundle:Relations:list.html.twig', array(
          'owner' => $user,
          'users' => $users,
          'haveToPaginate' => $this->haveToPaginate
      ));

    }
    $response->setMaxAge(30 * 60);
    return $response;
  }
  
  public function getEntityUser($id){
    $em = $this->getManager();
    $user = $em->getRepository('UsuarioBundle:User')->find($id);
      
    if (!$user) {
      throw new NotFoundHttpException("Usuario '" . $id . "' no encontrado");
    }
    
    return $user;
  }
  
  public function getManager() {
    return $this->getDoctrine()->getManager();
  }
  
  public function getPager($entity, $page = 1){
    $manager = $this->container->get('success.relation.manager');
    $queryBuilder = $manager->getFollowingsQuery($entity);
    
    $pager = new Pagerfanta(new DoctrineORMAdapter($queryBuilder));
    $pager->setMaxPerPage(10);
    $pager->setCurrentPage($page);
    
    $relationsResults = $pager->getCurrentPageResults();

    $collection = array();
    foreach ($relationsResults as $relation) {
      $collection[$relation->getId()] = $relation->getEntity2();
    }
    
    $this->haveToPaginate = $pager->hasNextPage();
    
    return $collection;    
  }
  
}
