<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
   /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_category';

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
        'parent_id',
        'name',
        'slug',
        'description',
        'order',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $dates = ['created_at', 'updated_at'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();
    }

    public function getParent(){
		return $this->where('id', $this->parent_id)->first();
    }

    public function getChilds(){
		return $this->where('parent_id', $this->id);
    }

    public function getFundsByCategory($category){
        $amount = $category->funds()->sum('amount');
        if($category->getChilds()->count() > 0){
            foreach($category->getChilds()->get() as $child){
                $amount += $this->getFundsByCategory($child);
            }
        }
		return $amount;
    }

    public function getFundsNetworkByCategory($category){
        $amount = $category->funds_network()->sum('amount');
        if($category->getChilds()->count() > 0){
            foreach($category->getChilds()->get() as $child){
                $amount += $this->getFundsNetworkByCategory($child);
            }
        }
		return $amount;
	}

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function funds()
    {
        return $this->hasManyThrough(ProductFunds::class, Product::class, 'category_id');
    }

    public function funds_network()
    {
        return $this->hasManyThrough(ProductFundsNetwork::class, Product::class, 'category_id');
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
