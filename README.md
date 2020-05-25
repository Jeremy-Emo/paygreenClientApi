# Paygreen API Client
> Client PHP permettant l'intégration de l'API de Paygreen
>
> <a href="https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement">Documentation de l'API</a>

## Installation
```shell script
composer require jemo/paygreen-api-client:dev-master
```

## Utilisation

### 1) Instancier ApiClient :
```php
use PaygreenApiClient\ApiClient;

$pk = "clé privée";
$url = "https://paygreen.fr";
$id = "identifiant paygreen";

$client = new ApiClient($id, $pk, $url);
```

### 2) Récupérer le sous-client nécessaire à l'appel de l'API puis utiliser les méthodes de la classe
```php
$info = $client->getTransactionClient()->getTransactionInfos("id de la transaction");
```
Il y a plusieurs clients à dispositions :
- CardprintClient, pour les empreintes de carte
- DonationClient, pour les dons
- PaymentTypeClient, pour récupérer les types de paiement
- TransactionClient, pour toute la gestion des transactions
- TransferClient, pour les virements

Certaines méthodes vont nécessiter d'instancier les classes du namespace PaygreenApiClient\Entity.
```php
use PaygreenApiClient\ApiClient;
use PaygreenApiClient\Entity\Buyer;
use PaygreenApiClient\Entity\Card;
use PaygreenApiClient\Entity\Transaction;

$pk = "clé privée";
$url = "https://paygreen.fr";
$id = "identifiant paygreen";

$client = new ApiClient($id, $pk, $url);

$buyer = new Buyer('id du buyer', 'nom', 'prénom', 'email@exemple.fr', 'pays', 'nom entreprise');
$card = new Card('token');

$transaction = new Transaction(10000, "id commande", "EUR");
$transaction->setBuyerAndCard($buyer, $card);

$info =  $client->getTransactionClient()->cashPayment($transaction);
```

### 3) Retour des méthodes
Les méthodes ont deux retours potentiels : un tableau dans le cas où l'appel à l'API s'est bien passé ou null dans le cas contraire. Il est alors possible de récupérer le code HTTP d'erreur :
```php
$client->getTransactionClient()->getLastHttpErrorCode();
```
Toutes les erreurs sont loguées sous le format suivant et stockées selon votre configuration de PHP : 

"Nom de la classe" - "nom de la méthode" : "erreur"

## A faire
- Connexion OAuth
- Tests unitaires
- Intégration des autres API, seule la partie paiement est faite