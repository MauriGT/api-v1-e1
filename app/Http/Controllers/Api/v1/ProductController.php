<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Nette\Utils\Image;
use Yajra\DataTables\DataTables;
use function response;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return Datatables::of($products)
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {

        $validator = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'brand' => 'required|string',
            'price' => 'required|numeric',
            'price_sale' => 'required|numeric',
            'category' => 'required|string',
            'stock' => 'required|numeric',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->brand = $request->brand;
        $product->price = $request->price;
        $product->price_sale = $request->price_sale;
        $product->category = $request->category;
        $product->stock = $request->stock;

        $url_image = $this->uploadFile($request);
        $product->image = $url_image;
        $result = $product->save();

        if ($result) {
            return response()->json([
                "success" => true,
                "message" => "Product created successfully.",
                "data" => $product],
                201);
        }
        return response()->json(['message' => 'Error to create product.'], 500);
    }


    private function uploadFile($request): string
    {


        $path_info = pathinfo($request->file('image')->getClientOriginalName());
        $post_path = 'images/products';
        $rename = uniqid() . '.' . $path_info['extension'];
        $request->file('image')->storeAs($post_path, $rename, 'public');

        return "$post_path/$rename";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {

        $product = Product::find($id);
        if ($product) {
            unlink(public_path() . "/storage/" . $product->image);
            $product->delete();
            return response()->json([
                "success" => true,
                "message" => "Product deleted successfully."], 200);
        }

        return response()->json(['message' => 'Product not found'], 404);


    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $product = Product::find($id);
        if ($product) {
            return response()->json([
                "product" => $product], 200);
        }
        return response()->json(['message' => 'Product not found.'], 404);

    }

    public function update(Request $request, $id)
    {


        $validator = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'brand' => 'required|string',
            'price' => 'required|numeric',
            'price_sale' => 'required|numeric',
            'category' => 'required|string',
            'stock' => 'required|numeric',
        ]);
        $product = Product::find($id);
        if ($product) {

            unlink(public_path() . "/storage/" . $product->image);

            $product->name = $request->name;
            $product->description = $request->description;
            $product->brand = $request->brand;
            $product->price = $request->price;
            $product->price_sale = $request->price_sale;
            $product->category = $request->category;
            $product->stock = $request->stock;

            $url_image = $this->uploadFile($request);
            $product->image = $url_image;
            $result = $product->update();

            if ($result) {
                return response()->json([
                    "success" => true,
                    "message" => "Product Updated successfully.",
                    "data" => $product],
                    201);
            }
            return response()->json(['message' => 'Error to update product.'], 500);
        }

        return response()->json(['message' => 'Product not found.'], 404);
    }
}
