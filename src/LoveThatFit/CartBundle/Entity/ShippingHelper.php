<?php

namespace LoveThatFit\CartBundle\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Yaml\Parser;


class ShippingHelper {

  private $container;
  /**
   * @var strAccessLicenseNumber string
   * Get this license number from your UPS account
   **/
  var $strAccessLicenseNumber = '1CFB4184AA5371E5';

  /**
   * @var strUserId string
   * The username you use to login to ups.com
   **/
  var $strUserId = 'prohlf.ltf';

  /**
   * @var strPassword string
   * The password you use to login to ups.com
   **/
  var $strPassword = 'GoPens21!';

  /**
   * @var strShipperNumber string
   * Your UPS account number (may have to remove dashes)
   **/
  var $strShipperNumber = '0R7E49';

  /**
   * @var strShipperZip string
   * This is the "ship from" zip
   **/
  var $strShipperZip = '22041';

  /**
   * @var strDefaultServiceCode string
   * The default method you'd like to use
   **/
  var $strDefaultServiceCode = '03'; // GND / General Ground method

  /**
   * @var strRateWebServiceLocation string
   * The location of the web service
   **/
  //var $strRateWebServiceLocation = 'https://www.ups.com/ups.app/xml/Rate'; // Production URL
  var $strRateWebServiceLocation = 'https://wwwcie.ups.com/ups.app/xml/Rate'; // Test URL

  /**
   * @var boolDebugMode boolean
   * Set this to true to print out debugging information
   **/
  var $boolDebugMode = false;


  public function __construct(Container $container) {
	$this->container = $container;
  }

  /**
   * Gets passed a character string that represents
   * the method. The service code that needs to be
   * passed to the web service is then returned.
   * Defaults to Ground shipping.
   *
   * @param strService string
   *
   * @return string The shipping code the web service wants
   **/
  private function GetServiceCode($strService='GND') {

	switch(strtoupper($strService)) {

	  case '1DM':
		$strServiceCode = '14';
		break;

	  case '1DA':
		$strServiceCode = '01';
		break;

	  case '1DAPI':
		$strServiceCode = '01';
		break;

	  case '1DP':
		$strServiceCode = '13';
		break;

	  case '2DM':
		$strServiceCode = '59';
		break;

	  case '2DA':
		$strServiceCode = '02';
		break;

	  case '3DS':
		$strServiceCode = '12';
		break;

	  case 'GND':
		$strServiceCode = '03';
		break;

	  case 'GNDRES':
		$strServiceCode = '03';
		break;

	  case 'GNDCOM':
		$strServiceCode = '03';
		break;

	  case 'STD':
		$strServiceCode = '11';
		break;

	  case 'XPR':
		$strServiceCode = '07';
		break;

	  case 'XDM':
		$strServiceCode = '54';
		break;

	  case 'XPD':
		$strServiceCode = '08';
		break;

	  default:
		$strServiceCode = '03';
		break;

	}

	return $strServiceCode;

  } # end method GetServiceCode()


