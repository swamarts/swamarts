<?php

namespace Acelle;

use Acelle\Library\Traits\HasUid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleUTM extends Model
{
    use HasFactory;
    use HasUid;

    protected $fillable = [
        'utm_campaign', 'utm_source', 'utm_medium', 'user_id', 'status'
    ];

    public function rules()
    {
        return array(
            'utm_campaign' => 'required',
            'utm_source' => 'required|alpha|min:2',
            'utm_medium' => 'required|substring:{PRICE}',
        );
    }
}
