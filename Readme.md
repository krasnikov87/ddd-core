# DDD Core package

Class set for make lumen or laravel application with using DDD principles

The kit includes:
- Base Model used UUID
- Command Bus package
- Pagination class
- Sort class
- Repository interface and abstract implementation of postgres and array
- Translation trait
- UUID trait

## Install
- `composer require krasnikov/ddd-core` 

### Use Base model
```php
<?php

declare(strict_types=1);

namespace App\Domain\Auth;

use Krasnikov\DDDCore\BaseModel;

class AuthToken extends BaseModel
{

}
```

### Use UUID trait
```php
<?php

declare(strict_types=1);

namespace App\Domain\User;

use Illuminate\Database\Eloquent\Model;use Krasnikov\DDDCore\BaseModel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Krasnikov\DDDCore\Uuid;
use Laravel\Lumen\Auth\Authorizable;
use Spatie\Permission\Traits\HasRoles;


class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable;
    use Authorizable;
    use HasRoles;
    use Uuid;
}
```

### Command Bus
#### Define command bus in base controller

```php
<?php

namespace App\Http\Controllers;

use Krasnikov\DDDCore\Command;
use Krasnikov\DDDCore\CommandBusInterface;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    /**
     * @var CommandBusInterface
     */
    private $dispatcher;

    /**
     * Controller constructor.
     * @param CommandBusInterface $dispatcher
     */
    public function __construct(CommandBusInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Command $command
     * @return mixed
     */
    public function execute(Command $command)
    {
        return $this->dispatcher->dispatch($command);
    }
}
```

#### Make command
```php
<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Role\GetRoleList;

use Krasnikov\DDDCore\Command;

/**
 * Class GetRoleList
 * @package App\Application\Dashboard\Role
 */
class GetRoleList implements Command
{}
```

#### Make command handler
```php
<?php

declare(strict_types=1);

namespace App\Application\Dashboard\Role\GetRoleList;

use Krasnikov\DDDCore\Command;
use Krasnikov\DDDCore\Handler;

/**
 * Class GetRoleListHandler
 * @package App\Application\Dashboard\Role\GetRoleList
 */
class GetRoleListHandler implements Handler
{
    /**
     * @param Command|GetRoleList $command
     */
    public function handle(Command $command): void
    {
        // same code
    }
}
```
#### Call in controller
```php
/**
 * @param Request $request
 * @return JsonResponse
 * @throws ValidationException
 */
public function index(Request $request): JsonResponse
{
    $res = $this->execute(GetRoleList::fromRequest($request));

    return new JsonResponse($res);
}
```

### Repository
#### Entity repository interface have to extend `RepositoryInterface` and define filter method
```php
<?php

declare(strict_types=1);

namespace App\Domain\Auth;

use Krasnikov\DDDCore\RepositoryInterface;
use Krasnikov\DDDCore\Sorting;
use App\Domain\User\UserRepositoryInterface;

/**
 * Class AuthTokenRepositoryInterface
 * @package App\Domain\Auth
 */
interface AuthTokenRepositoryInterface extends RepositoryInterface
{
    /**
     * @param AuthTokenFilter $filter
     * @return $this
     */
    public function filter(AuthTokenFilter $filter): self;
}
```

#### Postgres entity repository have to implement Entity repository interface, extend `AbstractPostgresRepository` and implement filter and __constructor method
```php
<?php

declare(strict_types=1);

namespace App\Infrastructure\Persists\PostgresRepository;

use App\Domain\Auth\AuthToken;
use App\Domain\Auth\AuthTokenFilter;
use App\Domain\Auth\AuthTokenNotFoundException;
use App\Domain\Auth\AuthTokenRepositoryInterface;
use Krasnikov\DDDCore\AbstractPostgresRepository;

/**
 * Class EloquentAuthTokenRepository
 * @package App\Infrastructure\Persists\EloquentRepository
 */
class PostgresAuthTokenRepository extends AbstractPostgresRepository implements AuthTokenRepositoryInterface
{
    /**
     * PostgresAuthTokenRepository constructor.
     */
    public function __construct()
    {
        $this->model = new AuthToken();
        $this->exceptionNotFound = AuthTokenNotFoundException::class;
    }

    /**
     * @inheritDoc
     */
    public function filter(AuthTokenFilter $filter): AuthTokenRepositoryInterface
    {
        $this->makeBuilder();
        //some code
        return $this;
    }
}
```

#### Array entity repository have to implement Entity repository interface, extend `AbstractArrayRepository` and implement filter and __constructor methods
```php
<?php

declare(strict_types=1);

namespace App\Infrastructure\Persists\ArrayRepository;

use App\Domain\{Auth\AuthTokenFilter,Auth\AuthTokenRepositoryInterface};
use Krasnikov\DDDCore\AbstractArrayRepository;

/**
 * Class ArrayAuthTokenRepository
 * @package App\Infrastructure\Persists\ArrayRepository
 */
class ArrayAuthTokenRepository extends AbstractArrayRepository implements AuthTokenRepositoryInterface
{
    /**
     * ArrayAuthTokenRepository constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->exceptionNotFound = AuthTokenNotFoundException::class;
    }

    /**
     * @inheritDoc
     */
    public function filter(AuthTokenFilter $filter): AuthTokenRepositoryInterface
    {
        $result = $this->seed;
        if ($this->result->count()) {
            $result = $this->result;
        }
        
        //some code
        $this->result = $result;
        
        return $this;
    }
}
```
- use ```Krasnikov\DDDCore\Pagination::fromRequest($request)``` in your controller for set pagination and send object to repository;
- use ```Krasnikov\DDDCore\Sorting::fromRequest($request)``` in your controller for set sorting and send object to repository.
- you can use ``Krasnikov\EloquentJSON\PaginateRequest`` in controller or extended in form request for validation pagination.

### Example pagination and sort url 
```
GET /articles?page[number]=2&page[size]=10&sort=title,-createdAt HTTP/1.1
Accept: application/vnd.api+json
```
