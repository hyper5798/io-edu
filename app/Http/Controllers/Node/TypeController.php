<?php

namespace App\Http\Controllers\Node;

use App\Models\Setting;
use Hamcrest\Core\Set;
use Illuminate\Http\Request;
use App\Models\Type;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class TypeController extends Common4Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $types =  Type::all();
        $setting = Setting::where('field','device_type')->first()->set;

        return view('nodes.types', compact(['types', 'setting']));
    }

    public function update(Request $request)
    {
        $input = $request->all();
        unset($input['_token']);
        unset($input['_method']);
        $mytype = $input['mytype'];

        $category = $input['category'];
        $work = $input['work'];
        if($mytype == null) {
            return back();
        }
        $data = json_decode($mytype);

        if($data->fields != null && gettype($data->fields) == 'string') {
            if($data->fields == '{}') {
                $data->fields = null;
            } else {
                $data->fields = json_decode($data->fields);
            }
        }
        if($data->rules != null && gettype($data->rules) == 'string') {
            $data->rules = json_decode($data->rules);
        }
        if($data->id == 0) {
            $type = new Type;
        } else {
            $id = $data->id;
            $type = Type::find($id);
        }
        if($category != null) {
            $type->category = (int)$category;
        } else {;
            $type->category = $category;
        }
        $type->work = $work;
        $type->type_id = $data->type_id;
        $type->type_name = $data->type_name;
        $type->description = $data->description;
        $type->fields = $data->fields;
        $type->rules = $data->rules;
        $type->save();

        return back();
    }

    public function destroy(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        unset($input['id']);
        Type::where('id', $id)->delete();
        return back();
    }

    /**
     * Upload image of type in storage.
     *
     * @param  Request $request
     * @return View
     */
    public function uploadTypeImage(Request $request)
    {
        $input = $request->all();
        $id = isset($input['id']) ? (int)$input['id'] : 0 ;

        $file_url = null;
        if($request->hasFile('type_img')){
            //dd('hasImage');
            $file = $request->file('type_img');
            $file_name = $file->getClientOriginalName();
            $save = $this->resizeImage($file, $file_name);
            if(!$save) {
                return back();
            }
            $path = 'public/photo/'.$file_name;
            $file_url = url(Storage::url($path));

        } else {
            return back();
        }

        $type = Type::where('id', $id)->first();
        $type->image_url = $file_url;
        $type->save();

        return back();
    }

    /**
     * Resizes a image using the InterventionImage package.
     *
     * @param object $file
     * @param string $fileNameToStore
     * @author Niklas Fandrich
     * @return bool
     */
    public function resizeImage($file, $fileNameToStore) {
        // Resize image
        $resize = Image::make($file)->resize(512, null, function ($constraint) {
            $constraint->aspectRatio();
        })->encode('jpg');

        // Create hash value
        $hash = md5($resize->__toString());

        // Prepare qualified image name
        $image = $hash."jpg";

        // Put image to storage
        $save = Storage::put("public/photo/{$fileNameToStore}", $resize->__toString());

        if($save) {
            return true;
        }
        return false;
    }
}
