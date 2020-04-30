<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Vision\VisionClient;



class AnnotationController extends Controller
{
    //show the upload form
    public function displayForm(){
        return view('annotate');
    }

    public function annotateImage(Request $request){
      if($request->file('image')){
        //convert image to base64
      $imageContent = file_get_contents($request->file('image'));
        // for linux we need to change /key/loraxlorax-ocr-6902800a657a.json
        //dd($imageContent);
        $filePath = __DIR__.'\key\agent-admin-255615-620c97f47453.json'; 
        //putenv('GOOGLE_APPLICATION_CREDENTIALS='.$filePath);

        //prepare request
        $vision = new VisionClient(['keyFile' => json_decode(file_get_contents($filePath), true), 'mime_types'=>'pplication/pdf']); 
        
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
        $skipStringsForName = [
            'rrullinum',
            'CE-1P',
            '15 224 03 005 19-R30154723-APR BHF',
            'IMMUNMITTEL',
            'pU.Lopulli',
            'Business Executive',
            'Business Owner/Manager',
            'Registered Business Owner',
            'RETURN SERVICE REQUESTED',
        ];

        // converting each line in array index
        $textArray = preg_split("/\\r\\n|\\r|\\n/",$description[0]);
        $arrayLength = count($textArray); 
         for($i=0 ; $i < $arrayLength ; $i++){
            if(stripos($textArray[$i], '2035 SUNSET LAKE') !==false ){
                    /**
                     * filter complete entity address
                     */
                    for($j= $i; $j <  $arrayLength ; $j++){
                        $entityAddress .=' '.$textArray[$j];
                        
                        //fetching last work
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
                        
                        $readLine++;
                        $lastTwoChar = substr($textArray[$k], -2);
                        if(ctype_alpha(substr(trim($textArray[$k],'.'), -1)) == false
                            || $readLine>3
                            || $lastTwoChar[0]==$lastTwoChar[1]
                            || in_array($textArray[$k],$skipStringsForName)) {
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
         
         echo '<strong>Entity Name: </strong>'.$entityName .'<br>';
         echo '<strong>Entity Address:</strong> '.$entityAddress;
         dd($textArray);
      }
    }
}
