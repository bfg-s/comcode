<?php

namespace Bfg\Comcode\Tests;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

class TestedClass extends Model implements Arrayable, ArrayAccess
{
}