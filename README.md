# Documentation projet TODO

## Introduction

Dans cette documentation, nous allons parler d'une application de todo.
C'est à dire que les utilisateurs du site pourront créer des tâches et indiquer si elles sont finies ou non.

### Installation du projet

- Cloner le projet dans un nouveau dossier depuis le github.
- Rendez-vous en ligne de commande dans le dossier contenant le projet.
- Exécutez la commande `composer install` afin d'installer toutes les dépendances / librairies nécessaires au projet.
- Allumez votre base de données si ce n'est pas fait et exécutez la commande `php bin/console doctrine:database:create` suivi de la commande `php bin/console doctrine:migrations:migrate` afin de créer la base de données et de la remplir des entités voulues.
- Vous pouvez ensuite exécutez la commande `php bin/console doctrine:fixtures:load` afin de générer un set de données de test pour l'application.
- Pour lancer l'application : `symfony server:start`, pour se connecter en admin, rendez-vous sur la page de connexion : [http://localhost:8000/login](http://localhost:8000/login), si vous avez utiliser les fixtures, alors l'identifiant est adminUser@test.com et le mot de passe 123456
:warning: Ce sont des données de test il est important de les changer afin d'avoir des données plus sécurisées lors de la mise en production.

## I - Création d'une nouvelle page

### 1) Créer une page dans le code

- Pour créer une nouvelle page, il faut vous rendre dans le controller correspondant.
    - S'il n'y a aucun controller correspondant, il vous faut le créer dans le dossier controller avec le bon namespace et le nom de classe qui correspond au nom du fichier.
    - N'oubliez pas d'étendre l'`AbstractController` afin d'avoir accès à toutes les fonctionnalités qu'il contient
- Une fois que vous êtes dans le bon controller, vous devez créer une nouvelle fonction en indiquant la route en annotation au dessus :

```php
#[Route("/", name:"task_list")]
public function index () {
    //code de votre fonction
}
```

Vous pourrez simplifier le chemin de votre route en spécifiant une route au dessus de la classe :

```php
#[Route("/task", name:"app_task_")]
public class TaskController extends AbstractController{

    #[Route("/create", name:"create")]
    public function new () {
        //Code de votre fonction
    }
}
```

Ce qui donne comme chemin pour y accéder : `/task/create` et comme nom : `app_task_create`.

Si vous souhaitez ajouter des paramètres dans votre route, *par exemple pour des id*, il vous suffit de mettre le paramètre entre accolades ex : `/maRoute/{id}`. Vous pourrez ensuite récupérer directement l'entité attachée à cet id dans les paramètres de votre fonction :

```php
#[Route("/{id}/edit", name:"edit")]
public function edit(Task $task) {
    //Code de votre fonction
}
```

Dans l'exemple ci-dessus, vous pouvez voir que nous avons fait appel à l'entité `Task` qui correspond aux tâches crées par les utilisateurs. Grâce à ce paramètre, vous allez pouvoir récupérez la tâche correspondante et faire le traitement que vous voulez dans le code de la fonction.

### 2) Afficher une page

