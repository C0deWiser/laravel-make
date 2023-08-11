# Some more `artisan:make` templates


## Make Collection

Run command.

```shell
php artisan make:collection UserCollection
```

It will create Collection class.

```php
namespace App\Collections;

use App\Models\User as Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Model first(callable $callback = null, $default = null)
 * @method Model firstOrFail($key = null, $operator = null, $value = null)
 * @method Model firstWhere($key, $operator = null, $value = null)
 * @method Model find($key, $default = null)
 * @method Model last(callable $callback = null, $default = null)
 * @method Model sole($key = null, $operator = null, $value = null)
 */
class UserCollection extends Collection
{
    //
}
```

Register it to a model.

```php
namespace App\Models;

use App\Collections\UserCollection;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static UserCollection all($columns = ['*'])
 */
class User extends Model
{
    public function newCollection(array $models = []): UserCollection
    {
        return new UserCollection($models);
    }
}
```

## Make Builder

Run command.

```shell
php artisan make:builder UserBuilder
```

It will create Builder class.

```php
namespace App\Builders;

use App\Models\User as Model;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Model getModel()
 * @method Model make(array $attributes = [])
 * @method Model create(array $attributes = [])
 * @method Model forceCreate(array $attributes)
 * @method Model sole($columns = ['*'])
 * @method Model find($id, $columns = ['*'])
 * @method Model findOr($id, $columns = ['*'], Closure $callback = null)
 * @method Model findOrNew($id, $columns = ['*'])
 * @method Model findOrFail($id, $columns = ['*'])
 * @method Model first($columns = ['*'])
 * @method Model firstOr($columns = ['*'], Closure $callback = null)
 * @method Model firstOrNew(array $attributes = [], array $values = [])
 * @method Model firstOrFail($columns = ['*'])
 * @method Model firstOrCreate(array $attributes = [], array $values = [])
 * @method Model firstWhere($column, $operator = null, $value = null, $boolean = 'and')
 * @method Model updateOrCreate(array $attributes, array $values = [])
 *
 * @method UserCollection|Model[] findMany($ids, $columns = ['*'])
 * @method UserCollection|Model[] get($columns = ['*'])
 */
class UserBuilder extends Builder
{
    //
}
```

Register it to a model.

```php
namespace App\Models;

use App\Builders\UserBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static UserBuilder query()
 */
class User extends Model
{
    public function newEloquentBuilder($query): UserBuilder
    {
        return new UserBuilder($query);
    }
}
```