  public function GetShippingRate($strDestinationZip, $strServiceShortName='GND', $strPackageLength=18, $strPackageWidth=12, $strPackageHeight=4, $strPackageWeight=2, $boolReturnPriceOnly=true) {

	$strServiceCode = $this->GetServiceCode($strServiceShortName);

	/*
	  Default value is 01. Valid values are:
		01 - Daily Pickup
		03 - Customer Counter
		06 - One Time Pickup
		07 - On Call Air
		11 - Suggested Retail Rates
		19 - Letter Center
		20 - Air Service Center
	*/
	$strXml ="<?xml version=\"1.0\"?>
		<AccessRequest xml:lang=\"en-US\">
			<AccessLicenseNumber>{$this->strAccessLicenseNumber}</AccessLicenseNumber>
			<UserId>{$this->strUserId}</UserId>
			<Password>{$this->strPassword}</Password>
		</AccessRequest>
		<?xml version=\"1.0\"?>
		<RatingServiceSelectionRequest xml:lang=\"en-US\">
			<Request>
				<TransactionReference>
					<CustomerContext>Shipping Calculation Request</CustomerContext>
					<XpciVersion>1.0001</XpciVersion>
				</TransactionReference>
				<RequestAction>Rate</RequestAction>
				<RequestOption>Rate</RequestOption>
			</Request>
			<PickupType>
				<Code>01</Code>
			</PickupType>
			<Shipment>
				<Shipper>
					<Address>
						<PostalCode>{$this->strShipperZip}</PostalCode>
						<CountryCode>US</CountryCode>
					</Address>
					<ShipperNumber>{$this->strShipperNumber}</ShipperNumber>
				</Shipper>
				<ShipTo>
					<Address>
						<PostalCode>{$strDestinationZip}</PostalCode>
						<CountryCode>US</CountryCode>
						<ResidentialAddressIndicator/>
					</Address>
				</ShipTo>
				<ShipFrom>
					<Address>
						<PostalCode>{$this->strShipperZip}</PostalCode>
						<CountryCode>US</CountryCode>
					</Address>
				</ShipFrom>
				<Service>
					<Code>{$strServiceCode}</Code>
				</Service>
				<Package>
					<PackagingType>
						<Code>02</Code>
					</PackagingType>
					<Dimensions>
						<UnitOfMeasurement>
							<Code>IN</Code>
						</UnitOfMeasurement>
						<Length>{$strPackageLength}</Length>
						<Width>{$strPackageWidth}</Width>
						<Height>{$strPackageHeight}</Height>
					</Dimensions>
					<PackageWeight>
						<UnitOfMeasurement>
							<Code>LBS</Code>
						</UnitOfMeasurement>
						<Weight>{$strPackageWeight}</Weight>
					</PackageWeight>
				</Package>
			</Shipment>
		</RatingServiceSelectionRequest>";

	$rsrcCurl = curl_init($this->strRateWebServiceLocation);

	curl_setopt($rsrcCurl, CURLOPT_HEADER, 0);
	curl_setopt($rsrcCurl, CURLOPT_POST, 1);
	curl_setopt($rsrcCurl, CURLOPT_TIMEOUT, 60);
	curl_setopt($rsrcCurl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($rsrcCurl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($rsrcCurl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($rsrcCurl, CURLOPT_POSTFIELDS, $strXml);

	$strResult = curl_exec($rsrcCurl);
	if($this->boolDebugMode) echo "<!--{$strResult}-->";

	$objResult = new \SimpleXMLElement($strResult);
	if($this->boolDebugMode) print_r($objResult);

	curl_close($rsrcCurl);

	// Return either the decimal string value that is the rate
	if($boolReturnPriceOnly) {

	  return (string) $objResult->RatedShipment->TotalCharges->MonetaryValue;

	  // Or return the full object and do with it what you want
	} else {

	  return $objResult;

	}

  } # end method GetShippingRate()

  ##### Track Order using UPS
  function getTrackingInformation()
  {

	//test tracking number from the documentation which have different statuses[page 122].
	$track_info = ['1Z12345E0291980793','1Z12345E6692804405','1Z12345E0390515214'];
	//,'1Z12345E1392654435','1Z12345E6892410845','1Z12345E029198079','1Z12345E1591910450','990728071','3251026119','9102084383041101186729','cgish000116630','1Z4861WWE194914215'
	$random_keys=array_rand($track_info,1);
	//$tracking_no='9102084383041101186729';
	$tracking_no=$track_info[$random_keys];

	$data ="<?xml version=\"1.0\"?>
        <AccessRequest xml:lang='en-US'>
        <AccessLicenseNumber>{$this->strAccessLicenseNumber}</AccessLicenseNumber>
        <UserId>{$this->strUserId}</UserId>
        <Password>{$this->strPassword}</Password>
        </AccessRequest>
        <?xml version=\"1.0\"?>
        <TrackRequest>
        <Request>
        <TransactionReference>
        <CustomerContext>
        <InternalKey>blah</InternalKey>
        </CustomerContext>
        <XpciVersion>1.0</XpciVersion>
        </TransactionReference>
        <RequestAction>Track</RequestAction>
        </Request>
        <TrackingNumber>{$tracking_no}</TrackingNumber>
        </TrackRequest>";
	$ch = curl_init("https://wwwcie.ups.com/ups.app/xml/Track");
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_TIMEOUT, 60);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	$result=curl_exec ($ch);
	$data = strstr($result, '<?');
	$xml_parser = xml_parser_create();
	xml_parse_into_struct($xml_parser, $data, $vals, $index);
	xml_parser_free($xml_parser);
	$params = array();
	$level = array();
	foreach ($vals as $xml_elem) {
	  if ($xml_elem['type'] == 'open') {
		if (array_key_exists('attributes',$xml_elem)) {
		  list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);
		} else {
		  $level[$xml_elem['level']] = $xml_elem['tag'];
		}
	  }
	  if ($xml_elem['type'] == 'complete') {
		if(isset($xml_elem['value']) && $xml_elem['value'] != '') {
		  $start_level = 1;
		  $php_stmt = '$params';
		  while($start_level < $xml_elem['level']) {
			$php_stmt .= '[$level['.$start_level.']]';
			$start_level++;
		  }
		  $php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
		  eval($php_stmt);
		}

	  }
	}
	curl_close($ch);
	return $params;
  }

  	#### Address Validation

  //Returns an xml string from ups with address validation details.
  function addressvalidation($city, $state, $zip_code)
  {
	$UPSWebserviceUrlstring = "https://wwwcie.ups.com/ups.app/xml/AV";

	$data ="<?xml version=\"1.0\"?>
        <AccessRequest xml:lang=\"en-US\">
        <AccessLicenseNumber>{$this->strAccessLicenseNumber}</AccessLicenseNumber>
        <UserId>{$this->strUserId}</UserId>
        <Password>{$this->strPassword}</Password>
        </AccessRequest>
        <?xml version=\"1.0\"?>
        <AddressValidationRequest xml:lang=\"en-US\">
            <Request>
                <TransactionReference>
                    <CustomerContext>address verification data</CustomerContext>
                    <XpciVersion>1.0001</XpciVersion>
                </TransactionReference>
                <RequestAction>AV</RequestAction>
            </Request>
            <Address>
                <City>$city</City>
                <StateProvinceCode>$state</StateProvinceCode>
                <PostalCode>$zip_code</PostalCode>
            </Address>
        </AddressValidationRequest>";

	$ch = curl_init("https://wwwcie.ups.com/ups.app/xml/AV");
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_TIMEOUT, 60);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	$result=curl_exec($ch);

	return $result;

  }

//These are the 3 functions that are interdependent.
// return an array with the locations of all the occurrences. Almost like an advanced strstr.
  function Arrayfindall($needle, $haystack)
  {
	//Setting up
	$buffer=''; //We will use a 'frameshift' buffer for this search
	$pos=0; //Pointer
	$end = strlen($haystack); //The end of the string
	$getchar=''; //The next character in the string
	$needlelen=strlen($needle); //The length of the needle to find (speeds up searching)
	$found = array(); //The array we will store results in

	while($pos<$end)//Scan file
	{
	  $getchar = substr($haystack,$pos,1); //Grab next character from pointer
	  if($getchar!="\n" || $buffer < $needlelen) //If we fetched a line break, or the buffer is still smaller than the needle, ignore and grab next character
	  {
		$buffer = $buffer . $getchar; //Build frameshift buffer
		if(strlen($buffer)>$needlelen) //If the buffer is longer than the needle
		{
		  $buffer = substr($buffer,-$needlelen);//Truncunate backwards to needle length (backwards so that the frame 'moves')
		}
		if($buffer==$needle) //If the buffer matches the needle
		{
		  $found[]=$pos-$needlelen+1; //Add the location of the needle to the array. Adding one fixes the offset.
		}
	  }
	  $pos++; //Increment the pointer
	}
	if(array_key_exists(0,$found)) //Check for an empty array
	{
	  return $found; //Return the array of located positions
	}
	else
	{
	  return false; //Or if no instances were found return false
	}
  }
  function GetAddressValidationResults($string)
  {
	$xml = $string;
	//this determines how many AddressValidationResults were returned
	$number = 0;
	$num=preg_match_all('#<AddressValidationResult>#', $string, $array);


	$avr_open = $this->Arrayfindall('<AddressValidationResult>', $string);

	$avr_close = $this->Arrayfindall('</AddressValidationResult>', $string);

	$int = 0;
	$tmparray = array();
	while ($int<$num)
	{
	  $open_int = $avr_open[$int];

	  $close_int = $avr_close[$int];
	  $node =  substr($xml, $open_int, $close_int);
	  $tmpsubarray = $this->putxmlinarray($node);
	  $tmparray[$int] = $tmpsubarray;
	  $int++;
	}
	return $tmparray;

  }
  function putxmlinarray($string)
  {
	$xml_parser = xml_parser_create();
	xml_parse_into_struct($xml_parser, $string, $vals, $index);
	xml_parser_free($xml_parser);
	$params = array();
	$level = array();
	foreach ($vals as $xml_elem)
	{
	  if ($xml_elem['type'] == 'open')
	  {

		if (array_key_exists('attributes',$xml_elem))
		{

		  list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);
		}
		else
		{
		  $level[$xml_elem['level']] = $xml_elem['tag'];
		}
	  }

	  if ($xml_elem['type'] == 'complete')
	  {

		$start_level = 1;
		$php_stmt = '$params';
		while($start_level < $xml_elem['level'])
		{
		  $php_stmt .= '[$level['.$start_level.']]';
		  $start_level++;
		}
		$php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
		eval($php_stmt);
	  }

	}

