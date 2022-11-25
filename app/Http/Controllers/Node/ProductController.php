<?php

namespace App\Http\Controllers\Node;

use App\Models\Device;
use App\Models\DeviceMission;
use App\Models\GroupMission;
use App\Models\Network;
use App\Models\Product;
use App\Models\Type;
use App\Models\User;
use App\Services\TypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ProductController extends Common4Controller
{
    private $typeService;

    public function __construct(
        TypeService       $typeService

    )
    {
        $this->typeService       = $typeService;
    }
    /**
     * Display a listing of the production.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $user = session('user');
        $category = $request['category'];
        $type_id = $request['type_id'];
        $isNew = $request['isNew'];
        if($isNew == null) {
            $isNew = 0;
        }
        $mac = $request['mac'];
        if($category == null) {
            $category = 0;
        } else {
            $category = (int)$category;
        }

        //類型下拉選單
        $types =  $this->typeService->getTypesByCategory($category);
        if($types->count() > 0 && $type_id ==null ){
            $type_id = $types[0]['type_id'] ;
        } else {
            $type_id = (int)$type_id;
        }
        $user = session('user');
        $products = null;
        $product_group = null;
        if($mac != null) {
            $products = Product::where('macAddr', 'like', '%' . $mac . '%')
                ->orderBy('created_at', 'desc')
                ->get();
            $product_group = DB::table('products')
                ->select('created_at', DB::raw('count(*) as total'))
                ->where('macAddr', 'like', '%' . $mac . '%')
                ->orderBy('created_at', 'desc')
                ->groupBy('created_at')
                ->get();
        } else {
            $products = Product::where('type_id', $type_id)
                ->orderBy('created_at', 'desc')
                ->get();
            $product_group = DB::table('products')
                ->select('created_at', DB::raw('count(*) as total'))
                ->where('type_id', $type_id)
                ->orderBy('created_at', 'desc')
                ->groupBy('created_at')
                ->get();
        }


        // dd($devices);
        return view('nodes.products', compact(['types', 'products', 'product_group','type_id', 'category', 'user', 'isNew']));
    }

    /**
     * Update  the product.
     *
     * @param Request $request
     * @return View
     */
    public function update(Request $request)
    {

        $user = session('user');
        $input = $request->all();
        unset($input['_token']);
        unset($input['_method']);
        $id = $input['id'];
        unset($input['id']);
        $rules = [
            'mac' => 'required',
            'type_id' => 'required',
        ];
        if($id == 0) {
            $rules['mac'] = 'required|between:12,12';
        }
        $msg = [
            'mac.required' => trans('device.device_mac_required'),
            'mac.between' => trans('device.device_mac_length'),
            'type_id.required' => trans('device.type_id_required')
        ];
        $validator = Validator::make($input, $rules, $msg);
        if(count($validator->errors()->all()) > 0){
            session(['error'=> $validator->errors()]);
            return back()->withErrors($validator);
        }
        if($id>0)
            $product = Product::find($id);
        else {
            //Verify mac is from yesio or not?
            $checkProduct = DB::table('products')->where('macAddr', $input['mac'])->get();
            if($checkProduct->count() > 0) {
                return back()->withErrors(['本公司已經有同樣MAC!']);
            }
            $product = new Product;
        }

        $product->type_id = $input['type_id'];
        $product->macAddr = $input['mac'];
        if($input['description'] != null)
            $product->description = $input['description'];
        $product->save();
        return back();
    }

    /**
     * Batch update  the product.
     *
     * @param Request $request
     * @return View
     */
    public function import(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $macs = json_decode($input['macs']);
        $errorMac = '';
        $okMac = '';
        foreach ($macs as $item) {
            $checkProduct = DB::table('products')->where('macAddr', $item->mac)->get();
            if($checkProduct->count() > 0) {
                if(strlen($errorMac) == 0)
                    $errorMac = $errorMac.$item->mac;
                else
                    $errorMac = $errorMac.','.$item->mac;
            } else {
                $product = new Product;
                $product->type_id = $input['type_id'];
                $product->macAddr = $item->mac;
                if(isset($item->description) && $item->description != '') {
                    $product->description = $item->description;
                }

                if(isset($item->date) && $item->date != '') {
                    $product->created_at = $item->date;
                }

                $product->save();
            }
        }

        if(strlen($errorMac) == 0)
            return back();
        else
            $errorMac = '重複加入產品MAC: 如下'.$errorMac;
        return back()->withErrors($errorMac);
    }

    public function destroy(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        unset($input['id']);
        $product = Product::find($id);
        $device = $product->device;

        if($device != null) {
            $mission = $device->mission;
            if($mission!=null) {
                DeviceMission::where('mission_id', $mission->id)->delete();
                GroupMission::where('mission_id', $mission->id)->delete();
                $mission->delete();
            }
            $device->delete();
        }

        $product->delete();

        return back();
    }
}
