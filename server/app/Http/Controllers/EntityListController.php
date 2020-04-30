<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EntityList;
use Validator;

class EntityListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        return response(EntityList::paginate(10,['id','entity_name','entity_state','created_at']), 200)
        ->header('Content-Type', 'application/json');
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules =[
            'entity_name'  => 'required|min:5',
            'entity_state' => 'required|min:2',
        ];

        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()){
            return response([
                'error'=>true,
            'message'=>$validate->errors()], 400)->
            header('Content-Type', 'application/json');
        }

        $insert = new EntityList;
        $insert->entity_name =  $request->post('entity_name');
        $insert->refined_entity_name =  $this->refindedEntityName($request->post('entity_name'));
        $insert->entity_state =  $request->post('entity_state');
        $insert->save();
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
        $entity = EntityList::find($id);
        if($entity === null){
            $entity = [
                'error' => true,
                'message' =>'Data not found.'
            ];
        }
        
        return response(['data' => $entity], 200)
        ->header('Content-Type', 'application/json');
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

    public function refindedEntityName($entityName)
    {
      return preg_replace('/\s+/', ' ', 
      str_replace([',','.'],['',''],
      trim($entityName)));
    }
}
