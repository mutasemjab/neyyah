<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\Adv;
use App\Models\Banner;
use App\Models\BottomSectionHome;
use App\Models\Brand;
use App\Models\Event;
use App\Models\Product;
use App\Models\PublicSession;
use App\Models\Service;
use App\Models\Projects;
use App\Models\Setting;
use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    
    public function index()
    {
        $banners = Banner::get();
        $brands = Brand::get();
        $services = Service::get();
        $bottomSection = BottomSectionHome::first();
        
        // Get featured products
        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(10) // Limit to 10 featured products
            ->get();

        $locale = app()->getLocale();
        $setting = Setting::first();
        
        return view('user.home', compact(
            'banners',
            'locale',
            'setting',
            'brands',
            'services',
            'bottomSection',
            'featuredProducts'
        ));
    }
    
   


}