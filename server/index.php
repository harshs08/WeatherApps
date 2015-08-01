<?php 
	//header('Content-Type: application/xml');

	$location = $location_reg = $type = $unit = $url = $xml= $woeids= "";
	$attr4 = $day = $low = $high= $text1=Array();

	$location = $_GET['location'];
	$type = $_GET['type'];
	$unit = $_GET['unit'];



	 $location_reg = preg_replace('#[^\w()/.%\-&]#', " ", $location);
  	$use_errors = libxml_use_internal_errors(true);

	

	 if($type=="zip"){
          	$url="http://where.yahooapis.com/v1/concordance/usps/".$location_reg."?appid=APPID";
 	 }
 	 elseif($type=="city"){
         	 $url= "http://where.yahooapis.com/v1/places\$and(.q('".$location_reg."'),.type(7));start=0;count=1?appid=APPID";
  	}
	

	 $xml = @simplexml_load_file(urlencode($url));



	if(!$xml){

	/************************************************create xml********************************************/
	try 
		{ 

    		/*** a new dom object ***/ 
   		 $dom = new domDocument; 
		 
		 $dom->preserveWhiteSpace = false;

  		  /*** make the output tidy ***/ 
   		 $dom->formatOutput = true; 

   		 /*** create the root element ***/ 
   		 $root = $dom->appendChild($dom->createElement( "weather" )); 

   		 /*** create the simple xml element ***/ 
  		  $sxe = simplexml_import_dom( $dom ); 

   		 /*** add a feed element ***/ 
   		 $sxe->addChild("error", "No results found"); 

		} 
		catch( Exception $e ) 
		{ 
 	 	  echo $e->getMessage(); 
		} 

		/************************************************create xml********************************************/
	
	}
  
  
	/********************************************zip***************************************************************************/
        	else if($type=="zip"){


                  	$woeids = $xml->woeid;

                 	
                 	if($unit=="c"){
                          	$url_weather= "http://weather.yahooapis.com/forecastrss?w=".$woeids."&u=c";
                 	}
                  	elseif($unit=="f"){
                         	$url_weather= "http://weather.yahooapis.com/forecastrss?w=".$woeids."&u=f";
                 	}
		
		$xml_weather = @simplexml_load_file($url_weather);

		 foreach($xml_weather->channel as $entry){


			/*************************************this check is for woeids that are not valid*************************/
			$title=$entry->title;
			$patt='/Error/';
			preg_match($patt,$title,$res);
			/*************************************this check is for woeids that are not valid*************************/
			

			if(count($res)==0){ //*******this check is for woeids that are not valid****

			$link = $entry->link;
			

			 $ns = $entry->getNameSpaces(true);
                       		 $yweather = $entry->children($ns['yweather']);

                          		$attr1 = $yweather->location->attributes();

                        	                $city = $attr1['city'];
                        		 if($city==""){
                          		$city="N/A";
                         		 }

                          		$region = $attr1['region'];
                         		 if($region==""){
                          		$region="N/A";
                          		}

                         		 $country = $attr1['country'];
                         		 if($country==""){
                          		$country="N/A";
                          		}

			$attr2 = $yweather->units->attributes();

                          		$temperature = $attr2['temperature'];
                         		 if($temperature==""){
                         		 $temperature="N/A";
                        		 }

			$yweather2 = $entry->item->children($ns['yweather']);
                         		$attr3 = $yweather2->condition->attributes();

                      		$text = $attr3['text'];
                         		if($text==""){
                          		$text="N/A";
                         		}

                         		 $temp = $attr3['temp'];
                         		if($temp==""){
                          		$temp="N/A";
                          		}

			$desc = $entry->item->description;
                         		$imgpattern = '/src="(.*?)"/i';
                          		preg_match($imgpattern, $desc, $matches);
                          		$imgurl = $matches[0];
                          		if($imgurl==""){
                          		$imgurl="N/A";
                          		}

			
			$count = 0;
			foreach($yweather2->forecast as $forecastentry)
			{		
			$attr4[$count] = $forecastentry->attributes();
			$day[$count] = $attr4[$count]['day'];
			$low[$count] = $attr4[$count]['low'];
			$high[$count] = $attr4[$count]['high'];
			$text1[$count] = $attr4[$count]['text'];
			//echo "result here".$day[$count];
			$count++;
			}


		}

		/************************************************create xml********************************************/
		try 
		{ 

    		/*** a new dom object ***/ 
   		 $dom = new domDocument; 

  		  /*** make the output tidy ***/ 
   		 $dom->formatOutput = true; 

   		 /*** create the root element ***/ 
   		 $root = $dom->appendChild($dom->createElement( "weather" )); 

   		 /*** create the simple xml element ***/ 
  		  $sxe = simplexml_import_dom( $dom ); 

   		 /*** add a feed element ***/ 
   		 $sxe->addChild("feed", $url_weather); 

		/*** add a link element ***/ 
   		 $sxe->addChild("link", $link); 

		/*** add a location element ***/ 
   		 $loc = $sxe->addChild("location");

		/*** add attributes ***/
		$loc->addAttribute("city",$city);
		$loc->addAttribute("region",$region);
		$loc->addAttribute("country",$country);
		
		/*** add a unit element***/
		$uni = $sxe->addChild("units");

		/*** add attributes ***/
		$uni->addAttribute("temperature",$temperature);
		
		///***add condition element***/
		$con=$sxe->addChild("condition");
		
		///*** add attributes ***/
		$con->addAttribute("text",$text);
		$con->addAttribute("temp",$temp);

		/***add img element***/
		preg_match('/src="([^"]+)/i',$imgurl, $matches);
		$sxe->addChild("img",$matches[1]);
		
		
		$count1 = 0;
		for($count1=0; $count1<5 ; $count1++)
		{
			$forc=$sxe->addChild("forecast");
			$forc->addAttribute("day",$day[$count1]);
			$forc->addAttribute("low",$low[$count1]);
			$forc->addAttribute("high",$high[$count1]);
			$forc->addAttribute("text",$text1[$count1]);
		}

		


    		/*** echo the xml ***/ 
    		echo $sxe->asXML(); 
		} 
	catch( Exception $e ) 
	{ 
 	   echo $e->getMessage(); 
	} 

	/************************************************create xml********************************************/

                 }//end if	 
                 	
   	}
	/********************************************zip***************************************************************************/


	/********************************************city***************************************************************************/




	else if($type=="city"){
			
                  	$woeids = $xml->place->woeid;

                 	
                 	if($unit=="c"){
                          	$url_weather= "http://weather.yahooapis.com/forecastrss?w=".$woeids."&u=c";
                 	}
                  	elseif($unit=="f"){
                         	$url_weather= "http://weather.yahooapis.com/forecastrss?w=".$woeids."&u=f";
                 	}
		
		$xml_weather = @simplexml_load_file($url_weather);

		 foreach($xml_weather->channel as $entry){


			/*************************************this check is for woeids that are not valid*************************/
			$title=$entry->title;
			$patt='/Error/';
			preg_match($patt,$title,$res);
			/*************************************this check is for woeids that are not valid*************************/
			

			if(count($res)==0){ //*******this check is for woeids that are not valid****





			
			$link = $entry->link;
			

			 $ns = $entry->getNameSpaces(true);
                       		 $yweather = $entry->children($ns['yweather']);

                          		$attr1 = $yweather->location->attributes();

                        	                $city = $attr1['city'];
                        		 if($city==""){
                          		$city="N/A";
                         		 }

                          		$region = $attr1['region'];
                         		 if($region==""){
                          		$region="N/A";
                          		}

                         		 $country = $attr1['country'];
                         		 if($country==""){
                          		$country="N/A";
                          		}

			$attr2 = $yweather->units->attributes();

                          		$temperature = $attr2['temperature'];
                         		 if($temperature==""){
                         		 $temperature="N/A";
                        		 }

			$yweather2 = $entry->item->children($ns['yweather']);
                         		$attr3 = $yweather2->condition->attributes();

                      		$text = $attr3['text'];
                         		if($text==""){
                          		$text="N/A";
                         		}

                         		 $temp = $attr3['temp'];
                         		if($temp==""){
                          		$temp="N/A";
                          		}

			$desc = $entry->item->description;
                         		$imgpattern = '/src="(.*?)"/i';
                          		preg_match($imgpattern, $desc, $matches);
                          		$imgurl = $matches[0];
                          		if($imgurl==""){
                          		$imgurl="N/A";
                          		}

			
			$count = 0;
			foreach($yweather2->forecast as $forecastentry)
			{		
			$attr4[$count] = $forecastentry->attributes();
			$day[$count] = $attr4[$count]['day'];
			$low[$count] = $attr4[$count]['low'];
			$high[$count] = $attr4[$count]['high'];
			$text1[$count] = $attr4[$count]['text'];
			//echo "result here".$day[$count];
			$count++;
			}


		}
		/************************************************create xml********************************************/
		try 
		{ 

    		/*** a new dom object ***/ 
   		 $dom = new domDocument; 

  		  /*** make the output tidy ***/ 
   		 $dom->formatOutput = true; 

   		 /*** create the root element ***/ 
   		 $root = $dom->appendChild($dom->createElement( "weather" )); 

   		 /*** create the simple xml element ***/ 
  		  $sxe = simplexml_import_dom( $dom ); 

   		 /*** add a feed element ***/ 
   		 $sxe->addChild("feed", $url_weather); 

		/*** add a link element ***/ 
   		 $sxe->addChild("link", $link); 

		/*** add a location element ***/ 
   		 $loc = $sxe->addChild("location");

		/*** add attributes ***/
		$loc->addAttribute("city",$city);
		$loc->addAttribute("region",$region);
		$loc->addAttribute("country",$country);
		
		/*** add a unit element***/
		$uni = $sxe->addChild("units");

		/*** add attributes ***/
		$uni->addAttribute("temperature",$temperature);
		
		///***add condition element***/
		$con=$sxe->addChild("condition");
		
		///*** add attributes ***/
		$con->addAttribute("text",$text);
		$con->addAttribute("temp",$temp);

		/***add img element***/
		preg_match('/src="([^"]+)/i',$imgurl, $matches);
		$sxe->addChild("img",$matches[1]);
		
		
		$count1 = 0;
		for($count1=0; $count1<5 ; $count1++)
		{
			$forc=$sxe->addChild("forecast");
			$forc->addAttribute("day",$day[$count1]);
			$forc->addAttribute("low",$low[$count1]);
			$forc->addAttribute("high",$high[$count1]);
			$forc->addAttribute("text",$text1[$count1]);
		}

		


    		/*** echo the xml ***/ 
    		echo $sxe->asXML(); 
		} 
	catch( Exception $e ) 
	{ 
 	   echo $e->getMessage(); 
	} 

	/************************************************create xml********************************************/
	}//end if res
                  	
   	}




	/********************************************city***************************************************************************/
           	
 ?>
