<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Formation;
use App\Entity\Produit;
use App\Entity\Inscription;
use App\Entity\Employe;
use App\Form\FormationType;
use Symfony\Component\HttpFoundation\Request;

class InscriptionController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription")
     */
    public function index()
    {
        return $this->render('inscription/index.html.twig', [
            'controller_name' => 'InscriptionController',
        ]);
    }

    /**
    * @Route("/afficheLesFormationsInscription", name="affiche_formations_inscription")
    */
    public function afficheLesFormationsInscription()
    {
        $formations = $this->getDoctrine()->getRepository(Formation::class)->findall();
        if(!$formations){
            $message = "Pas de formation, revenez plus tard";
        }
        else{
            $message = null;
        }
        return $this->render('inscription/listeformation.html.twig',array('ensFormations'=>$formations, 'message'=>$message));
    }

    /**
    * @Route("/inscriptionFormation/{id}", name="inscription_formation")
    */
    public function inscriptionFormation($id)
    {
        $formation = $this->getDoctrine()->getRepository(Formation::class)->find($id);
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($formation);
        $manager->flush();
        return $this->redirectToRoute('affiche_formations');
    }

    /**
    * @Route("/afficheLesInscriptions", name="affiche_inscription")
    */
    public function afficheLesInscriptions()
    {
        $inscriptions = $this->getDoctrine()->getRepository(Inscription::class)->findInscriptionAttente();
        if(!$inscriptions){
            $message = "Pas d'inscription, revenez plus tard";
        }
        else{
            $message = null;
        }
        return $this->render('inscription/listeInscription.html.twig',array('ensInscriptions'=>$inscriptions, 'message'=>$message));
    }

    /**
    * @Route("/accepterInscription/{id}", name="accepter_inscription")
    */
    public function AccepterInscription($id)
    {
        $inscription = $this->getDoctrine()->getRepository(Inscription::class)->find($id);
        $inscription->setStatut(1);
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('affiche_inscription');
    }
     /**
    * @Route("/supprimerInscription/{id}", name="supp_inscription")
    */
    public function SupprimerInscription($id)
    {
        $inscription = $this->getDoctrine()->getRepository(Inscription::class)->find($id);
        $inscription->setStatut(2);
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('affiche_inscription');
    }
//CASTAING ligne 103
     /**
    * @Route("/CreerInscription/{id}", name="create_inscription")
    */
    public function CreerInscription($id)
    {
        $formationId = $id;
        $formation = $this->getDoctrine()->getRepository(Formation::class)->find($id);
        $employeId = $this->get('session')->get('employeId');
        $employe = $this->getDoctrine()->getRepository(Employe::class)->find($employeId);

        $inscriptionTest = $this->getDoctrine()->getRepository(Inscription::class)->findOneBy(array('employe' => $employeId, 'formation' => $formationId));

        if($inscriptionTest == null){
            $inscription = new Inscription();
            $inscription->setStatut(0);
            $inscription->setFormation($formation);
            $inscription->setEmploye($employe);
            $em = $this->getDoctrine()->getManager();
            $em->persist($inscription);
            $em->flush();
        }
        
        
        return $this->redirectToRoute('affiche_formations_inscription');
    }
}
