<?php

namespace Acelle\Model;

use Acelle\Library\Traits\HasUid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailIpAddress extends Model
{
    use HasFactory;
    use HasUid;


    protected $fillable = ['ip_address', 'sending_server_id', 'price_monthly', 'price_yearly', 'user_id','plan_id', 'sending_server_id', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sendingservers()
    {
        return $this->belongsTo(SendingServer::class,'sending_server_id', 'id');
    }

    public function plans()
    {
        return $this->hasMany(Plan::class,'plan_id', 'id');
    }
    /**
     * Filter items.
     *
     * @return collect
     */
    public static function filter($request)
    {
        $query = self::select('mail_ip_addresses.*');

        // Keyword
        if (!empty(trim($request->keyword))) {
            foreach (explode(' ', trim($request->keyword)) as $keyword) {
                $query = $query->where(function ($q) use ($keyword) {
                    $q->orwhere('mail_ip_addresses.ip_address', 'like', '%'.$keyword.'%')
                        ->orWhere('mail_ip_addresses.status', 'like', '%'.$keyword.'%');
                });
            }
        }

        if (!empty($request->admin_id)) {
            $query = $query->where('mail_ip_addresses.user_id', '=', $request->admin_id);
        }

        return $query;
    }

    /**
    * Search items.
    *
    * @return collect
    */

    public static function search($request)
    {
        $query = self::filter($request);

        if (!empty($request->sort_order)) {
            $query = $query->orderBy($request->sort_order, $request->sort_direction);
        }

        return $query;
    }


    /**
     * The rules for validation.
     *
     * @var array
     */
    public function rules()
    {
        return array(
            'sending_server_id' => 'required',
            'ip_address' => 'required|ipv4|unique:mail_ip_addresses,ip_address,'.$this->id.',id',
            'price_monthly' => 'sometimes',
            'price_yearly' => 'sometimes',
        );
    }

}
