<?php

namespace App\Http\Controllers;

use App\Model\Category;
use App\Model\CPU;
use App\Model\GPU;
use App\Model\Post;
use App\Model\Product;
use App\Model\Slider;
use App\Model\User;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $categories;
    public function __construct()
    {
        $this->categories = Category::where('parent_id', 0)->get();
    }
    public function index()
    {
        //
        $sliders = Slider::where('type', 1)->where('status', '1')->orderBy('position')->get();
        // dd($sliders);
        // $products = Product::offset(0)->limit(4)->orderBy('id', 'desc')->get();
        $highlight = Product::leftJoin('review','review.product','=','product.id')
        ->select(['product.*',DB::raw('ifnull(avg(review.points),0) as point')])
        ->groupBy('product.id')->orderBy('point','desc')->where('status',1)->take(4)->get();
        // dd($highlights);
        $besseller = Product::leftJoin('order_detail','product.id','=','order_detail.product')
        ->select('product.*',DB::raw('ifnull(sum(order_detail.quantity),0) as sumsell'))
        ->groupBy('product.id')->orderBy('sumsell','desc')->where('status',1)->take(3)->get();
        // dd($besseller);
        $new = Product::orderBy('created_at','desc')->where('status',1)->take(3)->get();
        // dd($new);
        $posts = Post::orderBy('view','desc')->where('status',1)->take(2)->get();
        // dd($posts);
        return view('shop.index', ['sliders' => $sliders,'posts'=>$posts, 'highlight' => $highlight,'bestseller'=>$besseller,'new'=>$new, 'categories' => $this->categories]);

    }
    public function liveSearch(){
        $searchKey = request()->query('q');
        $data = [];
        if($searchKey!=null && $searchKey!=''){
            $products = Product::where('name','like','%'.$searchKey.'%')->orWhere('sku','like','%'.$searchKey.'%')->offset(0)->limit(6)->get();
            foreach($products as $item){
                $data[]=[
                    'name' => $item->name,
                    'image' => asset('storage/'.$item->card_image),
                    'link' => route('shop.product.show',$item->slug),
                    'sku' => $item->sku
                ];
            }
            return response()->json($data);
        }
        return abort(404,'Empty key');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function storePage()
    {
        
       
        
        $products = Product::with('cpu','gpu')->where('status',1);
        $searchKey = request()->query('q');
        $sortKey = request()->query('sort');
        $categoryID = request()->query('catalog');
        $price =  request()->query('price');
        $cpus =  request()->query('cpu');
        $gpus =  request()->query('gpu');
        $ram =  request()->query('ram');
        $screen =  request()->query('screen');
        $ssd =  request()->query('ssd');
        $hdd =  request()->query('hdd');
        
        $queryString = "";
        if($categoryID){
            $cat = Category::find($categoryID);
            $products = $cat->product();
           
        }
        //dd($products);
        if ($searchKey) {
            $products = $products->where('name', 'like', '%' . $searchKey . '%')->orWhere('sku', 'like', '%' . $searchKey . '%');
        }
        if($cpus){
            $products = $products->join('cpu','product.cpu','=','cpu.id')->whereIn('cpu.series',$cpus);
           
        }
        if($ram){
            $products = $products->whereIn('memory_capacity',$ram);
        }
        if($screen){
            $products = $products->whereIn('screen_size',$screen);
        }
        if($ssd){
            $products = $products->whereIn('ssd_capacity',$ssd);
        }
        if($hdd){
            $products = $products->whereIn('hdd_capacity',$hdd);
        }
        if($price && $price[0] && $price[1]){
            $products = $products->where('sell_price','>=',$price[0])->where('sell_price','<=',$price[1]);
           
        }
        if($gpus){
            $products = $products->join('gpu','product.gpu','=','gpu.id')->whereIn('gpu.name',$gpus);
           
        }
       
        if ($sortKey && ($sortKey == 'asc' || $sortKey == 'desc')) {
            $products = $products->orderBy('sell_price', $sortKey);
        }
        
        
        $products = $products->paginate(6,['product.*']);
        // dd($products);
        // $products = $products->where('status',1)->where('stock','>',0)->paginate(6);
        //x??? l?? query string
        if($cpus){
            $products->appends(['cpu' => $cpus]);
            $queryString.= join(array_map(function($item){
                return '&cpu[]='.$item;
            },$cpus));
        }
        if($gpus){
            $products->appends(['gpu' => $gpus]);
            $queryString.= join(array_map(function($item){
                return '&gpu[]='.$item;
            },$gpus));
        }
        if($ram){
            $products->appends(['ram'=>$ram]);
            $queryString.= join(array_map(function($item){
                return '&ram[]='.$item;
            },$ram));
        }
        if($screen){
            $products->appends(['screen'=>$screen]);
            $queryString.= join(array_map(function($item){
                return '&screen[]='.$item;
            },$screen));
        }
        if($ssd){
            $products->appends(['ssd'=>$ssd]);
            $queryString.= join(array_map(function($item){
                return '&ssd[]='.$item;
            },$ssd));
        }
        if($hdd){
            $products->appends(['hdd'=>$hdd]);
            $queryString.= join(array_map(function($item){
                return '&hdd[]='.$item;
            },$hdd));
        }
        if($price){
            $products->appends(['price' =>$price]);
            $queryString.= '&price[]='.$price[0].'&price[]='.$price[1];
        }
        if($categoryID){
            $products->appends(['catalog'=>$categoryID]);
            $queryString.="&catalog=$categoryID";
        }
        if ($searchKey) {
            $products->appends(['q' => $searchKey]);
        }
        if ($sortKey && ($sortKey == 'asc' || $sortKey == 'desc')) {
            $products->appends(['sort' => $sortKey]);
        }
       
        
        
        //x??? l?? d??? li???u filter
        $filters = collect([
            'cpus' => CPU::distinct()->has('product')->get('series')->toArray(),
            'gpus' => GPU::has('product')->get('name')->toArray(),
            'ram'  => Product::distinct()->get('memory_capacity')->toArray(),
            'screen' => Product::distinct()->get('screen_size')->toArray(),
            'ssd' => Product::distinct()->get('ssd_capacity')->toArray(),
            'hdd' => Product::distinct()->get('hdd_capacity')->toArray(),
        ]);
        // dd($filters);


        return view('shop.store', ['categories' => $this->categories, 'products' => $products, "queryString"=>$queryString,'filters'=>$filters]);
    }
    public function editUserInfo(){

        return view('shop.customer',['categories'=>$this->categories]);
    }
    public function updateUserInfo(Request $request){
        $request->validate([
            'name' => ['required','min:5'],
           
        ],[
            'name.required' => 'T??n kh??ng ???????c b??? tr???ng!',
            'name.min' => '????? d??i t??n t???i thi???u ph???i 5 k?? t???!'
           
        ]);

        $user = Auth::user();
        //x??? l?? c???p nh???t th??ng tin

        $options = [
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'birthday' => $request->input('birthday'),
            'gender' => $request->input('gender'),
        ];
        $user->update($options);


        $messages = [];
        

        //x??? l?? ?????i m???t kh???u
        if($request->input('old_password')){
            //ki???m tra m???t kh???u ch??nh x??c
            Auth::user()->makeVisible('password');
           if(Hash::check($request->input('old_password'),Auth::user()->password)){
                $request->validate([
                    'password' => ['required','min:6'],
                    'confirm' => ['required','same:password']
                ],[
                    'password.required' => 'M???t kh???u kh??ng ???????c ????? tr???ng!',
                    'password.min' => 'M???t kh???u ph???i t???i thi???u 6 k?? t???',
                    'confirm.same' => 'X??c nh???n m???t kh???u th???t b???i!'
                ]);

                
                
                $user->update(['password' => Hash::make($request->input('password'))]);
                return back()->with('success', '???? l??u!');
           }
           else{
                $messages['old_password'] = 'M???t kh???u kh??ng ch??nh x??c!';
                return redirect()->back()->withErrors($messages)->withInput();
           }
        }
        return back()->with('success', '???? l??u!');
    }
    public function showProduct($slug)
    {
        $product = Product::where('slug', $slug)->first();
        if ($product) {
            return view('shop.product.show', ['product' => $product, 'categories' => $this->categories]);
        } else {
            abort(404,'Kh??ng t??m th???y s???n ph???m!');
        }
    }
    public function contactPage()
    {
        return view('shop.contact', ['categories' => $this->categories]);
    }
    public function checkOut()
    {
        if (Cart::count() == 0) return back();
        return view('shop.checkout', ['categories' => $this->categories]);
    }
    public function postPage()
    {
        $posts = Post::where('status',1)->orderBy('created_at','desc')->paginate(6);
        return view('shop.post.index',['categories'=>$this->categories,'posts' => $posts]);
    }
    public function showPost($slug){

        $post = Post::where('slug',$slug)->first();

        if($post){
            return view('shop.post.show',['categories'=>$this->categories,'post'=>$post]);
        }else{
            abort(404);
        }
    }


    public function login(Request $request)
    {
        // dd($request);
      

        $credentials = [
            "email" => $request->email,
            "password" => $request->password
        ];
        // dd($request->input());
        // $remember = isset($request->remember) ? true : false;


        if (Auth::attempt($credentials, true)) {
            // dd(Auth::user());
            return back();
        }
        return back()->withErrors(['msg'=>'Th??ng tin ????ng nh???p kh??ng ch??nh x??c!'],'login')->withInput();
    }
    public function register(Request $request){
        //validate
        $rules =  [
            'name' => ['required','min:5'],
            'email' => ['required','email', Rule::unique('user','email')],
            'phone' =>['required','regex:/^\d{10}$/', Rule::unique('user','phone')],
            'password' => ['required','min:6'],
            'confirm' => ['required','same:password']
        ];
        $messages = [
            'name.required' => 'T??n kh??ng ???????c tr???ng!',
            'name.min' => 'T??n t???i thi???u ph???i 5 k?? t???!',
            'email.required' => 'Email kh??ng ???????c tr???ng!',
            'email.email' => 'Email kh??ng ????ng ?????nh d???ng!',
            'email.unique' => 'Email n??y ???? ???????c s??? d???ng!',
            'phone.required' => 'S??? ??i???n tho???i kh??ng ???????c tr???ng!',
            'phone.regex' => 'S??? ??i???n tho???i kh??ng ????ng ?????nh d???ng!',
            'phone.unique' => 'S??? ??i???n tho???i n??y ???? ???????c s??? d???ng!',
            'password.required' => 'M???t kh???u kh??ng ???????c tr???ng!',
            'password.min' => 'M???t kh???u t???i thi???u ph???i 6 k?? t???!',
            'confirm.required' => 'Vui l??ng x??c nh???n l???i m???t kh???u!',
            'confirm.same' => 'Vui l??ng nh???p l???i ch??nh x??c m???t kh???u!'

        ];
        $validator = Validator::make($request->input(),$rules,$messages);
        if($validator->fails()){

            
            return back()->withErrors($validator,'register')->withInput();
        }
        
        
        //t???o user
        $options = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password'))
        ];
        $user = User::create($options);
        if($user){
            Auth::login($user,true);
            return back()->with('success-created','B???n ???? t???o th??nh c??ng t??i kho???n!');
        }else{
            return back()->with('error-created','C?? l???i x???y ra!');
        }
        //chuy???n trang ????ng nh???p
        
    }
    public function logout()
    {
        if (Auth::check()) {
            $accessToken = auth()->user()->setRememberToken("");
         
            Auth::logout();
            // session()->invalidate();
            // session()->flush();
            // session()->regenerateToken();
           
            return back();
        }
        return back();
    }


    public function history(){
        // dd(Auth::user()->customer);
        $statusArray = ['Ch??? x??c nh???n','???? x??c nh???n','??ang giao h??ng', '???? giao h??ng', '???? h???y'];
        return view('shop.history',['categories'=>$this->categories,'statusArray'=> $statusArray]);
    }

    public function cancelOrder($orderCode){
        $customer = Auth::user()->customer;
        $order = $customer->order()->withTrashed()->where('order_code',$orderCode)->first();
        if($order){
            $order->update(['status' => 4]);
            foreach($order->detail as $detail){
                $detail->product()->first()->increment('stock',$detail->quantity);
            }
            return back()->with('success','???? h???y ????n h??ng');
        }else{
            return back()->with('error', 'C?? l???i x???y ra!');
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
