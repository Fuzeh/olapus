<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Settings;

class SettingsController extends Controller
{
    /**
     * Module basic path
     */
    protected $moduleBasicRoute = 'admin.settings';
    
    /**
     * View basic path
     */
    protected $moduleBasicTemplatePath = 'admin.modules.settings';
    
    /**
     * Validation rules
     */
    protected $arValidationArray = [
                    'name' => 'required|max:255|unique:settings',
                    'value' => 'max:255',
                    'description' => 'max:255'];
    
    /**
     * Constructor
     */
    public function __construct() {
        View::share('moduleBasicRoute', $this->moduleBasicRoute);
        View::share('moduleBasicTemplatePath', $this->moduleBasicTemplatePath);
        
        /**
         * Module name for blade
         */
        $temp = explode('.', $this->moduleBasicRoute);
        View::share('moduleNameBlade', $temp[0]."_module_".$temp[1]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /**
         * Handle saved settings
         */
        $redirectRoute = resetSaveIndexParameters($this->moduleBasicRoute);
        if($redirectRoute !== FALSE){
            
            return redirect($redirectRoute);
        }
        
        /**
         * Get the rows
         */
        $arResults = Settings::where( function($query) {
                $query->allColumns();
        })->orderByColumns()->paginate(env('ADMIN_PAGINATE', 10));

        /**
         * Return page
         */
        return view($this->moduleBasicTemplatePath.'.index', ['results' => $arResults]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /**
         * Return page
         */
        return view($this->moduleBasicTemplatePath . '.create_edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**
         * Validate input
         */
        $this->validate($request, $this->arValidationArray);
        
        /**
         * Create row
         */
        Settings::create([
            'name' => $request['name'],
            'value' => $request['value'],
            'description' => $request['description'],
        ]);
        
        /**
         * Redirect to index
         */
        return redirect(route($this->moduleBasicRoute . '.index'));
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
        /**
         * Get the row
         */
        $arResults = Settings::find($id);
        
        /**
         * Row does not exist - redirect
         */
        if($arResults == FALSE){
            
            return redirect(route($this->moduleBasicRoute . '.index'))->withInput()->withErrors(['edit' => trans('validation.row_not_exist')]);
        }
        
        /**
         * Set the put method for update
         */
        $arResults['_method'] = 'PUT';
        
       /**
         * Return page
         */    
        return view($this->moduleBasicTemplatePath . '.create_edit', ['results' => $arResults]);
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
        /**
         * Validate input
         */
        $this->validate($request, [
                    'name' => 'required|max:255|unique:settings,name,'.$id,
                    'value' => 'max:255',
                    'description' => 'max:255']);
        
        /**
         * Get the row
         */
        $arResults = Settings::find($id);
        
        /**
         * Row does not exist - redirect
         */
        if($arResults == FALSE){
            
            return redirect(route($this->moduleBasicRoute . '.index'))->withInput()->withErrors(['edit' => trans('validation.row_not_exist')]);
        }
        
        /**
         * Set updated values
         */
        $arResults->name = $request['name'];
        $arResults->value = $request['value'];
        $arResults->description = $request['description'];
        
        /**
         * Save the changes
         */
        $arResults->save();
        
        /**
         * Return to index
         */
        return redirect(route($this->moduleBasicRoute . '.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /**
         * Delete the setting
         */
        Settings::destroy($id);
        
        /**
         * Redirect to index
         */
        return redirect(route($this->moduleBasicRoute . '.index'));
    }
}
