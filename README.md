# php-crufd-wizard-client - RetrieveQL

[![Total Downloads](https://img.shields.io/packagist/dt/macropay-solutions/php-crufd-wizard-client)](https://packagist.org/packages/macropay-solutions/php-crufd-wizard-client)
[![Latest Stable Version](https://img.shields.io/packagist/v/macropay-solutions/php-crufd-wizard-client)](https://packagist.org/packages/macropay-solutions/php-crufd-wizard-client)
[![License](https://img.shields.io/packagist/l/macropay-solutions/php-crufd-wizard-client)](https://packagist.org/packages/macropay-solutions/php-crufd-wizard-client)

 This can be used for calling [php-crufd-wizard](https://github.com/macropay-solutions/php-crufd-wizard)

## Install

    composer require macropay-solutions/php-crufd-wizard-client

## Start using it

```php

    $crud = new \MacropaySolutions\CrufdWizardClient\RequestBuilder(\env('API_BEARER'), \env('APP_URL'));

    try {
        $result = $crud->list('clients', $crud->getBuilder()->sort('country', 'asc')
            ->sort('zip')->equals('name', 'alt')->withRelation('relation')
            ->withRelations(['rel1', 'rel2'])
            ->addCountRelations(['relation1'])->addExistRelation('relation2');
        $result = $crud->get('clients', '73', ['rel1', 'rel2']);
        $result = $crud->delete('clients', '73');
        $result = $crud->create('clients', [
            'active' => '1',
            'name' => 'abc',
            // ...
        ]);
        $result = $crud->update('clients', '73', [
            'active' => '1',
            'name' => 'abc',
            // ...
        ]);
    } catch (\Exception $e) {
        $decodedErrorMessage = \json_decode($e->getMessage());
        echo $decodedErrorMessage->message;
    }

    $decodedResult = \json_decode($result);
```

Discover more methods available by installing it.
