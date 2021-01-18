<?php

namespace App\Transformers;

use App\Models\News;
use League\Fractal\TransformerAbstract;

class NewsTransformer extends TransformerAbstract
{
    public $type = 'news';
    
    protected $availableIncludes = ['user'];

    public function transform(News $model)
    {
        return [
            "id"            => $model->id,
            "title"         => $model->title,
            "description"   => $model->description
        ];
    }

    public function includeUser(News $model)
    {   
        if (! empty($model->user)) {
            return $this->item($model->user, new UserTransformer(), 'user');
        }
    }
}