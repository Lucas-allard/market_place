# Market Place Project

Ce projet est une plateforme de market place qui permet aux vendeurs de proposer leurs produits aux clients. Les clients
peuvent parcourir et acheter les produits proposés par les vendeurs enregistrés. Le projet est basé sur Symfony et
utilise diverses technologies pour assurer le bon fonctionnement de la plateforme.

## Installation

Avant de commencer, assurez-vous d'avoir installé Composer et Node.js sur votre système.

Clonez ce dépôt sur votre machine locale.
```bash
git clone https://github.com/votre-utilisateur/market-place.git
```
Installez les dépendances PHP via Composer.
```bash
composer install
```

Installez les dépendances JavaScript via npm.
```bash
npm install
```

Lancez votre serveur local (WAMP, XAMPP, etc.).

Démarrez le serveur Symfony.
```bash
symfony serve
```

Compilez les assets avec npm.
```bash
npm run watch
```

Créez la base de données.
```bash
symfony console doctrine:database:create
```

Exécutez la migration pour mettre à jour la base de données.
```bash
symfony console doctrine:migrations:migrate
```

## Connexions

Vous pouvez vous connecter avec les comptes suivants :

Admin : admin@email.com / admin
Vendeur : andre.mahe@live.com / seller
Client : paul.danielle@orange.fr / customer

Ou vous pouvez créer votre propre compte selon le rôle que vous souhaitez.

## Configuration de l'environnement

Assurez-vous d'ajouter ces variables d'environnement dans un fichier .env à la racine du projet :

```bash
DATABASE_URL="mysql://root:@127.0.0.1:3306/db_market_place?serverVersion=8.0.32&charset=utf8mb4"
MAILER_DSN=smtp://857b636af75876:fc71a1ac0668da@sandbox.smtp.mailtrap.io:2525?encryption=tls&auth_mode=login
ADMIN_EMAIL=noreply@discountopia.fr
STRIPE_API_KEY='pk_test_51N3mn2D3ZvKl23tVBjgb6jIgoWsTs9J27bl8DkUqXQy5ME8475EWHqWIBoigN1pdDs9ry1da86PJxLSkxRnuGBUp006QDw7cnK'
STRIPE_SECRET_KEY='sk_test_51N3mn2D3ZvKl23tVmHQfuSuHFLVtJ0heVev0IX30M0qYLtyqdrvfG8XI7hPW7YEbb0IkZHE8OMSK2bM5ec97chhx00srI1slyY'
CLOUDINARY_CLOUD_NAME='dleawzfux'
CLOUDINARY_API_KEY='516211528541387'
CLOUDINARY_API_SECRET='zjlNj8qezgwiCfo0Ytz3pL7b7pA'
```