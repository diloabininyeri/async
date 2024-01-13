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
## Synchronous
If you want some objects to run in Sync, you can do this through the Mutex object.

```php

use Zeus\Async\AsyncProcess;
use Zeus\Async\Mutex;
use Zeus\Async\test\Sleep;
use Zeus\Async\test\SleepSync;

require_once 'vendor/autoload.php';


$mutex = new Mutex();

$asyncProcess = new AsyncProcess(7);

$asyncProcess->add(new SleepSync($mutex, 2));
$asyncProcess->add(new Sleep(1));
$asyncProcess->add(new SleepSync($mutex, 2));

$asyncProcess->start();
$asyncProcess->wait();
```

## Mutex
In critical transactions, some operations may need to wait for some operations, the best example of this is if two people try to buy a ticket at the same time, it will make one of them wait atomically.
```php

$mutex = new Mutex();

$mutex->lock();
//critic operations 
$mutex->unlock();
```
