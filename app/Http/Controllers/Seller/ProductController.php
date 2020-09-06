<?php

namespace App\Http\Controllers\Seller;

use App\Product;
use App\Categorya;
use App\Categoryb;
use App\Categoryc;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    public function test($id)
    {
        $product = Product::findOrFail($id);

        $photos = unserialize($product->photo_paths);

        foreach ($photos as $photo){
            //return $photo;
        }

        return unserialize($product->photo_paths);
    }

    public function showProductById($id)
    {
        $url = env('APP_URL_MAIN').'/product/details/'.$id;
        return Redirect::to($url);
    }

    public function uploadImages(Request $request)
    {
        $files = $_FILES; //File inputs are not put into $_POST, they're only in $_FILES.
        $number_of_files = count($_FILES['files']['name']);
        $images = $request->file('files');

        $errors = 0;

        $photos = [];//////추후에 사용하기 위해....

        for ($i = 0; $i < $number_of_files; $i++)
        {
            $_FILES['files']['name'] = $files['files']['name'][$i];

            //파일이름 앞에 상품 아이디를 첨가하여 업로드하는 파일 명을 다시 만든다(중복방지를 위해)
            //$productNo = 100001;////////////////////////////////////////////////지울것
            //$fileName = $productNo . "_";
            // $fileName .= $_FILES['files']['name'];

            $fileName = $_FILES['files']['name'];

            $photos[$i] = $fileName;//////

            $type = $files['files']['type'][$i];
            $size = $files['files']['size'][$i];
            if (($type != "image/jpeg") && ($type != "image/png" ) &&
                ($type != "image/jpg" ) && ($type != "image/gif" ))
            {
                $errors++;
            }

            if ($size > 1024000) //byte
            {
                $errors++;
            }
            if ($errors == 0)
            {
                ////아래는 local disk에 upload
                // $images[$i]->storeAs('products',$fileName,'public',); //storage/app/public/products
                ////아래는 upload 후에 public access 불가
                //$images[$i]->storeAs('products',$fileName,'s3',); //amazon s3/hdkwonnz.openmarket/products
                ////아래는 upload 후에 public access 가능
                $images[$i]->storePubliclyAs('products',$fileName,'s3',); //amazon s3/hdkwonnz.openmarket/products
            }
        }

        return $photos;

    }

    public function showEditProduct($productId)
    {

        return view('seller/product/showEditProduct',compact('productId'));

    }

    public function showMyProducts()

    {
        return view('seller.product.showMyProducts');

    }

    public function getMyProductById()
    {
        $product = Product::with('categorya','categoryb','categoryc')
                                ->where('user_id','=',auth()->user()->id)
                                ->where('id','=',request('productId'))
                                ->first();

        if (!$product){
            return response()->json([
                'errorMsg' => "Not found product Id."
            ]);
        }

        $photos = unserialize($product->photo_paths);
        //$imagePath = unserialize($product->image_path);

        return response()->json([
            'product' => $product,
            'photos' => $photos,
            //'imagePath' => $imagePath,
            'errorMsg' => null,
        ]);
    }

    public function getMyProducts()
    {
        $products = Product::where('user_id','=',auth()->user()->id)
                                ->orderBy('created_at','desc')
                                ->get();

        return response()->json([
            'products' => $products,
        ]);
    }

    public function showProductInputForm()
    {
        return view('seller.product.productInput');
    }

    public function addProduct(Request $request)
    {
        //return $request->all();

        ////https://stackoverflow.com/questions/42342411/laravel-validation-check-array-size-min-and-max
        $request->validate([
            'categoryAid' => 'required|numeric',
            'categoryBid' => 'required|numeric',
            'categoryCid' => 'required|numeric',
            'productName' => ["required","min:2","max:200"],
            'normalPrice' => ["required","numeric"],
            'salePrice' => ["nullable","numeric"],
            'stockQty' => ["nullable","numeric"],
            'imagePath' => ["required","min:2","max:255"],
            'photoPaths' => ["nullable","max:255"],
            'detailsPath' => ["nullable","max:255"],
            'countryOfOrigin' => ["required", "max:15"],
            'manufacturer' => ["nullable","max:255"],
            'option' => ["nullable","numeric"],
            'files' => ['required'],
        ]);

        ////////////////////////////////////////////////////////////////////

        $files = $_FILES; //File inputs are not put into $_POST, they're only in $_FILES.
        $number_of_files = count($_FILES['files']['name']);
        if ($number_of_files < 1){
            return response()->json([
                'errorMsg' => 'Please select photos.'
            ]);
        }

        $images = $request->file('files');

        $errors = 0;

        $selectedPhotos = [];

        for ($i = 0; $i < $number_of_files; $i++){
            $_FILES['files']['name'] = $files['files']['name'][$i];

            $fileName = $_FILES['files']['name'];

            //$selectedPhotos[$i] = $fileName;//////

            $type = $files['files']['type'][$i];
            $size = $files['files']['size'][$i];
            if (($type != "image/jpeg") && ($type != "image/png" ) &&
                ($type != "image/jpg" ) && ($type != "image/gif" ))
            {
                $errors++;
            }

            if ($size > 1024000) //byte
            {
                $errors++;
            }
            if ($errors < 1){
                ////아래는 local disk에 upload
                // $images[$i]->storeAs('products',$fileName,'public',); //storage/app/public/products
                ////아래는 upload(amazon s3) 후에 public access 불가
                //$images[$i]->storeAs('products',$fileName,'s3',); //amazon s3/hdkwonnz.openmarket/products
                ////아래는 upload(amazon s3) 후에 public access 가능
                $images[$i]->storePubliclyAs('products',$fileName,'s3',); //amazon s3/hdkwonnz.openmarket/products
                $selectedPhotos[$i] = Storage::cloud()->url('products/'.$fileName);//get url for uploaded photo
                ////https://stackoverflow.com/questions/52598891/how-can-i-get-the-full-url-of-file-uploaded-to-s3-with-laravel
                ////return Storage::cloud()->url($fileName);//get url for uploaded photo
            }else{
                return response()->json([
                    'errorMsg' => "Photos have problems with over size or wrong extention.",
                ]);
            }

            $serializedSelectedPhotos = serialize($selectedPhotos);

        }

        ////////////////////////////////////////////////////////////////////

        $photoPaths = explode(',', $request->photoPaths);
        if ($photoPaths[0] == ""){
            $serializedPhotos = null;
        }else{
            $serializedPhotos = serialize($photoPaths);
        }

        if (!($request->salePrice)){
            $request->salePrice = $request->normalPrice;
        }

        $product = Product::create([
            'status' => 'pending',
            'categorya_id' => $request->categoryAid,
            'categoryb_id' => $request->categoryBid,
            'categoryc_id' => $request->categoryCid,
            'user_id' => auth()->user()->id,
            'name' => $request->productName,
            'price' => $request->normalPrice,
            'sale_price' => $request->salePrice,
            'stock' => $request->stockQty,
            'country_origin' => $request->countryOfOrigin,
            // 'image_path' => $request->imagePath,//will be changed
            'image_path' => $serializedSelectedPhotos,//will be changed
            'photo_paths' => $serializedPhotos,//will be changed
            // 'details_path' => $request->detailsPath,//will be changed
            'details_path' => $serializedSelectedPhotos,//will be changed
            'ingredients' => $request->ingredients,
            //'number_option' => 0,///////////
            'sku' => $request->skuNumber,
            'brand' => $request->manufacturer,
            'number_option' => $request->option,
        ]);

        return response()->json([
            'productId' => $product->id,
        ]);
    }

    public function getCategoryAs()
    {
        //redis에 cache key 'seller.categoryAs'가 존재하면 cache에서 data를 읽어 오고
        //그렇지 않으면 db에서 읽어온 data를 cache에 저장 한다.
        $categoryAs = Cache::store('redis')->remember('seller.categoryAs', now()->addHours(24), function() {
            return (Categorya::all());
        });

        // $categoryAs = Categorya::all();

        return response()->json([
            'categoryAs' => $categoryAs,
        ]);
    }

    public function getCategoryBbyId()
    {
        $aId = request('aId');

        //redis에 cache key 'seller.categoryBs'가 존재하면 cache에서 data를 읽어 오고
        //그렇지 않으면 db에서 읽어온 data를 cache에 저장 한다.
        $categoryBs = Cache::store('redis')->remember('seller.categoryBs'.$aId, now()->addHours(24),
             function() use($aId){
                return (Categoryb::where('categorya_id','=', $aId)
                        ->get()
                );
        });

        // $categoryBs = Categoryb::where('categorya_id','=',request('aId'))
        //                        ->get();

        return response()->json([
            'categoryBs' => $categoryBs,
        ]);
    }
    public function getCategoryCbyId()
    {
        $aId = request('aId');
        $bId = request('bId');

        //redis에 cache key 'seller.categoryCs'가 존재하면 cache에서 data를 읽어 오고
        //그렇지 않으면 db에서 읽어온 data를 cache에 저장 한다.
        $categoryCs = Cache::store('redis')->remember('seller.categoryCs'.$aId.$bId, now()->addHours(24),
            function() use($aId, $bId){
                return (Categoryc::where('categorya_id','=',$aId)
                    ->where('categoryb_id','=',$bId)
                    ->get()
                );
        });

        // $categoryCs = Categoryc::where('categorya_id','=',request(('aId')))
        //                         ->where('categoryb_id','=',request(('bId')))
        //                         ->get();

        return response()->json([
            'categoryCs' => $categoryCs,
        ]);
    }
}
