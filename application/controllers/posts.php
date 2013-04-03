<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Posts extends CI_Controller {

    
       
       function __construct()
	{
		parent::__construct();
		//$this->load->helper(array('form','url','tesseract','saaspose','opencalais'));
		$this->load->helper(array('form','url'));
		$this->load->database();
		//$this->load->library('jquery');
		$this->load->model('post');
	}

	function index()
	{
		$this->load->view('post', array('error' => ''));
	}

	function do_upload()
	{
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf';
		$config['max_size']	= '100000000';
		//$config['max_width']  = '1024';
		//$config['max_height']  = '768';

		$this->load->library('upload', $config);
		$field_name = "docurl";


		if (!$this->upload->do_upload($field_name))
		{
			$error = array('error' => $this->upload->display_errors());
			$data = array('page_title'     => 'Test the document!');
			$this->load->view('header',$data);
			$this->load->view('post', $error);
			$this->load->view('footer');
		}
		else
		{
			$upload_data = $this->upload->data();
			$data = $this->do_post_request($upload_data,"");

			
			$content = array('content' => $data['content'], 'error' => $data['error'], 'filename' => $upload_data['file_name']);
			$data_head = array('page_title' => 'Extract text from document!');

			$this->load->view('header',$data_head);
			$this->load->view('test',$content);
			$this->load->view('footer');
			//	$this->load->view('upload_success', $data);
		}
	}
	function submit_pdf(){

//extract data from the post
		extract($_POST);
		$url = 'https://www.documentcloud.org/api/upload.json';
		//$url = 'http://www.pichanoma.com/docupload.php';
		$fields = 		array (
						'username' => '', 
						'password' => '', 
						'file'=>'http://labs.appligent.com/presentations/recognizing_malformed_pdf_f.pdf', 
						'access'=>'private', 
						'source'=>'Some Very Confidential Source', 
						'title'=>'Test Upload via Curl');
		 

		//url-ify the data for the POST
		$fields_string = '';
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; } rtrim($fields_string, '&');

		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_HEADER, 1); 
		curl_setopt($ch, CURLOPT_USERPWD, 'nick@openinstitute.com' . ":" . 'Einstein/149378');
		//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Basic'));        
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		//execute post
		$result = curl_exec($ch);

		//close connection
		curl_close($ch);

		//print_r($result);
		echo $result;
	}
				///---------------------
	function do_post_request($data, $optional_headers = null) 
	{ 
				$fileHandle = fopen("./uploads/".$data['file_name'], "rb");
				$fileContents = stream_get_contents($fileHandle);
				 //  echo  $fileContents;
				fclose($fileHandle);
				
				
				
				$params = array(
				//'project' => '7877',
               		//'access' => 	'private',
               		'title' => $data['file_name'],
               		'username' => 'benjamin@openinstitute.com',
               		'password' => 'private',
      //         		'authenticity_token' => 'y9NZyEWHEK5dcTFgBu3RqBJDCJvRQdMDO62cLbmR0zs=',
				'file' => $fileContents
				); 
  
				
				$data = http_build_query($params);
			//	echo $data;
				//$cred = sprintf('Authorization: Basic %s', base64_encode('benjamin@openinstitute.com:private'));
				
				$cont = array(
				'http' => array (
					'method' => 'POST',
					'header'=>"enctype: multipart/form-data\r\n Content-Transfer-Encoding: binary",
					'content' => $data
				      )
				 );
				   $url = "https://www.documentcloud.org/api/upload.json";
				   $ctx = stream_context_create($cont);
				  // echo $ctx;
				   $fp = fopen($url, 'rb', false, $ctx);
				   
				
				$response = stream_get_contents($fp);			
				return $response;		
			
				/*
				if ($optional_headers!== null) { 
				$params['http']['header'] = $optional_headers; 
				} 
				$ctx = stream_context_create($params); 
				$fp = @fopen($url, 'rb', false, $ctx); 
				if (!$fp) { 
				throw new Exception("Problem with $url, $php_errormsg"); 
				} 
				$response = @stream_get_contents($fp); 
				if ($response === false) { 
				throw new Exception("Problem reading data from $url, $php_errormsg"); 
				} 
				return $response; 
				*/
	}
			///---------------------
	function do_process() {

	 // $options = array('project'=>'OpenDuka','access'=>'private','docurl'=> $filedata);
 
	//	$this->jquery->post('https://www.documentcloud.org/api/upload.json', $options, function(data) {
	//	    alert(data); // should print "Number is 12"
	//	});
		
		/*('#calais').submit(function (event) {
		   $dataString = $filedata.serialize();
		    $this->jquery->ajax({
		        type:"POST",
		        url:"https://www.documentcloud.org/api/upload.json",
		        data:$dataString,
		
		        success:function (data) {
		            alert('test');
		        }
		
		    });*/
		//    event.preventDefault();
		//});
	}
	 

	
	function do_process1($filedata){
		$src=array('project'=>'' ,'access'=>'private');
	$fileName=$filedata['file_name'];
	$BaseProductUri = "https://www.documentcloud.org/api/upload.json";
	$responseStream = uploadFileBinary($BaseProductUri, $fileName, "");
	echo $responseStream ; 	


	}
	/*function do_process($filedata)
	{
		//
		//================================================
		//Extraction part
		//================================================
		//
	//-----------------------------IMG LOAD-----------------------------------------------------------  


		$fileName=$filedata['file_name'];
		//echo $filedata['file_type']; exit;

		if ((($filedata['file_type'] == "image/jpeg")
		|| ($filedata['file_type'] == "image/gif")
		|| ($filedata['file_type'] == "image/png"))
		&& ($filedata['file_size'] < 10000000))
		{
		//include 'tesseract.php';
		$OutPutLocation = realpath(dirname($_SERVER['DOCUMENT_ROOT'])) . '/projects/tesseract/uploads/';
		$image_path=$OutPutLocation . $fileName;	
	//	echo $image_path; exit;
			$api= new TessBaseAPI;
			$api->Init(".","eng",$mode_or_oem=OEM_DEFAULT);
			$api->SetPageSegMode(PSM_AUTO);
			
			//$mImgFile = "eurotext.jpg";
			$handle=fopen($image_path,"rb");
			//echo filesize($image_path) .'>'.$filedata['file_size'];exit;
			$mBuffer=fread($handle,filesize($image_path));
			//print strlen($mBuffer);exit;
			$content=ProcessPagesBuffer($mBuffer,strlen($mBuffer)*4,$api);
			//print "result(ProcessPagesBuffer)=";
			//print $content; exit;
			//echo strlen($content);exit;
			if(strlen($content)<=25){$context=array('content'=>'','error'=>'Sorry! Text could not be extracted from image');}
			else{$context=array('content'=>$content,'error'=>'');}
			
			return $context;
			//$result = ProcessPagesFileStream($mImgFile,$api);
			//print "result(ProcessPagesFileStream)=";
			//print $result;
	  	}
	
	
	//-----------------------------PDF LOAD-----------------------------------------------------------  
	  	if (($filedata['file_type'] == "application/pdf")
		&& ($filedata['file_size'] < 10000000))
		{
		//include './libraries/saaspose.php';	
			
		$AppSID  = "9a7ad154-c42d-4896-9c86-d06274518e85";
		$AppKey = "9931ee02a636e3b8c2382b47ddf82c0e";
		$BaseProductUri = "http://api.saaspose.com/v1.0";
		
		//web server location to save file
		//echo realpath(dirname($_SERVER['DOCUMENT_ROOT'])) . '/projects/tesseract/'; exit;
		$OutPutLocation = realpath(dirname($_SERVER['DOCUMENT_ROOT'])) . '/projects/tesseract/uploads/';
		//$OutPutLocation = '/projects/tesseract/';						
		$OutPutType ='html';
		 
		$filePath = $OutPutLocation . $fileName;
	//echo $filePath;exit;
		//Upload file to Sasspose server
		//UploadFile(realpath(dirname($_SERVER['DOCUMENT_ROOT'])) . '/projects/tesseract/'. $fileName, "", $BaseProductUri . "/storage/file/");

		$strURIRequest = $BaseProductUri . "/storage/file/" . $fileName;
   		$signedURI = Sign($strURIRequest, $AppSID, $AppKey);
    		uploadFileBinary($signedURI, $filePath);
	echo "Pdf file has been uploaded successully <br/>";  
		//echo basename(realpath(dirname($_SERVER['DOCUMENT_ROOT'])) . '/projects/tesseract/'. $fileName);
		//sleep(2);		
		//build URI
		$strURI = $BaseProductUri . "/pdf/" . $fileName . "?format=".$OutPutType;
		//sign URI
		$signedURI = Sign($strURI, $AppSID, $AppKey);
	//echo $signedURI;
		$responseStream = processCommand($signedURI, "GET", "", "");
	//echo $responseStream ; 
		//sleep(2);	
		$v_output = ValidateOutput($responseStream);
		
			
			if ($v_output === "") 
			{
			saveFile($responseStream, $OutPutLocation . getFileName($fileName).".zip");
			//header('Location:download.php?file='. $OutPutLocation . getFileName($fileName).'.html');//.$_REQUEST["OutPutType"]);
			//sleep(2);
			
			//delete file from saaspose
			
			//$responseDelStream = processCommand($signedURI, "DELETE", "", "");
            		//if ($responseDelStream->Code != 200) {
                	//	return FALSE;
	            	//	}else{
	            	//	return TRUE;
	            	//}
		
			$zip_path=$OutPutLocation . getFileName($fileName).".zip";
		//echo $zip_path;
		
			//------Extract the html file in the  downloaded zip file	
			$zip = new ZipArchive();
			$res = $zip->open($zip_path);
			//echo $res;
				if ($res === TRUE) {
				    $zip->extractTo($OutPutLocation);
				    $zip->close();
				    echo '<br/>zip extraction done';
				    
				    //------Get the html text to OpenCalais-----				
						//Get the html text
			
						//$html_source = simplexml_load_file(getFileName($fileName).'.'.$OutPutType); 
						$html_doc=	$OutPutLocation. getFileName($fileName).'.'.$OutPutType;
						//$file = fopen('test.html', 'r');
						$doc = new DOMDocument(); 
						$doc->loadHTMLFile($html_doc); 
						
					
						$content = $doc->saveHTML(); 
						if(strlen($content)<=25){$context=array('content'=>'','error'=>'Sorry! Text could not be extracted from zip');}
						else{$context=array('content'=>$content,'error'=>'');}
						
						return $context;
						//return $doc->saveHTML();
				    
				    
				} else {
				    echo '<br/>zip extraction failed';
				    $context=array('content'=>'','error'=>'Sorry! Text could not be extracted from document');
				    return $context;
				}
				
			} 
		//sleep(2);
	 	}
	}*/
	
	function entity()
	{
		
		$context = $_POST['content'];	
		$File_Name = $_POST['filename'];		
		//echo $context;exit;
		$doc_data = array('DocName' => $File_Name,'DocText' => $context );
		$DocID = $this->post->insert_document($doc_data);
		
		
		$apikey = "sp3u4wvyqbpx34zauxqp7qr2";
		$oc = new OpenCalais($apikey);
		
		$entities = $oc->getEntities($context);
		$entity_info = "";
		$entity_type_db = "";
		$entity_val_db = "";
		//$tree .= $File_Name;

		foreach ($entities as $type => $values) {
			
			$entity_type_db .= $type .",";

			foreach ($values as $entity) {
				$this->post->insert_entity($type,$entity,$DocID);
			}
			
		}

		$entity_data = array('DocType' => $entity_type_db, 'DocID' => $DocID);
		$this->post->update_document($entity_data);
		
		$entity_type_db = "";
		
		redirect('<?php echo base_url() . index_page();?>/trees/index/'.$DocID);
		/*
		$content=array('entities' => $entity_info,'filename' => $File_Name,'tree' => $tree,'error' => '');
		$data_head = array('page_title'     => 'Entity Extraction');


		$this->load->view('header_entity',$data_head);
		$this->load->view('entity',$content);
		$this->load->view('footer');
		*/
	}
	
	
			
}

/* End of file posts.php */
/* Location: ./application/controllers/posts.php */