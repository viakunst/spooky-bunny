<?php

namespace App\Controller\Profile;

use App\Security\AuthUserProvider;
use App\Template\Annotation\MenuItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Activity controller.
 *
 * @Route("/", name="profile_")
 */
class ProfileController extends AbstractController
{
    /**
     * Lists all activities.
     *
     * @MenuItem(title="Terug naar frontend", menu="admin-profile", class="mobile")
     * @Route("/", name="index", methods={"GET"})
     */
    public function indexAction()
    {
        $personal = $this->getUser()->getPerson();

        return $this->render('profile/index.html.twig', [
            'personal' => $personal,
        ]);
    }

    /**
     * Displays a form to edit logged in person.
     *
     * @Route("/edit", name="edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, AuthUserProvider $authProvider)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $this->getUser()->getPerson();

        $form = $this->createForm('App\Form\Person\PersonType', $person, ['current_user' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $auth = $person->getAuth();
            $auth->setAuthId($authProvider->usernameHash($person->getEmail()));

            $em->flush();

            return $this->redirectToRoute('profile_index');
        }

        return $this->render('profile/edit.html.twig', [
            'person' => $person,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit logged in person.
     *
     * @Route("/update", name="update", methods={"GET", "POST"})
     */
    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $this->getUser()->getPerson();

        $form = $this->createForm('App\Form\Person\PersonUpdateType', $person, ['current_user' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirect('/');
        }

        return $this->render('profile/update.html.twig', [
            'personal' => $person,
            'form' => $form->createView(),
        ]);
    }
}
