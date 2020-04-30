<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Vision\VisionClient;
use Mtownsend\XmlToArray\XmlToArray;
use App\AppDocuments;
use App\TrackIndexProcess;
use App\EntityList;
use App\IndexProcessException;


class FileIndexController extends Controller
{

private $indexedBucketName ;
private $nonIndexedBucketName ;
private $cloudStorageKeyFilePath;
private $cloudVisionKeyFilePath;
private $googleCoudProject;

function __construct()
{
   // old
   /* $this->cloudStorageKeyFilePath = __DIR__.'\key\lorax-system-4c1ab33ad6ad.json';
   $this->googleCoudProject = 'lorax-system';
   $this->indexedBucketName = 'lorax_system_indexed_files';
   $this->nonIndexedBucketName = 'lorax_system_non-indexed_files'; */
   
   $this->indexedBucketName = 'lorax_indexed_files';
   $this->nonIndexedBucketName = 'lorax_non-indexed_files';
   $this->googleCoudProject = 'agent-admin-255615';
   $this->cloudStorageKeyFilePath = __DIR__.'\key\agent-admin-255615-370467a434ea.json';
    
   // for linux we need to change /key/loraxlorax-ocr-6902800a657a.json
   $this->cloudVisionKeyFilePath = __DIR__.'\key\agent-admin-255615-620c97f47453.json';
}

   public function accessIndexedObject(Request $request)
   {
      $objectName = $request->query('name');
      $documentInfo = AppDocuments::where('document_path','like', '%'. $objectName.'%')->first();
      //dd($documentInfo);
      $response = $this->getEncryptedObjectSignedUrl($this->indexedBucketName, $documentInfo->document_path, $documentInfo->encryption_key, $documentInfo->encryptionsha256_key );
     
      //dd($response);
            return view('view-object',[
               'url' => $response['url'],
               'x-goog-encryption-algorithm' => 'AES256',
                'key' => $response['key'],
                'keySHa256' => $response['keySHa256']
                ]);

   }

   /**
    * Read Non-indexed file and indexed that file
    * 
    * @return Response  
    */

