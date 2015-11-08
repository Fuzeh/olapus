<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

/**
 * Description of AdminModuleTrait
 *
 * @author Uzivatel
 */
trait AdminModuleTrait {
    
    /**
     * Module name
     */
    protected $moduleName;
    
    /**
     * Module basic path
     */
    protected $moduleBasicRoute;
    
    /**
     * View basic path
     */
    protected $moduleBasicTemplatePath;
    
    /**
     * Model Class
     */
    protected $modelClass;
    
    /**
     * Constructor
     */
    public function __construct() {
        
        /**
         * Get module name
         */
        $this->moduleName = str_replace('Controller', '', class_basename(__CLASS__));
        
        $this->modelClass = '\\App\\'.$this->moduleName;
        
        /**
         * Set variables
         */
        $this->moduleBasicRoute = 'admin.'.lcfirst($this->moduleName);
        $this->moduleBasicTemplatePath = 'admin.modules.'.strtolower($this->moduleName);
        
        /**
         * Global templates
         */
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
    public function index(Request $request)
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
        $modelClass = $this->modelClass;
        $arResults = $modelClass::where( function($query) {
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
        $inputsToSave = array();
        foreach ($this->arValidationArray as $name => $value){
            $inputsToSave[$name] = $request[$name];
        }
        $modelClass = $this->modelClass;
        $modelClass::create($inputsToSave);
        
        /**
         * Redirect to index
         */
        return redirect(route($this->moduleBasicRoute . '.index'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $modelClass = $this->modelClass;
        
        /**
         * Get the row
         */
        $arResults = $modelClass::find($id);
        
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /**
         * Change the validation array
         */
        foreach ($this->arValidationArrayUpdateChange as $name => $value){
            $this->arValidationArray[$name] = $this->arValidationArray[$value].','.$id;
        }
        /**
         * Validate input
         */
        $this->validate($this->arValidationArray);
        
        /**
         * Get the row
         */
        $modelClass = $this->modelClass;
        $arResults = $modelClass::find($id);
        
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
        $arResults->description = $request['description'];
        $arResults->color = $request['color'];
        
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
        $modelClass = $this->modelClass;
        $modelClass::destroy($id);
        
        /**
         * Redirect to index
         */
        return redirect(route($this->moduleBasicRoute . '.index'));
    }
}
