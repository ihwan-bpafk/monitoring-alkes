<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Session;

class BencanaScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Mengecek apakah ada session 'active_bencana_id'
        if (Session::has('active_bencana_id')) {
            $builder->where('bencana_id', Session::get('active_bencana_id'));
        }
    }
}
