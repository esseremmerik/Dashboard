<?php 
//$ofile = fopen('tmp.txt','a');
//fwrite($ofile,date("d-m-Y H:i:s:") . "G:" . serialize($_GET) . "#P:" . serialize($_POST) . "\n");


// version 2.0

    // All placeholders such as {ownership}, {categoryId}, {templateId} should
    // be replaced with real values without the {} brackets

require('solve360_cred.php');

function xml2array($xml) { 
    $arXML=array(); 
    $arXML['name']=trim($xml->getName()); 
    $arXML['value']=trim((string)$xml); 
    $t=array(); 
    foreach($xml->attributes() as $name => $value){ 
        $t[$name]=trim($value); 
    } 
    $arXML['attr']=$t; 
    $t=array(); 
    foreach($xml->children() as $name => $xmlchild) { 
        $t[$name][]=xml2array($xmlchild); //FIX : For multivalued node 
    } 
    $arXML['children']=$t; 
    return($arXML); 
} 
    
    // Get request data
    $requestData = $_POST;


    $contactData = array(
        'firstname'     => $requestData['personFirstName'],
        'lastname'      => $requestData['personLastName'],
        'jobtitle'      => $requestData['job'],
        'businessemail' => $requestData['emails']['work'],
        'cellularphone' => $requestData['phones']['mobile'],
        'businessphonemain' => $requestData['phones']['work'],
        'businessaddress' => $requestData['addresses'][0]['street'] . ' ' . $requestData['addresses'][0]['zip'] . ' ' . $requestData['addresses'][0]['city'],
        'ownership'     => '49460117',
        // OPTION Apply category tag(s) and set the owner for the contact to a group
        // You will find a list of IDs for your tags, groups and users in Workspace > My Account > API Reference
        // To enable this option, uncomment the following:

        /*        
        // Specify a different ownership i.e. share the item
        'ownership'     => {ownership},

,
        
        
        // Add categories
        'categories'    => array(
            'add' => array('category' => array({categoryId},{categoryId}))
        ),
        */        
        
    );
    

   // var_dump($solve360Service->getOwnership());
   // exit();
    $searchcontact = $solve360Service->searchContacts(array("searchmode"=>"Cany","searchvalue"=>$requestData['emails']['work']));
    $sAction = "adding";
     //  var_dump($contact); 
  //  echo "abcs";
    $aXML = xml2array($searchcontact);
    if (isset($aXML['children']['count'][0]['value'])) {
	    $iResult = $aXML['children']['count'][0]['value']; 
	    //fwrite($ofile,date("d-m-Y H:i:s:") . "G:$iResult\n");

	    if ($iResult == "0") {		    
		    //
		    // Adding the contact
		    //
    		$contact = $solve360Service->addContact($contactData);
    		//fwrite($ofile,date("d-m-Y H:i:s:") . "G:" . serialize($_GET) . "#P:" . serialize($_POST) . "\n");

    		
	    } else {
	    	// update contact
	    	// editContact
	    	//	print_r($aXML);
	    			$aID = array();
    		foreach ($aXML['children'] as $sKey =>$sValue) {
    			if (substr($sKey, 0,2) == "id") {
	    			$aID[] = $sKey;
	    		}
	    			
    		}
    		//print_r($aXML);
    		if (count($aID) > 0) {
	    		if(isset($aID[0])) {
	    				if (isset($aXML['children'][$aID[0]][0]['children']['id'][0]['value'])) {
			    			$contact = $solve360Service->editContact($aXML['children'][$aID[0]][0]['children']['id'][0]['value'], $contactData);
			    			       $sAction = "editing";
			    			           		//fwrite($ofile,date("d-m-Y H:i:s:") . "G:" . $sAction . serialize($contactData) . "\n");
		    			}
	    		}
    		}
    		
	    	
	    }
	    
    } else {
	     	//fwrite($ofile,date("d-m-Y H:i:s:") . "error\n");
	     //exit();
    }

    

    
    if (isset($contact->errors)) {
        // Mail yourself if errors occur  
        $ofile = fopen('log_cardbridge.txt','a'); 
        fwrite($ofile,date("d-m-Y H:i:s:") . 'Error: ' . $contact->errors->asXml() . "\n"); 
       fclose($ofile);
       /* mail(
            USER, 
            'Error while ' . $sAction . ' contact to Solve360', 
            'Error: ' . $contact->errors->asXml()
        );
        die ('System error');*/
    } else {
        // Get new contact params from the response
        $contactName = (string) $contact->item->name;
        $contactId   = (integer) $contact->item->id;
                // Mail yourself the result
        /*
         mail(
            USER, 
            'Contact ' . $sAction . ' succesful (Solve360)', 
            'Name ' . $contactData['firstname']
        );*/
    }
    
    //
    // OPTION Adding a activity 
    //
    
    /*
     * You can attach an activity to the contact you just created
     * This example creates a Note, to enable this feature just uncomment the following request
     */    
    
    /*    
    // Preparing data for the note
    $noteData = array(
        'details' => nl2br($requestData['note'])
    );

    $note = $solve360Service->addActivity($contactId, 'note', $noteData);
    
    // Mail yourself the result
    mail(
        USER, 
        'Note was added to "' . $contactName . '" contact in Solve360',
        'Note with id ' . $note->id . ' was added to the contact with id ' . $contactId
    );
    // End of adding note activity
    */
 
    //
    // OPTION Inserting a template of activities
    //
    
    /*
     * You can also insert a template directly into the contact you just created
     * You will find a list of IDs for your templates in Workspace > My Account > API Reference
     * To enable this feature just uncomment the following request
     */

    /*
    // Start of template request
    $templateId = {templateId};
    $template = $solve360Service->addActivity($contactId, 'template', array('templateid' => $templateId));
        
    // Mail yourself the result
    mail(
        USER, 
        'Template was added to "' . $contactName . '" contact in Solve360',
        'Template with id ' . $templateId . ' was added to the contact with id ' . $contactId
    );
    // End of template request
    */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<h2>Result</h2>
<p>Thank you, <b><?php echo $contactName ?></b></p>
<p>Your data was successfully saved.</p>
</body>
</html> 