    public function readInputFiles()
    {
      //$inputfolderName = '000118_165903_03052020-jpeg/';
      //$inputfolderName = 'jpeg';
      
/*       $entityList = CopyEnity::all(['entity_name']);
      //dd($entityList);
      foreach($entityList as $list){
         //dd($list);
         $insertEntity =  new EntityList;
         $insertEntity->entity_name = $list->entity_name;
         $insertEntity->refined_entity_name = $this->refindedEntityName($list->entity_name);
         $insertEntity->entity_state = 'DE';
         $insertEntity->save();
      }

     die('ok'); */
     /*  $entityLists = EntityList::all(['id','entity_name']);

     
      foreach ($entityLists as $entity) {
        $updateEntity =  EntityList::find($entity->id);
        $updateEntity->refined_entity_name = $this->refindedEntityName($entity->entity_name);
        $updateEntity->entity_state = 'DE';
        $updateEntity->save();

      }
      die; */
      $storage = new StorageClient([
         'keyFile' => json_decode(file_get_contents($this->cloudStorageKeyFilePath), true),
         'projectId' => $this->googleCoudProject
      ]);

      // bucket name
       //$bucketName = 'lorax_system_non-indexed_files'; old
       //$bucketName = 'lorax_non-indexed_files';
      
       // bucket name
      $bucket = $storage->bucket($this->nonIndexedBucketName);
      /* dd($bucket); */
      foreach ($bucket->objects(/* [ 'prefix'=> $inputfolderName] */) as $object) {
         // printf('Object: %s' . PHP_EOL, $object->name());
         //die;
         if(strpos($object->name(),'-jpeg/') 
            &&  TrackIndexProcess::where('object_name', $object->name())->first() === null ){
               //fetch first level folder name from complete object name
               $folderName = substr($object->name(),0, strpos($object->name(),'/')+1);
               
               foreach($bucket->objects([ 'prefix'=> $folderName]) as $objectXml){
                  if(strpos($objectXml->name(),'.xml')){
                     $path = storage_path("app\public\\").'output.xml';
                     $objectXml->downloadToFile($path);
                     $xml= file_get_contents($path);
                     $xmlFileArray = XmlToArray::convert($xml);
                     //dd($xmlFileArray);
                     $commonAddress = $xmlFileArray['Transaction'][0]['commonAddress'];
                     $state ='DE';
                     $receivedDate = $xmlFileArray['@attributes']['ReceiveDate'];
                     $processDate = $xmlFileArray['@attributes']['ProcessDate'];
                     //echo $commonAddress;
                  break;
                  }
               }
               
            
               //echo $object->name().'<br>';
               if(!strpos($object->name(),'.xml')){
                  $stream = $object->downloadAsStream();
                  $OcrResponse = $this->OCR($stream->getContents(), $commonAddress, $state);
                  //echo $stream->getContents();
                  if(!$OcrResponse['error']){
                     if(EntityList::where('refined_entity_name',$OcrResponse['refined_entity_name'])
                     ->where('entity_state',$state)->first() === null){

                        if(IndexProcessException::where('object_name',$object->name())->first() === null){
                           $indexException = new IndexProcessException();
                           $indexException->fetched_entity_name = $OcrResponse['entity_name'];
                           $indexException->refined_fetched_entity_name = $OcrResponse['refined_entity_name'];
                           $indexException->fetched_entity_address = $OcrResponse['entity_address'];
                           $indexException->fetched_state = $state;
                           $indexException->received_at = $receivedDate;
                           $indexException->process_at  = $processDate;
                           $indexException->bucket_name = $this->nonIndexedBucketName;
                           $indexException->object_name = $object->name();
                           $indexException->save();
                        }
                     }
                     else{

                        $sourceFileObjectName = str_replace(['jpeg/','_.jpg'],['PDF/','.pdf'], $object->name());
                        $sourceFileName = substr($sourceFileObjectName,strrpos($sourceFileObjectName,'/')+1);
                        $indexedObjectName = str_replace(' ', '_', trim($OcrResponse['refined_entity_name'])).
                        "-".$state."/"
                        .$receivedDate."/".$sourceFileName;
                        //dd($indexedObjettName);
                        ////dd($sourceFileObjectName );
                        if(AppDocuments::where('document_path',$indexedObjectName)->first() === null){
                           
                           $sourceObjectContent = $this->downloadObjectStream($bucket,$sourceFileObjectName);

                           $reponseIndexedFile = $this->uploadEncryptedObjectToIndexed($storage,$indexedObjectName,$sourceObjectContent);
                        // dd($reponseIndexedFile);
                           $appDetaill = new AppDocuments();
                           $appDetaill->app_id = 1;
                           $appDetaill->document_path = $indexedObjectName;
                           $appDetaill->bucket_name = $reponseIndexedFile['bucketName'];
                           $appDetaill->encryption_key = $reponseIndexedFile['encryptionKey'];
                           $appDetaill->encryptionsha256_key = $reponseIndexedFile['encryptionKeySHA256'];
                           $appDetaill->received_at = $receivedDate;
                           $appDetaill->process_at = $processDate;
                           $appDetaill->save();
                           
                           $indexInfo = new TrackIndexProcess();
                           $indexInfo->document_id = $appDetaill->document_id;
                           $indexInfo->object_name = $object->name();
                           $indexInfo->save();
                        }
                     }
                  }
         }
      }

     }
       
   }

   /*
    * Update the avatar for the user.
    *
    * @param  Request  $request
    * @return Response
    */

   public function update(Request $request)
   {
       
        $path = str_replace('/',"\\",$request->file('image')->store( 'input-files',['disk' => 'public']));
      // bucket name
      $bucketName = $this->indexedBucketName;
     $fileSource = storage_path("app\public\\"). $path;
     $objectName = "test/images/file-name01.jpg";

     $reponse = $this->uploadEncryptedObjectToCloud($bucketName, $objectName, $fileSource);
     
      if(!$reponse['error']){
         //$destination = storage_path("app\public\output-files\\output-image.jpg");
         //$this->downloadEncryptedObject($reponse['bucketName'], $reponse['objectName'], $destination, $reponse['encryptionKey'] );
         
         //$this->downloadEncryptedObjectStream($reponse['bucketName'], $reponse['objectName'], $reponse['encryptionKey'] );
        $sign = $this->getEncryptedObjectSignedUrl($reponse['bucketName'], $reponse['objectName'], $reponse['encryptionKey'], $reponse['encryptionKeySHA256'] );
        dd($sign);
      } 
      dd($reponse);
     
   }

