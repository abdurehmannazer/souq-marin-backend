<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController;


use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $products = Product::with('user')->get();
            $data = [
                "products" => $products,
            ];

            return $this->BaseResponse(true, "all products ", $data  );


        } catch (\Exception $e) {
            return $this->BaseResponse(false, "fail to get all products", ["error" , $e->getMessage()]  );

        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1️⃣ Validate request
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|string',
            'city'        => 'required|string|max:255',
            'categories'  => 'required|array',
            'categories.*.categoryLevel' => 'required|string',
            'categories.*.categoryID'    => 'required|string',
            'files'       => 'nullable|array',
            'files.*' => 'nullable|file|mimetypes:image/jpeg,image/png,image/webp,application/pdf|max:2048',
        ]);

        // 2️⃣ Handle file uploads
        $filePaths = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('products', $filename, 'public');
                $filePaths[] = $path;
            }
        }


        // 3️⃣ Create new product and bind to user
        $product = Product::create([
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'] ?? null,
            'city' => $validated['city'] ?? null,
            'categories' => $validated['categories'] ?? null,
            'files' => $filePaths,
            'user_id' => auth()->id(), // logged-in user
        ]);

        $data = [
            "request" => $request->all(),
            "created product" => $product
        ];

        // 4️⃣ Return response
        return $this->BaseResponse(true, "Product created successfully", $data , );


    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
