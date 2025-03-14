<?php

namespace App\Http\Middleware;

use Artesaos\SEOTools\Facades\SEOTools;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SEODefaults
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set default SEO values that should be present on all pages
        SEOTools::setTitle('Cacha Store - Jajanan & Snack Kekinian Asli Pangandaran');
        SEOTools::setDescription('Temukan berbagai macam jajanan dan snack kekinian khas Pangandaran di Cacha Store. Tersedia berbagai varian rasa dan ukuran yang cocok untuk semua kalangan.');

        // Set default OpenGraph values
        SEOTools::opengraph()->setType('website');
        SEOTools::opengraph()->setSiteName('Cacha Store');
        SEOTools::opengraph()->addProperty('locale', 'id_ID');

        // Set default Twitter card values
        SEOTools::twitter()->setSite('@cachastore');

        // Default image for OpenGraph and Twitter if no specific image is set
        SEOTools::opengraph()->addImage(asset('images/logo-snack-circle.png'));
        SEOTools::twitter()->setImage(asset('images/logo-snack-circle.png'));

        // Add default keywords
        SEOTools::metatags()->addKeyword([
            'jajanan pangandaran',
            'snack kekinian',
            'cacha store',
            'cemilan khas pangandaran',
            'oleh-oleh pangandaran'
        ]);

        return $next($request);
    }
}