      /**
    * upload file to google cloud storage bucket
    * @parma $bucketName String Bucket Name
    * @parma $objectName String complete path in seleted bucket 
    * @parma $fileSource String File path which need to be updated
    * @return Array
    */
    public function uploadToCloud($bucketName,  $objectName, $fileSource)
    {
       
       $responseArray = [];
       $storage = new StorageClient([
          'keyFile' => json_decode(file_get_contents($this->cloudStorageKeyFilePath), true),
          'projectId' => $this->googleCoudProject
       ]);
       
       // bucket name
       $bucket = $storage->bucket($bucketName);
     
       $options = [
          'resumable' => true,
          'name' => $objectName,
          'metadata' => [
              'contentLanguage' => 'en',
          ],
      ];
 
      try{
       
       //uploading file into google bucket
      $res = $bucket->upload(
          fopen($fileSource,'r'),
          $options
       );
       //dd($res->getEncryptionData());
       $responseArray['error'] = false;
       $responseArray['bucketName'] = $bucketName;
       $responseArray['objectName'] = $objectName;
 
      }
      catch(Exception $e){
       $responseArray['error'] = true;
       $responseArray['errorMessage'] = 'File did not upload!';
 
      }
       
      return $responseArray; 
    }

   /**
    * upload file to google cloud storage bucket
    * @parma $bucketName String Bucket Name
    * @parma $objectName String complete path in seleted bucket 
    * @parma $fileSource String File path which need to be updated
    * @return Array
    */
   public function uploadEncryptedObjectToCloud($bucketName,  $objectName, $fileSource)
   {
      
      $responseArray = [];
       // Authenticating with keyfile data.

      $storage = new StorageClient([
         'keyFile' => json_decode(file_get_contents($this->cloudStorageKeyFilePath), true),
         'projectId' => $this->googleCoudProject
      ]);
      
      // bucket name
      $bucket = $storage->bucket($bucketName);
    
      //create 32 byte encrypted key
      $key= random_bytes(32);
      // convert it to base64 
      $base64EncryptionKey = base64_encode($key); 
      //
      $base64EncryptionKeySha256 = base64_encode(hash('SHA256', $key, true));
 
      $options = [
         'resumable' => true,
         'name' => $objectName,
         'metadata' => [
             'contentLanguage' => 'en',
         ],
         'encryptionKey' => $base64EncryptionKey,
         'version' => 'v4',
         'encryptionKeySHA256' => $base64EncryptionKeySha256
     ];

     try{
      
      //uploading file into google bucket
     $res = $bucket->upload(
         fopen($fileSource,'r'),
         $options
      );
      //dd($res->getEncryptionData());
      $responseArray['error'] = false;
      $responseArray['encryptionKey'] = $base64EncryptionKey;
      $responseArray['encryptionKeySHA256'] = $base64EncryptionKeySha256;
      $responseArray['bucketName'] = $bucketName;
      $responseArray['objectName'] = $objectName;

     }
     catch(Exception $e){
      $responseArray['error'] = true;
      $responseArray['errorMessage'] = 'File did not upload!';

     }
      
     return $responseArray; 
   }
   /**
    * upload file to google cloud storage bucket
    * @parma $bucketName String Bucket Name
    * @parma $objectName String complete path in seleted bucket 
    * @parma $fileSource String File path which need to be updated
    * @return Array
    */
    public function uploadEncryptedObjectToIndexed( $storageObject, $objectName, $fileContent)
    {
       
       
       // bucket name
       $indexedBucket = $storageObject->bucket($this->indexedBucketName);
     
       //create 32 byte encrypted key
       $key= random_bytes(32);
       // convert it to base64 
       $base64EncryptionKey = base64_encode($key); 
       //
       $base64EncryptionKeySha256 = base64_encode(hash('SHA256', $key, true));
  
       $options = [
          'resumable' => true,
          'name' => $objectName,
          'metadata' => [
              'contentLanguage' => 'en',
          ],
          'encryptionKey' => $base64EncryptionKey,
          'version' => 'v4',
          'encryptionKeySHA256' => $base64EncryptionKeySha256
      ];
 
   try{
       
       //uploading file into google bucket
      $res = $indexedBucket->upload(
         $fileContent,
          $options
       );
       //dd($res);
       //dd($res->getEncryptionData());
       $responseArray['error'] = false;
       $responseArray['encryptionKey'] = $base64EncryptionKey;
       $responseArray['encryptionKeySHA256'] = $base64EncryptionKeySha256;
       $responseArray['bucketName'] = $this->indexedBucketName;
       $responseArray['objectName'] = $objectName;
 
      }
      catch(Exception $e){
       $responseArray['error'] = true;
       $responseArray['errorMessage'] = 'File did not upload!';
 
      }
       
      return $responseArray; 
    }

