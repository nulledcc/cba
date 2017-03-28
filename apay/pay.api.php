<?php
class xe{
	public static $iso = "USD";
	public static $wsdlUrl = "http://api.cba.am/exchangerates.asmx?WSDL";
	public static function localXE($method = 0,$data = null)
	{
		$fileName = "apay/xe.json";
		
		if(is_writable($fileName))
		{
			if($method == 0){
				$xejson = fopen($fileName, "r") or die("Unable to open {$fileName} file!");
				$data = json_decode(fread($xejson,filesize($fileName)),true);
				return $data;					
				fclose($xejson);
			}else if($method == 1){
				$xejson = fopen($fileName, "w") or die("Unable to open {$fileName} file!");
				$data = json_encode($data);
				fwrite($xejson, $data);
				return json_decode($data,true);
				fclose($xejson);
			}else if($method == 2){
				$xejson = fopen($fileName, "r") or die("Unable to open {$fileName} file!");
				$xeArray = json_decode(fread($xejson,filesize($fileName)),true);
				$currentxe = array("ERROR"=>"NO EXCHANGE FOR THAT ISO");
				for($i = 0; $i < count($xeArray);$i++)
				{
					if(in_array(self::$iso,$xeArray[$i]))
					{
					
						$currentxe = $xeArray[$i];
						$i = count($xeArray)+1;
					}
				}	
				return $currentxe;
				fclose($xejson);
			}
		}else{
			die("Can't read {$fileName} file!");
		}
	}
	public static function cbaXE($ISO = "USD"){
		self::$iso = $ISO;
		try{
			$options = array('soap_version'=> SOAP_1_1,'exceptions'=> true,'trace'=> 1,'wdsl_local_copy' => true);
			$client = null;
			if(stristr(file_get_contents(self::$wsdlUrl),"<wsdl:definitions"))
			{
				$client = new SoapClient(self::$wsdlUrl);
				if (is_soap_fault($client)){
					die(json_encode(self::localXE(2)));
				}
			}else{
				die(json_encode(self::localXE(2)));
			}
			$result = $client->ExchangeRatesLatestByISO();
			$xeData = $result->ExchangeRatesLatestByISOResult;
			$xeDate  = $xeData->CurrentDate;
			$xeRates = $xeData->Rates->ExchangeRate;
			$xeArray = self::localXE(1,$xeRates);
			$currentxe = array("ERROR"=>"NO EXCHANGE FOR THAT ISO");
			for($i = 0; $i < count($xeArray);$i++)
			{
				if(in_array($ISO,$xeArray[$i]))
				{
					$currentxe = $xeArray[$i];
					$i = count($xeArray)+1;
				}
			}
			return $currentxe;
			
		}catch(Exception $e){
			die(json_encode(self::localXE(2)));
		}
		die(json_encode(self::localXE(2)));
	}
}
	