	return $params;
  }
  function GetTimeInTransitResults($string)
  {
	$xml = $string;
	//this determines how many AddressValidationResults were returned
	$number = 0;
	$num=preg_match_all('#<TimeInTransitResponse>#', $string, $array);


	$avr_open = $this->findallTransit('<TimeInTransitResponse>', $string);

	$avr_close = $this->findallTransit('</TimeInTransitResponse>', $string);

	$int = 0;
	$tmparray = array();
	while ($int<$num)
	{
	  $open_int = $avr_open[$int];

	  $close_int = $avr_close[$int];
	  $node =  substr($xml, $open_int, $close_int);
	  $tmpsubarray = $this->putxmlinarray($node);
	  $tmparray[$int] = $tmpsubarray;
	  $int++;
	}
	// print_r($tmparray);

	return $tmparray;

  }
  function findallTransit($needle, $haystack)
  {
	//Setting up
	$buffer=''; //We will use a 'frameshift' buffer for this search
	$pos=0; //Pointer
	$end = strlen($haystack); //The end of the string
	$getchar=''; //The next character in the string
	$needlelen=strlen($needle); //The length of the needle to find (speeds up searching)
	$found = array(); //The array we will store results in

	while($pos<$end)//Scan file
	{
	  $getchar = substr($haystack,$pos,1); //Grab next character from pointer
	  if($getchar!="\n" || $buffer < $needlelen) //If we fetched a line break, or the buffer is still smaller than the needle, ignore and grab next character
	  {
		$buffer = $buffer . $getchar; //Build frameshift buffer
		if(strlen($buffer)>$needlelen) //If the buffer is longer than the needle
		{
		  $buffer = substr($buffer,-$needlelen);//Truncunate backwards to needle length (backwards so that the frame 'moves')
		}
		if($buffer==$needle) //If the buffer matches the needle
		{
		  $found[]=$pos-$needlelen+1; //Add the location of the needle to the array. Adding one fixes the offset.
		}
	  }
	  $pos++; //Increment the pointer
	}
	if(array_key_exists(0,$found)) //Check for an empty array
	{
	  return $found; //Return the array of located positions
	}
	else
	{
	  return false; //Or if no instances were found return false
	}
  }
  ##### Time In Transit using UPS
  function getTimeInTransitInformation($city, $state, $zip_code,$date)
  {


	$data="<?xml version=\"1.0\"?>
<AccessRequest xml:lang=\"en-US\">
 <AccessLicenseNumber>{$this->strAccessLicenseNumber}</AccessLicenseNumber>
  <UserId>{$this->strUserId}</UserId>
  <Password>{$this->strPassword}</Password>
</AccessRequest>
<?xml version=\"1.0\"?>
<TimeInTransitRequest xml:lang=\"en-US\">
  <Request>
    <TransactionReference>
	<CustomerContext>Calculate Number of Days</CustomerContext>
      <XpciVersion>1.001</XpciVersion>
    </TransactionReference>
    <RequestAction>TimeInTransit</RequestAction>
  </Request>
  <TransitFrom>
    <AddressArtifactFormat>
      <PostcodePrimaryLow>{$this->strShipperZip}</PostcodePrimaryLow>
      <CountryCode>US</CountryCode>
    </AddressArtifactFormat>
  </TransitFrom>
  <TransitTo>
    <AddressArtifactFormat>
      <PoliticalDivision2>{$city}</PoliticalDivision2>
      <PoliticalDivision1>{$state}</PoliticalDivision1>
      <PostcodePrimaryLow>{$zip_code}</PostcodePrimaryLow>
      <CountryCode>US</CountryCode>
    </AddressArtifactFormat>
  </TransitTo>
  <PickupDate>{$date}</PickupDate>
  <InvoiceLineTotal>
    <CurrencyCode>USD</CurrencyCode>
    <MonetaryValue>50</MonetaryValue>
  </InvoiceLineTotal>
  <ShipmentWeight>
    <UnitOfMeasurement>
      <Code>LBS</Code>
      <Description>Pounds</Description>
    </UnitOfMeasurement>
    <Weight>2</Weight>
  </ShipmentWeight>
</TimeInTransitRequest>";


	$ch = curl_init("https://wwwcie.ups.com/ups.app/xml/TimeInTransit");
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_TIMEOUT, 60);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	$result=curl_exec($ch);
	$params = $this->GetTimeInTransitResults($result);
	$response_code= $params[0]["TIMEINTRANSITRESPONSE"]["RESPONSE"]["RESPONSESTATUSCODE"];
	$transit_days = $params[0]["TIMEINTRANSITRESPONSE"]["TRANSITRESPONSE"]["SERVICESUMMARY"]["ESTIMATEDARRIVAL"]["BUSINESSTRANSITDAYS"];
	$days = $transit_days+2;
	return $days;
  }
}