   /**
    * Download an encrypted file
    *
    * @param string $bucketName the name of your Google Cloud bucket.
    * @param string $objectName the name of your Google Cloud object.
    * @param string $destination the local destination to save the encrypted file.
    * @param string $base64EncryptionKey the base64 encoded encryption key.
    *
    * @return void
    */

   public function downloadEncryptedObject($bucketName, $objectName, $destination, $base64EncryptionKey)
   {
      $filePath = __DIR__.'\key\lorax-system-4c1ab33ad6ad.json';

      $storage = new StorageClient([
         'keyFile' => json_decode(file_get_contents($filePath), true),
         'projectId' => 'lorax-system'
      ]);

      $bucket = $storage->bucket($bucketName);
      $object = $bucket->object($objectName);
      $object->downloadToFile($destination, [
         'encryptionKey' => $base64EncryptionKey,
      ]);
      printf('Encrypted object gs://%s/%s downloaded to %s' . PHP_EOL,
         $bucketName, $objectName, basename($destination));
   }

   /**
    * Download an encrypted file stream
    *
    * @param string $bucketName the name of your Google Cloud bucket.
    * @param string $objectName the name of your Google Cloud object.
    * @param string $destination the local destination to save the encrypted file.
    * @param string $base64EncryptionKey the base64 encoded encryption key.
    *
    * @return void
    */

    public function downloadEncryptedObjectStream($bucketName, $objectName, $base64EncryptionKey)
    {
       $filePath = __DIR__.'\key\lorax-system-4c1ab33ad6ad.json';
 
       $storage = new StorageClient([
          'keyFile' => json_decode(file_get_contents($filePath), true),
          'projectId' => 'lorax-system'
       ]);
       
       $bucket = $storage->bucket($bucketName);
       $object = $bucket->object($objectName);
       $stream = $object->downloadAsStream([
          'encryptionKey' => $base64EncryptionKey,
       ]);
       echo $stream->getContents();
    }

   /**
    * get signed URL for the encrypted object
    *
    * @param string $bucketName the name of your Google Cloud bucket.
    * @param string $objectName the name of your Google Cloud object.
    * @param string $destination the local destination to save the encrypted file.
    * @param string $base64EncryptionKey the base64 encoded encryption key.
    *
    * @return void
    */

    public function getEncryptedObjectSignedUrl($bucketName, $objectName, $base64EncryptionKey,$base64EncryptionKeySHA256)
    {
   
      $storage = new StorageClient([
         'keyFile' => json_decode(file_get_contents($this->cloudStorageKeyFilePath), true),
         'projectId' => $this->googleCoudProject
      ]);
      
      $bucket = $storage->bucket($bucketName);
      $object = $bucket->object($objectName);
      $signedUrl = $object->signedUrl(new \DateTime('15 min'),[
         'version' => 'v4',
         'method' => 'GET',
         'headers'=>[
         'x-goog-encryption-algorithm' => "AES256",
         'x-goog-encryption-key' => $base64EncryptionKey,
         'x-goog-encryption-key-sha256' => $base64EncryptionKeySHA256
      ],
      ]);

      return ['key'=>$base64EncryptionKey,
      'keySHa256'=> $base64EncryptionKeySHA256, 
         'url'=>$signedUrl];

    }
       /**
    * get signed URL for the object
    *
    * @param string $bucketName the name of your Google Cloud bucket.
    * @param string $objectName the name of your Google Cloud object.
    * @param string $destination the local destination to save the encrypted file.
    * @param string $base64EncryptionKey the base64 encoded encryption key.
    *
    * @return void
    */

    public function getObjectSignedUrl($bucketName, $objectName)
    {
      $filePath = __DIR__.'\key\lorax-system-4c1ab33ad6ad.json';
 
      $storage = new StorageClient([
         'keyFile' => json_decode(file_get_contents($filePath), true),
         'projectId' => 'lorax-system'
      ]);
      
      $bucket = $storage->bucket($bucketName);
      $object = $bucket->object($objectName);
      $signedUrl = $object->signedUrl(new \DateTime('03 min'),[
         'version' => 'v4',
         'method' => 'GET',
      ]);

      dd([ 
         'url'=>$signedUrl]);

    }

    /**
    * Download an encrypted file stream
    *
    * @param string $bucketName the name of your Google Cloud bucket.
    * @param string $objectName the name of your Google Cloud object.
    * @param string $destination the local destination to save the encrypted file.
    * @param string $base64EncryptionKey the base64 encoded encryption key.
    *
    * @return void
    */

