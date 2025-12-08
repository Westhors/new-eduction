<?php

namespace App\Repositories;

use App\Interfaces\CurriculaRepositoryInterface;
use App\Models\Curriculum;
use Illuminate\Database\Eloquent\Model;

class CurriculaRepository extends CrudRepository implements CurriculaRepositoryInterface
{
    protected Model $model;

    public function __construct(Curriculum $model)
    {
        $this->model = $model;
    }
}
