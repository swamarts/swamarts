<?php

namespace Acelle\Model;

use Acelle\Library\Traits\HasUid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'template_categories';
    use HasFactory, HasUid;
    protected $fillable = [
        'name'
    ];
}
