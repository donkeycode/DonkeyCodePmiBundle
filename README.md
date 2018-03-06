# DonkeyCodePmiBundle

An interface to show propel datas 

## Setup

````
composer require donkeycode/pmi-bundle
````

Add in `AppKernel.php`

````
new DonkeyCode\PmiBundle\DonkeyCodePmiBundle(),
````

Add in routing.yml

````
pmi:
    resource: '@DonkeyCodePmiBundle/Controller/'
    type: annotation
````

Visit your app : http://<app>/pmi
