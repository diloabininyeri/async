## Php async 
Provides async running objects with PHP via process spawn

for installation via composer 
```console
composer require zeus/async:dev-main

```
php test code 
```php
use Zeus\Async\AsyncProcess;
use Zeus\Async\test\Sleep;

require_once 'vendor/autoload.php';


$asyncProcess = new AsyncProcess();
$asyncProcess->add(new Sleep(2));
$asyncProcess->add(new Sleep(1));
$asyncProcess->add(new Sleep(2));
$asyncProcess->add(new Sleep(1));
$asyncProcess->add(new Sleep(3));
$asyncProcess->add(new Sleep(1));
$asyncProcess->add(new Sleep(2));
$asyncProcess->add(new Sleep(2));
$asyncProcess->add(new Sleep(1));


echo 'app started';

$asyncProcess->start();

echo 'app progressing';
$asyncProcess->wait();

echo 'app is finished';


```