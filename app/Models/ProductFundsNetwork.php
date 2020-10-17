<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFundsNetwork extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_funds_network';

   /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'network_id',
        'action_type_id',
        'year',
        'amount',
        'import_at',
        'created_at',
        'updated_at',
    ];

    protected $dates = ['import_at', 'created_at', 'updated_at'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function network()
    {
        return $this->belongsTo(Networks::class, 'network_id');
    }

    public function action_type()
    {
        return $this->belongsTo(ActionType::class, 'action_type_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

}
