<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Slide;
use App\Services\RajaOngkirService;

class HomeController extends Controller
{
    public function index()
    {
        $slides = Slide::where('status', 1)->get()->take(3);
        $categories = Category::orderBy('name')->get();
        $sproducts = Product::whereNotNull('sale_price')->where('sale_price', '<>', '')->inRandomOrder()->get()->take(8);
        $fproducts = Product::where('featured', 1)->get()->take(8);
        return view('index', compact('slides', 'categories', 'sproducts', 'fproducts'));
    }

    public function contact()
    {
        return view('contact');
    }

    public function contact_store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email',
            'phone' => 'required|numeric|digits:12',
            'comment' => 'required'
        ]);

        $contact = new Contact();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->comment = $request->comment;
        $contact->save();
        return redirect()->back()->with('success', 'Your message has been sent successfully');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = Product::where('name', 'LIKE', "%($query)%")->get()->take(8);
        return response()->json($results);
    }

    protected RajaOngkirService $raja;

    public function __construct(RajaOngkirService $raja)
    {
        $this->raja = $raja;
    }

    public function example()
    {
        // 1. Semua provinsi
        $provinsi = $this->raja->getAllProvinces();

        // 2. Provinsi ID=5
        $p5 = $this->raja->getProvinceById(5);

        dd($provinsi, $p5);

        // 3. Cari provinsi “Daerah”
        $cProv = $this->raja->searchProvinces('Daerah');

        // 4. Semua kota
        $cities = $this->raja->getAllCities();

        // 5. Kota di provinsi 5
        $cByProv5 = $this->raja->getCitiesByProvince(5);

        // 6. Kota ID=39
        $city39 = $this->raja->getCityById(39);

        // 7. Cari kota “Tangerang” di provinsi 5
        $cCity = $this->raja->searchCities('Tangerang', 5);

        // 8. Hitung ongkir: 155→80, berat 1500g, JNE
        $costs = $this->raja->calculateCost(155, 80, 1500, 'jne');
        // misal ambil tarif REG:
        $regCost = collect($costs)
            ->firstWhere('service', 'REG')['cost'][0]['value'] ?? 0;

        return view('demo', compact(
            'provinsi',
            'p5',
            'cProv',
            'cities',
            'cByProv5',
            'city39',
            'cCity',
            'costs',
            'regCost'
        ));
    }

    public function articles()
    {
        $articles = Blog::orderBy('created_at', 'desc')->paginate(10);

        return view('blog', compact('articles'));
    }

    public function showArticle(Blog $article)
    {
        return view('blog-show', compact('article'));
    }
}
