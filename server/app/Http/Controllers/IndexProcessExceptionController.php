<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\IndexProcessException;
use App\AppDocuments;
use App\TrackIndexProcess;
use Google\Cloud\Storage\StorageClient;
use Validator;

class IndexProcessExceptionController extends Controller
{
    private $googleCoudProject;
    private $cloudStorageKeyFilePath;

    public function __construct()
    {
        $this->googleCoudProject = 'agent-admin-255615';
        $this->cloudStorageKeyFilePath = __DIR__.'\key\agent-admin-255615-370467a434ea.json';
        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return response(IndexProcessException::where('mannual_indexed', 0)->paginate(10,['id','fetched_entity_name','refined_fetched_entity_name','fetched_entity_address','object_name','fetched_state','created_at','received_at', 'process_at']), 200)
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
    public function store(Request $request, FileIndexController $indexController)
    {
        //
       
        $rules =[
            'name'  => 'required|min:5',
        ];

        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()){
            return response([
                'error'=>true,
            'message'=>$validate->errors()], 201)->
            header('Content-Type', 'application/json');
        }

        $reindexRequire = IndexProcessException::find($request->post('id'));
        
        if($reindexRequire !== null){
            
            $sourceFileObjectName = str_replace(['jpeg/','_.jpg'],['PDF/','.pdf'], $reindexRequire->object_name);
            $sourceFileName = substr($sourceFileObjectName,strrpos($sourceFileObjectName,'/')+1);
            $indexedObjectName = str_replace(' ', '_', trim($indexController->refindedEntityName( $request->post('name') ) )).
            "-".$reindexRequire->fetched_state."/"
            .$reindexRequire->received_at."/".$sourceFileName;
            $storage = $indexController->getStorageObject();
            $bucketObject = $storage->bucket($reindexRequire->bucket_name);

            $sourceObjectContent = $indexController->downloadObjectStream($bucketObject,$reindexRequire->object_name);
            
            $reponseIndexedFile = $indexController->uploadEncryptedObjectToIndexed($storage,$indexedObjectName,$sourceObjectContent);
            
            $appDetaill = new AppDocuments();
            $appDetaill->app_id = 1;
            $appDetaill->document_path = $indexedObjectName;
            $appDetaill->bucket_name = $reponseIndexedFile['bucketName'];
            $appDetaill->encryption_key = $reponseIndexedFile['encryptionKey'];
            $appDetaill->encryptionsha256_key = $reponseIndexedFile['encryptionKeySHA256'];
            $appDetaill->received_at = $reindexRequire->received_at;
            $appDetaill->process_at = $reindexRequire->process_at;
            $appDetaill->save();
            
            $indexInfo = new TrackIndexProcess();
            $indexInfo->document_id = $appDetaill->document_id;
            $indexInfo->object_name = $reindexRequire->object_name;
            $indexInfo->save();

            $reindexRequire->entered_entity_name = $request->post('name');
            $reindexRequire->mannual_indexed =1;
            $reindexRequire->save();
            return response([
                'error'=>false,
            'message'=>'File successfully re-index!'], 200)->
            header('Content-Type', 'application/json');
        }

        
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
        $detail = IndexProcessException::find($id);

        if($detail===null){
            return response(['error'=>true,'message'=>'Data not found.'],201)
            ->header('Content-Type','application/json');
        }
        $detail->object_name = $this->getObjectSignedUrl($detail->bucket_name,$detail->object_name);
        return response(['error'=>false,'data'=>$detail],200)
            ->header('Content-Type','application/json');
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

    /** 
    * get object signed URL for the object
    *
    * @param string $bucketName the name of your Google Cloud bucket.
    * @param string $objectName the name of your Google Cloud object.
    *
    * @return object link
    */

    public function getObjectSignedUrl($bucketName, $objectName)
    {
      
      $storage = new StorageClient([
         'keyFile' => json_decode(file_get_contents($this->cloudStorageKeyFilePath), true),
         'projectId' => $this->googleCoudProject
      ]);
      
      $bucket = $storage->bucket($bucketName);
      $object = $bucket->object($objectName);
      $signedUrl = $object->signedUrl(new \DateTime('20 min'),[
         'version' => 'v4',
         'method' => 'GET',
      ]);

     return $signedUrl;

    }
}
