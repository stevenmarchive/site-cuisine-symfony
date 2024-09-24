<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Affiche une liste paginée des ingrédients.
 *
 * Cette méthode est accessible via la route '/ingredient' et ne répond qu'aux requêtes HTTP GET. Elle utilise 
 * un service de pagination pour afficher une liste d'ingrédients récupérée depuis le repository. La pagination 
 * est gérée via les paramètres de la requête, avec une limite de 10 résultats par page.
 *
 * @param IngredientRepository $repository Le repository utilisé pour récupérer les ingrédients.
 * @param PaginatorInterface $paginator Le service utilisé pour paginer les résultats.
 * @param Request $request L'objet de requête HTTP contenant les paramètres de pagination.
 *
 * @return Response Retourne la vue Twig 'page/ingredient/index.html.twig' avec la liste des ingrédients paginée.
 */
class IngredientController extends AbstractController
{
    // Annotation pour la route '/ingredient' qui accepte uniquement les requêtes HTTP GET et définit le nom de route 'ingredient'
    #[Route('/ingredient', name: 'ingredientIndex', methods: ['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        // Récupère la liste complète des ingrédients avec findAll(), puis récupère le numéro de page via la requête (par défaut page 1), et limite à 10 résultats par page
        $ingredients = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        // Retourne la vue Twig 'page/ingredient/index.html.twig' en passant les ingrédients paginés dans un tableau
        return $this->render('page/ingredient/index.html.twig', ['ingredients' => $ingredients]);
    }


    /**
     * Gère la création d'un nouvel ingrédient.
     *
     * Cette méthode est accessible via la route '/ingredient/nouveau' et prend en charge les requêtes HTTP GET et POST.
     * Elle permet d'afficher un formulaire pour la création d'un ingrédient et de traiter la soumission de ce formulaire.
     * Si le formulaire est soumis et valide, l'ingrédient est enregistré dans la base de données, un message de succès est 
     * affiché, et l'utilisateur est redirigé vers la liste des ingrédients.
     *
     * @param EntityManagerInterface $manager L'entity manager pour persister et enregistrer l'ingrédient dans la base de données.
     * @param Request $request L'objet de requête HTTP contenant les données du formulaire.
     *
     * @return Response Retourne la vue Twig 'page/ingredient/nouveau.html.twig' avec le formulaire de création d'ingrédient.
     */
    #[Route('/ingredient/nouveau', name: 'ingredientNouveau', methods: ['GET', 'POST'])]
    public function nouveau(EntityManagerInterface $manager, Request $request): Response
    {
        // Crée une nouvelle instance de l'entité Ingredient
        $ingredient = new Ingredient();

        // Crée un formulaire basé sur la classe IngredientType, associé à l'entité Ingredient créée
        $form = $this->createForm(IngredientType::class, $ingredient);

        // Traite la requête HTTP pour vérifier si le formulaire a été soumis
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et si les données sont valides
        if ($form->isSubmitted() && $form->isValid()) {

            // Récupère les données soumises du formulaire
            $ingredient = $form->getData();

            // Persiste l'objet Ingredient dans la base de données
            $manager->persist($ingredient);

            // Exécute les opérations en attente dans la base de données (enregistre les changements)
            $manager->flush();

            // Ajoute un message flash pour informer l'utilisateur que l'ingrédient a été créé avec succès
            $this->addFlash('success', 'Votre ingrédient a été crée avec succès.');

            // Redirige l'utilisateur vers la route 'ingredient_index' (la liste des ingrédients)
            return $this->redirectToRoute('ingredientIndex');
        }

        // Rend la vue Twig du formulaire pour la 0création d'un nouvel ingrédient
        return $this->render('page/ingredient/nouveau.html.twig', ['form' => $form->createView()]);
    }


    /**
     * Gère la modification d'un ingrédient existant.
     *
     * Cette méthode est accessible via la route '/ingredient/modification/{id}' et prend en charge les requêtes HTTP GET et POST.
     * Elle permet d'afficher un formulaire de modification d'un ingrédient et de traiter la soumission de ce formulaire.
     * Si le formulaire est soumis et valide, les modifications sont enregistrées dans la base de données, un message de succès
     * est affiché, et l'utilisateur est redirigé vers la liste des ingrédients.
     *
     * @param int $id L'identifiant de l'ingrédient à modifier.
     * @param IngredientRepository $repository Le repository utilisé pour récupérer l'ingrédient à modifier.
     * @param Request $request L'objet de requête HTTP contenant les données du formulaire.
     * @param EntityManagerInterface $manager L'entity manager pour persister et enregistrer les modifications dans la base de données.
     *
     * @return Response Retourne la vue Twig 'page/ingredient/modification.html.twig' avec le formulaire de modification de l'ingrédient.
     */
    #[Route('/ingredient/modification/{id}', name: 'ingredientModification', methods: ['GET', 'POST'])]
    public function modification(int $id, IngredientRepository $repository, Request $request, EntityManagerInterface $manager): Response
    {
        // Récupère l'ingrédient correspondant à l'identifiant donné en utilisant le repository
        $ingredient = $repository->findOneBy(['id' => $id]);

        // Crée un formulaire basé sur la classe IngredientType, lié à l'entité Ingredient récupérée
        $form = $this->createForm(IngredientType::class, $ingredient);

        // Traite la requête HTTP pour vérifier si le formulaire a été soumis
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et si les données sont valides
        if ($form->isSubmitted() && $form->isValid()) {

            // Récupère les données soumises du formulaire (l'objet Ingredient modifié)
            $ingredient = $form->getData();

            // Persiste l'entité Ingredient modifiée pour préparer sa sauvegarde
            $manager->persist($ingredient);

            // Exécute les opérations en attente dans la base de données (enregistre les modifications)
            $manager->flush();

            // Ajoute un message flash pour informer l'utilisateur que la modification a été effectuée avec succès
            $this->addFlash('success', 'Votre ingrédient a été modifier avec succès.');

            // Redirige l'utilisateur vers la liste des ingrédients après modification
            return $this->redirectToRoute('ingredientIndex');
        }

        // Rend la vue Twig avec le formulaire de modification d'ingrédient
        return $this->render('page/ingredient/modification.html.twig', ['form' => $form->createView()]);
    }


    /**
     * Supprime un ingrédient de la base de données.
     *
     * Cette méthode récupère un ingrédient via son identifiant, le supprime
     * et applique les changements en base de données. Elle gère également
     * les cas où l'ingrédient n'est pas trouvé et ajoute des messages flash
     * pour informer l'utilisateur du résultat de l'opération.
     *
     * @param int $id L'identifiant de l'ingrédient à supprimer.
     * @param IngredientRepository $repository Le repository utilisé pour récupérer l'ingrédient.
     * @param Request $request La requête HTTP en cours (non utilisée dans cette méthode).
     * @param EntityManagerInterface $manager L'EntityManager permettant de gérer la suppression de l'ingrédient.
     *
     * @return Response Redirige vers la liste des ingrédients après la suppression.
     */
    #[Route('/ingredient/suppression/{id}', name: 'ingredientDelete', methods: ['GET'])]

    // Fonction publique pour la suppression d'un ingrédient, prenant en paramètres l'id, le repository, la requête et l'EntityManager
    public function suppression(int $id, IngredientRepository $repository, Request $request, EntityManagerInterface $manager): Response
    {

        // Récupère l'ingrédient correspondant à l'identifiant donné en utilisant le repository
        $ingredient = $repository->findOneBy(['id' => $id]);

        // Vérifie si l'ingrédient n'a pas été trouvé
        if (!$ingredient) {

            // Ajoute un message flash indiquant que l'ingrédient n'a pas été trouvé
            $this->addFlash('success', "L'ingrédient en question n'a pas été trouvé");
        }

        // Supprime l'ingrédient trouvé à l'aide du manager
        $manager->remove($ingredient);

        // Applique les changements en base de données
        $manager->flush();

        // Ajoute un message flash indiquant que l'ingrédient a été modifié avec succès (Erreur de message ici, il s'agit d'une suppression)
        $this->addFlash('success', 'Votre ingrédient a été modifier avec succès.');

        // Redirige vers la route de la liste des ingrédients après la suppression
        return $this->redirectToRoute('ingredientIndex');
    }
}
