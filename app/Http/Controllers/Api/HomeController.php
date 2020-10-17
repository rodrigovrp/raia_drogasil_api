<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Networks;
use App\Models\ProductFunds;
use App\Models\ProductFundsNetwork;
use App\Models\ProductCategory;
use App\Http\Resources\HomeCategoryResource;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $total_products = Product::count();
        $total_networks = Networks::count();
        $total_funds = ProductFunds::sum('amount');
        $total_funds_network = ProductFundsNetwork::sum('amount');
        $total_funds_by_category = HomeCategoryResource::collection(ProductCategory::where(function($query){ $query->where('parent_id', 0)->orWhere('parent_id', null); })->get());

        return response()->json([
            'data' => [
                'total_products' => $total_products,
                'total_networks' => $total_networks,
                'total_funds' => $total_funds,
                'total_funds_network' => $total_funds_network,
                'total_funds_by_year' => [
                    ProductFunds::where('year', date('Y') )->sum('amount'),
                    ProductFunds::where('year', date('Y', strtotime('+1 year')) )->sum('amount')
                ],
                'total_funds_network_by_year' => [
                    ProductFundsNetwork::where('year', date('Y') )->sum('amount'),
                    ProductFundsNetwork::where('year', date('Y', strtotime('+1 year')) )->sum('amount')
                ],
                'years' => [
                    date('Y'),
                    date('Y', strtotime('+1 year'))
                ],
                'total_funds_by_category' => $total_funds_by_category,
            ]
        ], 200);
    }

}
