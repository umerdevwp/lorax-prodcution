<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntegratedApps extends Model
{
    
    protected $primaryKey = 'app_id';

    /**
     * Get the app documents for the blog post.
     */
    public function appDocuments()
    {
        return $this->hasMany('App\AppDocuments','app_id', 'app_id');
    }
}

