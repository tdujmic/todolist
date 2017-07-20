<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todolist;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;






/**
 * Todolist controller.
 *
 * @Route("todolist")
 */
class TodolistController extends Controller
{
    /**
     * Lists all todolist entities.
     *
     * @Route("/", name="todolist_index")
     * @Method("GET")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $todolists = $em->getRepository('AppBundle:Todolist')->findAll();

        return $this->render('todolist/index.html.twig', array(
            'todolists' => $todolists,
        ));
    }

    /**
     * Creates a new todolist entity.
     *
     * @Route("/new", name="todolist_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $todolist = new Todolist();
        $form = $this->createForm('AppBundle\Form\TodolistType', $todolist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($todolist);
            $em->flush();

            return $this->redirectToRoute('todolist_show', array('id' => $todolist->getId()));
        }

        return $this->render('todolist/new.html.twig', array(
            'todolist' => $todolist,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a todolist entity.
     *
     * @Route("/{id}", name="todolist_show")
     * @Method("GET")
     */
    public function showAction(Todolist $todolist)
    {
        $deleteForm = $this->createDeleteForm($todolist);

        return $this->render('todolist/show.html.twig', array(
            'todolist' => $todolist,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing todolist entity.
     *
     * @Route("/{id}/edit", name="todolist_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Todolist $todolist)
    {
        $deleteForm = $this->createDeleteForm($todolist);
        $editForm = $this->createForm('AppBundle\Form\TodolistType', $todolist);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('todolist_edit', array('id' => $todolist->getId()));
        }

        return $this->render('todolist/edit.html.twig', array(
            'todolist' => $todolist,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a todolist entity.
     *
     * @Route("/{id}", name="todolist_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Todolist $todolist)
    {
        $form = $this->createDeleteForm($todolist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($todolist);
            $em->flush();
        }

        return $this->redirectToRoute('todolist_index');
    }

    /**
     * Creates a form to delete a todolist entity.
     *
     * @param Todolist $todolist The todolist entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Todolist $todolist)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('todolist_delete', array('id' => $todolist->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
