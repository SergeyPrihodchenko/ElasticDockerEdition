# Avy Search

## Описание
Репозиторий является абстракцией на API Elasticsearch

## Применение
Чтобы получить доступ к библиотеке нужно инстанциировать ее таким образом
```php
use AvySearch\Auth\BaseAuthentication;
use AvySearch\SearchInstance;
use Elastic\Elasticsearch\ClientBuilder;

$authenticator = new PasswordAuthentication('http://<host_name>:<port>', '<elastic_user>', '<elastic_user_password>');
$searchInstance = new SearchInstance($authenticator, ClientBuilder::create());
```  
Далее нужно запустить миграции, чтобы создать нужные индексы    
```php
$searchInstance->runSchemeMigrations();
```  
Далее мы должны загрузить данные. Например:  
```php
use AvySearch\DTO\ExternalEntities\FileCategory;
use AvySearch\DTO\ExternalEntities\FileData;

// Основная информация о файле
 $fileData = (new FileData())
        ->setLanguageAlias('rus')
        ->setFilename('test_3892hf.pdf')
        ->setOriginFilename('test.pdf')
        ->setByteSize(21093)
        ->setText('Тут фитинги такие, что офигеть...')
        ->setFileTypeName('Каталог')
        ->setMimeType('application/pdf');

// Добавляем категорию файлу
$fileData->appendCategory(
    (new FileCategory())
        ->setId(1)
        ->setTitle('Категория 1')
        ->setProductsExists(false)
);

// Добавляем категорию файлу, по которой мы можем
// сгруппировать поисковые результаты
$fileData->appendCategoryForGrouping(
    (new FileCategory())
        ->setId(2)
        ->setTitle('Категория 2')
        ->setProductsExists(true)
);

$searchInstance->upload()->document($fileData);
```