    public function downloadObjectStream($bucketObject, $objectName)
    {
      
       $object = $bucketObject->object($objectName);
       $stream = $object->downloadAsStream();
       return $stream->getContents();
         
    }

    /**
     * OCR for fetching entity name and address
     * @parma $imageContent String
     * @parma $CommonAddressString String 
     * @return response Array
     */
    public function OCR($imageContent,$commonAddressString, $state)
    {
      
      $responseArray = [];

      if(!empty($imageContent)){
        //dd($imageContent);
        
        //prepare request
        $vision = new VisionClient(['keyFile' => json_decode(file_get_contents($this->cloudVisionKeyFilePath), true)]); 
        
        $image = $vision->image($imageContent, 
        [
            'DOCUMENT_TEXT_DETECTION',
        ]);
        
        $result = $vision->annotate($image);
        
        //dd($result);
        $texts = $result->text();
        //dd($texts);
        foreach($texts as $key=>$text)
        {
            $description[]=$text->description();
        }
        // fetch text from image //

        //dd($description[0]);
        /**
         * Filter Entity Name and Address
         */
        $entityName='';
        $entityAddress='';
        $stopLineStringsForName = [
            'rrullinum',
            'CE-1P',
            '15 224 03 005 19-R30154723-APR BHF',
            'IMMUNMITTEL',
            'pU.Lopulli',
            'Business Executive',
            'Business Owner/Manager',
            'Business Owner Or',
            'Registered Business Owner',
            'RETURN SERVICE REQUESTED',
            'Or Owner'
        ];
        $skipLineStrinsForName =[
            'Ste. B2',
            'STEB'
        ];

        
        // converting each line in array index
        $textArray = preg_split("/\\r\\n|\\r|\\n/",$description[0]);
        $arrayLength = count($textArray); 
         for($i=0 ; $i < $arrayLength ; $i++){
            if(stripos($textArray[$i], $commonAddressString) !==false ){
                    /**
                     * filter complete entity address
                     */
                    for($j= $i; $j <  $arrayLength ; $j++){
                        $entityAddress .=' '.$textArray[$j];
                        
                        //fetching last word
                        $wordList = explode(' ',$textArray[$j]) ; 
                        $lastWord = $wordList[count($wordList)-1];
                        $lastWord = explode('-',$lastWord)[0];
                        
                        //Cheching zip code to stop
                        $lastWordLength = strlen($lastWord);
                        if($lastWordLength==5 || $lastWordLength==9){
                            break;

                        }
                    }

                    /**
                     * Filter complete entity name
                     */
                    $readLine = 0;
                    for($k = $i-1; $k > 0; $k--){
                       //skip line of ste b2
                        if(in_array($textArray[$k],$skipLineStrinsForName)){
                           continue;
                        }
                           
                        $readLine++;
                        $lastTwoChar = substr($textArray[$k], -2);
                        if(ctype_alpha(substr(trim($textArray[$k],'.'), -1)) == false
                            || $readLine>3
                            || $lastTwoChar[0]==$lastTwoChar[1]
                            || in_array($textArray[$k], $stopLineStringsForName)) {
                            //echo $k.' testing '.substr($textArray[11], -2);
                            break;
                        }

                        //collecting entity name
                        if($k == $i-1)
                            $entityName = $textArray[$k];
                        else
                            $entityName = $textArray[$k]. ' '.$entityName;

                    }
            }
         }

        // dd($textArray);
        $responseArray['error'] = false;
        $responseArray['entity_name'] = $entityName;
        $responseArray['refined_entity_name'] = $this->refindedEntityName($entityName);
        $responseArray['entity_address'] = $entityAddress;
       
      }
      else{
         $responseArray['error'] = true;
         $responseArray['errorMessage'] = 'Image conent is empty!';
      }
      return $responseArray;
    }

    /**
     * Refinded Entity name for extra space, comma and dot
     * @parma String $entityName
     * @response String
     */
    public function refindedEntityName($entityName)
    {
      return preg_replace('/\s+/', ' ', 
      str_replace([',','.'],['',''],
      trim($entityName)));
    }

    public function getStorageObject()
    {
       return new StorageClient([
         'keyFile' => json_decode(file_get_contents($this->cloudStorageKeyFilePath), true),
         'projectId' => $this->googleCoudProject
      ]);
    }

}