Maintenant que vous avez créer votre fonction, il vous faut quand même afficher quelque chose à la sortie (sauf si ce n'est que du code exécutif bien sûr).

Pour afficher une page, nous utilisons le framework qui est fourni avec Symfony : [Twig](https://twig.symfony.com/).

Pour créer votre nouvelle page, vous allez vous rendre dans le dossier `templates`, puis vous choisirez le sous-dossier correspondant ou créerez un sous-dossier pertinent dans le cas où votre page ne rentrerez dans aucun sous-dossier existant. Votre fichier doit avoir la terminaison : `.html.twig`

Dans votre fichier, il y a quelque règles à respecter. Si vous voulez créer des blocs, vérifiez qu'ils n'existent pas déjà dans le fichier `base.html.twig`, fichier qui contient du code commun à toutes les pages. Toutes les pages sont étendues de ce fichier afin d'avoir un style commune ou encore des éléments communs (notamment la navbar).

Donc votre fichier devra contenir au moins ces éléments :

```twig
{% extends 'base.html.twig' %}

{% block body %}

<!-- Votre code HTML -->

{% endblock %}
```

Comme dit précédemment, vous pourrez ajouter de nouveaux blocs dans votre fichier en utilisant soit ceux déjà existants dans le fichier de base ou en créeant de nouveaux blocs.

Pour afficher votre fichier, vous devez le `render` dans votre fonction crée précédemment :

```php
return $this->render('votreDossier/votreFichier.html.twig');
```

:warning: **Attention** : tout code executé après le return ne sera pas lu car les informations seront déjà envoyées. Vérifiez donc où est ce que vous placez le return (en général à la fin ou pour finir le code si une condition est remplie)

Il est possible de passer des paramètres à votre fichier, ce qui peut être pratique pour afficher des informations que vous avez récupéré grâce au code. Dans l'exemple ci-dessous, je vais montrer comment afficher une tâche en fonction de son id :

#### Fichier php <a name="afficher-une-tache"></a>

```php
//Dans le fichier TaskController

#[Route("/{id}", name:"show")]
public function show (Task $task) {
    return $this->render('task/show.html.twig', ["task" => $task]);
}
```

#### Fichier Twig

```twig
<!-- Dans le fichier twig -> le bloc body -->

<h1>Nom de la tâche : {{ task.name }}</h1>
<p>Description de la tâche : {{ task.description }}</p>
```

Vous pouvez passer plusieurs paramètres dans le render car il s'agit d'un array, vous pourrez ainsi récupérer plusieurs informations si besoin.

Pour plus d'informations sur twig, je vous renvois à sa [documentation](https://twig.symfony.com/doc/). Vous verrez que vous pouvez aussi utiliser des conditions dans le rendu ou encore accéder à l'utilisateur qui est connecté. C'est un outil bien pratique.

## II- Création d'une nouvelle entité

Pour créer une nouvelle entité dans symfony, vous allez utiliser les lignes de commande.
> Avant toute chose, il faut penser à require le maker: `composer require --dev symfony/maker-bundle`
>`--dev` permet d'utiliser cette librairie uniquement en environnement de développement et non pas de production

Pour ce faire, rendez-vous dans le dossier contenant votre projet Symfony et faite : `php bin/console make:entity`
Plusieurs questions vous seront ensuite posées afin de crée les propriétés de votre nouvelle entité.

- Vous allez commencer par choisir le nom de votre entité.
- Le maker vous demandera ensuite de renseigner les propriétés que doit contenir votre entité.
    - Vous commencez par le nom
      - :warning: **Il n'y a pas besoin de créer une propriété pour un id / une clé primaire, symfony le fait automatiquement**
    - Ensuite il y a le type (`int`, `string`, `bool`...)
        > si vous ne savez pas quel type mettre, mettez un `?`, et la liste de tous les types disponibles apparaîtra.
    - Vous pouvez choisir ensuite quelque paramètre relatifs au type que vous avez choisi, (par exemple la taille de la chaîne de ca ractère si vous avez choisi un string).
    - Le maker vous demandera ensuite si vous souhaitez que cette propriété puisse être null dans la base de donnée ou si elle doit toujours être égale à quelque chose (même si c'est 0 ou une chaîne de caractère vide).
- Une fois que tous les paramètres de la propriété sont remplis, vous pouvez entrer le nom d'une nouvelle propriété si vous en avez besoin d'autres ou juste appuyer sur entrer lorsque vous devez choisir le nom de la nouvelle propriété.
- Si vous êtes en local, vérifier que votre serveur SQL soit bien allumé
    > avec Xampp, il suffit de cliquer sur le start à côté de MySQL.
- Ensuite il y a d'autres commandes à entrer : `php bin/console make:migration`.
    - Cette commande permet à Symfony de préparer toutes les requêtes dont aura besoin la base de donées pour créer l'entité que vous venez de choisir. Vous pouvez retrouver le fichier qui a été crée par cette commande dans le dossier `migration` qui est à la racine.
- Enfin il reste : `php bin/console doctrine:migrations:migrate`.
    - Cette dernière commande permet d'éxecuter toutes les requêtes que vous avez pu préparer.
        > En effet, il est possible de préparer l'insertion de plusieurs entités en une seule migration.
        - Il se peut que certaines requêtes générent des erreurs lors de leurs exécutions, dès fois il suffit de commenter certaines lignes qui peuvent poser problèmes et le tour est joué. (Votre base de données n'en sera que peu impactées, car ce sont souvent d'anciennes requêtes qui peuvent poser problèmes.)

Une fois que tout est fait, vous pouvez vous rendre dans votre base de donnéees (en ligne de commande, via PHPMyAdmin ou une extension de votre éditeur de code peut importe) et vous pourrez observer qu'une nouvelle table est apparu, portant le nom de votre entité, et que chaque propriété est une colonne de cette table.

**Pour l'édition d'entités déjà existantes, c'est très simple :**

- Vous commencez de la même manière, en écrivant `php bin/console EntiteAModifier`. Ce coup-ci on rajoute juste le nom de l'entité que l'on veut modifier juste après
    > A noter que l'on peut créer une nouvelle entité en renseignant le nom directement à côté de la commande comme fait ici.

Vous n'avez plus qu'à rajouter les champs dont vous avez besoin.

Certaines éditions ne se font pas par ligne de commande mais vous allez devoir modifier le fichier php de l'entité directement :

- Pour ce faire, rendez-vous dans le fichier de l'entité que vous souhaitez modifier. Par exemple, je vais prendre `Task.php`.
- On peut retrouver diverses annotations au dessus des propriété de l'entité :

```php
//Indique que la propriété title à une longueur max de 255 caractères
    #[ORM\Column(length: 255)]
    private ?string $title = null;
```

Ceci permettra de générer une erreur si l'on essaie de mettre un titre qui fait plus de 255 caractère et ce sans que ce ne soit un problème SQL, Symfony permet de faire la vérification avant. Ce qui rajoute une sécurité.

```php
    public function __construct()
    {
        $this->created_at = new DateTimeImmutable();
        $this->isDone = false;
        $this->categories = new ArrayCollection();
    }
```

On peut aussi remarquer la fonction construct qui nous permet de mettre les valeurs par défaut que l'on souhaite dans notre entité.
    > Pour rappel, la fonction construct est appelée lorsque nous créeons une nouvelle instance de l'objet

```php
    $task = new Task()
```

Ce qui peut être bien utile, pour des propriété qu'on ne veut pas avoir à spécifier à chaque fois, ou alors pour des propriétés qui ne doivent pas être accessibles lors de la création par une personne ne possédant pas les droits.*

## III - Fonctionnement de la base de donnée

La base de donnée de Symfony, repose sur doctrine ORM ("Object-Relational Mapping"), un outil qui permet de faciliter la communication entre le code et la base de données en convertissant les objets de la base de données en objet php.
Grâce à cela, il n'y a pas besoin d'écrire de requête SQL dans le code car les entités sont des objets qui ont des fonctions associés permettant d'effectuer des actions dessus.

Nous avons déjà vu comment créer une entité dans la base de donnés. Nous allons maintenant voir comment nous pouvons récupérer ces données ou une partie grâce au **repository**.

Nous allons reprendre notre controller du début mais ce coup-ci, nous allons afficher toutes les tâches que nous avons au lieu de n'en [afficher qu'une](#afficher-une-tache).

Pour ce faire, nous allons créer une nouvelle fonction qui va avoir certains paramètres :

```php
#[Route("/", name:"show_all")]
public function showAll(TaskRepository $taskRepository) {
    //code de la fonction
}
```

Nous avons déjà vu que mettre un paramètre comme ceci, nous permet d'accéder à une des instances d'un objet. Ici nous allons pouvoir accéder au repository de nos tâches.

**Une entité est composé de 3 éléments :**

- L'entité `Task.php` qui contiendra toutes les propriétés de l'entité et les fonctions qui sont propres à ces propriétés.
- Le controller `TaskController.php` qui contiendra toutes les routes relatives à notre entité et qui permettra ensuite de faire les actions voulues sur l'entité ou afficher les pages en lien avec cette entité.
- Le repository `TaskRepository.php` qui contiendra le lien entre l'entité et la base de donnée. C'est dedans que nous pourrons sauvegarder l'entité dans la base de donées, faire des fonctions qui permettent la sélection précise de l'entité que l'on veut (par exemple pour faire de la pagination ou autre).

Donc avec ce paramètre est cet objet, nous allons pouvoir sélectionner tous les objets de notre entité :

```php
#[Route("/", name:"show_all")]
public function showAll(TaskRepository $taskRepository) {
    $tasks = $taskRepository->findAll();
    return $this->render('task/showAll.html.twig', ["task" => $tasks]);
}
```

Et voilà tout simple non ? en même temps notre requête n'était pas compliqué.
Cette fonction est présente de base dans le repository, nous n'avons pas besoin de la créer.

Si vous avez besoin de créer des fonctions particulières de sélection, je vous conseille de le faire dans le repository afin de garder votre projet organisé au possible.

Nous allons voir un exemple, avec de la pagination pour récupérer les tâches :

```php
//Dans notre repositoy, nous allons créer la fonction dont nous avons besoin

public function getPaginatedTask (int $limit, int $page) {
    $query = $this->createQueryBuilder("t")
    ->orderBy("t.created_at")
    ->setFirstResult(($page * $limit) - $limit)
    ->setMaxResults($limit);
    return $query->getQuery()->getResult();
}
```

Alors il y a plusieurs choses à dire sur cette fonction. Tout d'abord, on peut voir qu'elle prend deux paramètres. La limite et la page (qui nous sert à créer l'offset). Si vous êtes familier avec MySQL, cela doit vous rappeler des souvenirs.

- La limit permet de savoir combien d'éléments nous voulons récupérer
- La page permet de savoir à quelle page nous en sommes afin de calculer l'offset pour commencer à récupérer les données

la première ligne de cette fonction est une requête SQL un peu spéciale, car au lieu de l'écrire comme nous aurions eu l'habitude de faire en temps normal, nous allons la "composer" avec Doctrine.

- On commence par notre SELECT qui prend la forme de `createQueryBuilder()`. Dans lequel nous passons le paramètre comme quoi nous voulons les tâches `t`.
- Nous allons ensuite dire que nous voulons ordonner les tâches par ordre de création `t.created_at` (ce n'est pas obligatoire, mais ça permet de voir un autre paramètre que nous pouvons passer dans cette fonction).
    > created_at est une propriété de l'entité tâche. C'est donc une des colonnes dans la base de données.
- Ensuite, nous calculons l'offset, c'est-à-dire, à partir de quelle tâche est ce que nous sommes sur la bonne page.
    > pous ce faire, nous savons qu'il y a par exemple 10 tâches par page. si nous sommes sur la page 2, nous voulons donc commencer à la tâche n° 2 * 10 - 10 à savoir la tâche 10.
- Vient ensuite la limit qui nous permet de savoir combien de tâches récupérer.
    > pour poursuivre l'explication, nous allons donc récupérer les tâches 10 à 19.
    > la première page permet de récupérer les tâches de 0 à 9. (c'est comme un array le premier éléments est à 0).
- Enfin, nous avons la dernière ligne qui renvoie le résultat de l'opération afin de pouvoir le récupérer dans le controller.

Justement. Du côté du controller, comment ça se passe ?
Et bien c'est très simple :

```php
//Dans notre controller
#[Route("/page", name:"page")]
public function taskPage (TaskRepository $taskRepository, Request $request) {
    $page = $request->query->get('page', 0);
    $tasks = $taskRepository->getPaginatedTask(10, $page);
    return $this->render('task/showAll.html.twig', ["task" => $tasks]);
}
```

Bien il n'y a pas beaucoup de ligne donc ça ne va pas être trop long à expliquer :smile:.
Par contre il y a quelque chose dont nous n'avons jamais parlé, il s'agit du `Request`. Cela permet de récupérer les variables qui sont dans la variable globale `$_GET` ou `$_POST`. Ici nous allons passer le paramètre page en GET (donc dans l'url). Je profite juste pour montrer différentes choses en rapport avec Symfony.
Notre url ressemblera à ça : `nomDeDomaine/task/page?page=1`.

>petit rappel. Juste au dessus de la classe de ce controller, nous avons mis que la route était `/task` c'est pour cela que vous le retrouvez dans cet exemple

Cet url permet donc d'afficher les tâches de la page 1.

:warning: pensez à bien écrire les paramètres de la fonction, afin que l'autocomplétion de votre IDE puisse importer les bonnes classes grâce aux use correspondants.

Dans nos paramètres nous utilisons donc le `TaskRepository` pour accéder à la fonction que nous avons créee précédemment ainsi que le `Request` pour accéder au paramètre dans l'url.

la première ligne de la fonction nous permet de récupérer la valeur du paramètre page ou alors 0 par défaut s'il n'y a pas de paramètre.

Nous pouvons ensuite appeler la fonction que nous avons crée dans le repository en lui passant les paramètres indiqués, à savoir la limit et la page (dans le bon ordre).

Et nous n'avons plus qu'à rendre la page grâce à Twig, en lui passant l'array de tâches en paramètre.

```twig

<!-- Dans notre fichier twig, nous allons pouvoir boucler dans toutes les tâches pour les afficher -->

{% for (task in tasks) %}
<div>
    <h1>task.title</h1>
    <!-- reste de l'affichage -->
</div>
{% endfor %}

```

Noter que l'on passe en paramètre `$tasks` qui est au pluriel car c'est un array, mais dans le for nous utilisons `task` car il n'y en a qu'une seul et ce n'est qu'un élément de notre array, cela nous permet donc d'accéder à ses propriétés tel que le titre.

## IV - Sécurité et utilisateur

Nous avons vu comment créer des entités, mais les utilisateurs sont des entités particulières. En effet, vous devez stocker le mot de passe qui doit être crypté afin que personne ne puisse le connaître. Pas de panique, Symfony à tout prévu.

> Avant toute chose, il faut require les bonnes libraires grâce à composer : `composer require symfony/security-bundle`.

Le principe est sensiblement le même que pour une entité : `php bin/console make:user`.
Sauf que ce coup-ci, on prévient directement Symfony qu'il faut créer un utilisateur.
Encore une fois vous pouvez choisir le nom de votre entité ou le laisser par défaur sur `User` (le plus simple).
Vous pouvez ensuite choisir comment est ce que votre utilisateur sera unique, que ce soit par un username ou une adrese mail afin qu'il ne puisse pas se créer plein de compte ce qui gênerait la modération.
Et enfin on vous demande si votre site aura besoin de stocker les mots de passe, vous choissez oui ou non et le tour est joué.

Enfin si c'était si facile, tout le monde le ferait.

Pour tout le reste je vous renvoie à [la documentation de Symfony](https://symfony.com/doc/current/security.html) car je ne ferais que paraphraser. Notamment pour la [connexion](https://symfony.com/doc/current/security.html#form-login) de l'utilisateur ou son enregistrement.

Nous avons parlé de la sécurité des utilisateurs, mais si notre site se fait attaquer, il y d'autres défenses à préparer.

### FormType

Les `FormType` sont des formulaires qui permettent de faire une première validation de données avant que ces données n'arrivent dans votre code ou pire dans votre base de donéees. Beaucoup d'attaques se passent par "injection SQL" c'est à dire, des requêtes SQL qui vont s'effectuer sans que vous le vouliez parce que quelqu'un de malveillant à réussi à trouver une faille et essaie donc de récupérer des données confidentielles de votre site par exemple les informations des utilisateurs.

**Comment ça marche ?** En général, une personne malveillante va écrire une requête spéciale dans un des champs des formulaires de votre site en espérant que cette requête s'exécute après ou au milieu de la requête prévue. Par exemple, dans la création d'une tâche, quelqu'un va rentrer le titre d'une tâche puis essayer d'exécuter la requête au plus vite afin que sa requête qu'il a inclus dans le champ puisse s'exécuter aussi.

**Comment s'en prévenir ?** Comme je vous en parlais, les FormType sont un bon moyen de prévenir les données indésirables. Déjà les champs vont avoir un type précis, ce qui empêchera la bonne validation du formulaire si les données entrées n'ont pas le bon type.
Le formulaire est généré automatiquement grâce au controller ce qui rend la chose plus facile. Toutefois, vous pouvez le modifier si vous voulez des données particulières qui ne sont pas prévues de base dans votre entité.

## V - Bonnes pratiques à avoir

Lors du développement de l'application, il est préférable de respecter certaines règles afin de garder une cohérence dans le projet ou encore de faciliter sa pérennité.

**Chacun s'occupe de sa partie jusqu'au bout :**

Si vous devez développer une fonctionnalité particulière de l'application, vous devez le faire jusqu'au bout, c'est à dire que vous allez écrire les tests nécessaire à la vérification du bon fonctionnement de votre fonctionnalité, vous allez aussi effectuer les autres test afin de voir si votre ajout n'a rien boulverser du contenu existant dans le cas ou ça pourrait affecter d'autres parties que la votre.

Il est également nécessaire de remplir la documentation / de commenter son code afin que les futures personnes qui travailleront sur votre partie ne passe pas plus de temps à essayer de comprendre ce qu'il se passe qu'à réellement continuer de développer le projet.