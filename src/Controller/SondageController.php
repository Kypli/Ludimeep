<?php

namespace App\Controller;

use App\Entity\Sondage;
use App\Form\SondageType;
use App\Repository\SondageRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/sondage", name="sondage")
 */
class SondageController extends AbstractController
{
    /**
     * @Route("/", name="", methods={"GET"})
     */
    public function index(SondageRepository $sondageRepository): Response
    {
        return $this->render('sondage/index.html.twig', [
            'sondages' => $sondageRepository->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/add", name="_add", methods={"GET", "POST"})
     */
    public function add(Request $request, SondageRepository $sr): Response
    {
        $sondage = new Sondage();
        $form = $this->createForm(SondageType::class, $sondage);
        $form->handleRequest($request);
        $sondage = $form->getData();

        $limiteMax = $sr->countSondageRunning() >= 2
            ? true
            : false
        ;

        if ($form->isSubmitted() && $form->isValid()){

            // Start < End
            if ($sondage->getStart() >= $sondage->getEnd()){
                $this->addFlash('error', "La date de départ doit être inférieur à la date de fin.");
                return $this->redirectToRoute('sondage', [], Response::HTTP_SEE_OTHER);
            }

            // 2 sondages en cours max
            if ($limiteMax){
                $this->addFlash('error', "Il ne peut y avoir que 2 sondages en cours maximum.");
                return $this->redirectToRoute('sondage', [], Response::HTTP_SEE_OTHER);
            }

            $sr->add($sondage, true);

            return $this->redirectToRoute('sondage', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sondage/add.html.twig', [
            'form' => $form,
            'sondage' => $sondage,
            'limiteMax' => $limiteMax,
        ]);
    }

    /**
     * Affiche un sondage
     */
    public function show(Sondage $sondage): Response
    {
        return true;
    }

    /**
     * @Route("/result/{id}", name="_result", options={"expose"=true})
     * Envoie les datas d'un sondage
     */
    public function result(Sondage $sondage, SondageRepository $sr, Request $request): Response
    {
        // Control request
        if (!$request->isXmlHttpRequest()){ throw new HttpException('500', 'Requête ajax uniquement'); }

        // Couleur ['Rouge', 'Bleu', 'Yellow', 'Turquoise', 'Purple', 'Orange', 'Vert', 'Rose'];
        $datas = [];
        $labels = [];

        for($i = 1; $i <= 8; $i++){

            $label = 'getLine'.$i;

            if (!empty($sondage->$label())){
                $labels[] = $sondage->$label();
                $data = 'getResult'.$i;
                $datas[] = $sondage->$data();
            }
        }

        return new JsonResponse([
            'datas' => $datas,
            'labels' => $labels,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/edit", name="_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Sondage $sondage, SondageRepository $sondageRepository): Response
    {
        $form = $this->createForm(SondageType::class, $sondage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sondageRepository->add($sondage, true);

            return $this->redirectToRoute('sondage', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sondage/edit.html.twig', [
            'form' => $form,
            'sondage' => $sondage,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="_delete", methods={"POST"})
     */
    public function delete(Request $request, Sondage $sondage, SondageRepository $sondageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sondage->getId(), $request->request->get('_token'))) {
            $sondageRepository->remove($sondage, true);
        }

        return $this->redirectToRoute('sondage', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/getVotantsBySondageId/{id}", name="_getVotantsBySondageId")
     */
    public function getVotantsBySondageId(Sondage $sondage, SondageRepository $sr)
    {
        if (empty($sondage)){
            return new Response(0);
        }

        $result = 0;
        $array = $sr->getVotantsBySondageId($sondage->getId());

        if (isset($array[0])){
            foreach($array[0] as $value){
                if (!empty($value)){
                    $result += (int) $value;
                }
            }
        }

        return new Response($result);
    }
}
