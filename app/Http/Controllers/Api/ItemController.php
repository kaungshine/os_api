<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Item;
use Illuminate\Http\Request;
use App\Http\Resources\ItemResource;
use App\Brand;
use App\Subcategory;

class ItemController extends Controller
{
    public function __construct($value='')
    {
        $this->middleware('auth:api')->except('index', 'filter');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Item::all();
        return response()->json([
            "status" => "ok",
            "totalResults" => count($items),
            "items" => ItemResource::collection($items)
        ]);
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
        $request->validate([
        'codeno' => 'required',
        'name' => 'required',
        'photo' => 'required',
        'price' => 'required',
        'discount' => 'required',
        'description' => 'required',
        'brand' => 'required',
        'subcategory' => 'required',
]);
        //File upload
        $imageName = time().'.'.$request->photo->extension();

        $request->photo->move(public_path('backendtemplate/itemimg'), $imageName);

        $myfile = 'backendtemplate/itemimg/'.$imageName;
        //Store Data
        $item = new Item;
        $item->codeno = $request->codeno;
        $item->name = $request->name;
        $item->photo = $myfile;
        $item->price = $request->price;
        $item->discount = $request->discount;
        $item->description = $request->description;
        $item->brand_id = $request->brand;
        $item->subcategory_id = $request->subcategory;
        $item->save();

        //Redirect
       // return redirect()->route('items.index')->with('status', 'stored');
        return new ItemResource($item);
    }

    

    /**
     * Display the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        //
    }

    // public function filter($sid,$bid)
    // {
    //     $items = array();
    //     if ($sid && $bid) {
    //         $items = Item::where('subcategory_id',$sid)
    //         ->where('brand_id', $bid)
    //         ->get();

    //     }else{
    //         $items = Item::where('subcategory_id',$sid)
    //         ->or_where('brand_id', $bid)
    //         ->get(); 
    //     }
    //     return $items;
    // }

    public function filter(Request $request)
    {
        $query = $request->query();
        $items = Item::all();
        $results = collect([]);
        
        foreach ($query as $key => $value) {
            if($key == "subcategory")
            {
                $subcategory = Subcategory::where('name', 'Like', '%' . $value . '%')->get();
                if(count($subcategory) > 0)
                    $results = $items->where('subcategory_id', $subcategory[0]->id);
            }
            elseif($key == "brand")
            {
                $brand = Brand::where('name', 'Like', '%' . $value . '%')->get();
                if(count($brand) > 0)
                    $results = $items->where('brand_id', $brand[0]->id);
            }
        }

        return response()->json([
                "status" => "ok",
                "totalResults" => count($results),
                "items" => ItemResource::collection($results),
        ]);
    }
}
