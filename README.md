# Documentation projet TODO

## Introduction

Dans cette documentation, nous allons parler d'une application de todo.
C'est à dire que les utilisateurs du site pourront créer des tâches et dire quand ils les ont fini ou non.

## I - Création d'une nouvelle page

### 1) Créer une page dans le code

- Pour créer une nouvelle page, il faut vous rendre dans le controller correspondant.
  - S'il n'y a aucun controller correspondant, il vous faut le créer dans le dossier controller avec le bon namespace et le nom de class qui correspond au nom du fichier.
  - N'oubliez pas d'étendre l'`AbstractController` afin d'avoir accès à toutes les fonctionnalités qu'il contient
- Une fois que vous êtes dans le bon controller, vous devez créer une nouvelle fonction en indiquant la route en annotation au dessus :

```php
#[Route("/", name:"task_list")]
public function index () {
    //code de votre fonction
}
```

Vous pourrez simplifier le chemin de votre route en spécifiant une route au dessus de la class :

```php
#[Route("/task", name:"app_task_")]
public class TaskController extends AbstractController{

    #[Route("/create", name:"create")]
    public function new () {
        //Code de votre fonction
    }
}
```

Ce qui donner comme chemin pour y accéder : `/task/create` et comme nom : `app_task_create`.

Si vous souhaitez ajouter des paramètres dans votre route, *par exemple pour des id*, il vous suffit de mettre le paramètre en accolades ex : `/maRoute/{id}`. Vous pourrez ensuite récupérer directement l'entité attachée à cet id dans les paramètres de votre fonction :

```php
#[Route("/{id}/edit", name:"edit")]
public function edit(Task $task) {
    //Code de votre fonction
}
```

Dans l'exemple ci-dessus, vous pouvez voir que nous avons fait appel à l'entité `Task` qui correspond aux tâches crées par les utilisateurs. Grâce à ce paramètre, vous allez pouvoir récupérez la tâche correspondante et faire le traitement que vous voulez dans le code de la fonction.

### 2) Afficher une page

Maintenant que vous avez créer votre fonction, il vous faut quand même afficher quelque chose à la sortie (sauf si ce n'est que du code exécutif bien sûr).

Pour afficher une page, nous utilisons le framework qui est fourni avec Symfony, [Twig](https://twig.symfony.com/).

Pour créer votre nouvelle page, vous allez vous rendre dans le dossier `templates`, puis vous choisirez le sous-dossier correspondant ou créerez un sous-dossier pertinent dans le cas où votre page ne rentrerez dans aucune catégorie connue. Votre fichier doit avoir la terminaison : `.html.twig`

Dans votre fichier, il y a quelque règles à penser. Si vous voulez créer des blocs, vérifiez qu'ils n'existent pas déjà dans le fichier `base.html.twig`, qui contient du code commun à toutes les pages. Toutes les pages sont étendues de ce fichier afin d'avoir un style commune ou encore des éléments communs (notamment la navbar).

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

:warning: **Attention** : tout code executé après le return ne sera pas lu car les informations seront déjà envoyées. Vérifiez donc où est ce que vous placez votre return

Il est possible de passer des paramètres à votre fichier, ce qui peut être pratique pour afficher des informations que vous avez récupéré grâce au code. Dans l'exemple ci-dessous, je vais montrer comment afficher une tâche en fonction de son id :

#### Fichier php

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

Vous pouvez passer plusieurs paramètres dans le render car il s'agit d'un array, vous pourrez ainsi récupérez plusieurs informations si besoin.

Pour plus d'informations sur twig, je vous renvois à sa [documentation](https://twig.symfony.com/doc/). Vous verrez que vous pouvez aussi utilisez des conditions dans le rendu ou encore accéder à l'utilisateur qui est connecté. C'est un outil bien pratique.
