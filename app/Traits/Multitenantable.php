<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Multitenantable {

    public static function bootMultitenantable() {

        if (auth()->check() && !auth()->user()->hasRole('Super Administrador')) {
            static::creating(function ($model) {
                if (!$model->empresa_id) {
                    $model->empresa_id = auth()->user()->empresa_id;
                }
                if (!$model->sucursal_id) {
                    $model->sucursal_id = auth()->user()->sucursal_id;
                }
            });
            
            static::addGlobalScope('multitenancy', function (Builder $builder) {
                $table = $builder->getModel()->getTable();
                
                if (auth()->user()->empresa_id) {
                    $builder->where($table . '.empresa_id', auth()->user()->empresa_id);
                }
                
                if (auth()->user()->sucursal_id) {
                    $builder->where($table . '.sucursal_id', auth()->user()->sucursal_id);
                }
            });
        }
    }
}