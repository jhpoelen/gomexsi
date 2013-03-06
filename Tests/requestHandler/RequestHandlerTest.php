<?php

require_once 'requestHandler/RequestHandler.php';

class RequestHandlerTest extends PHPUnit_Framework_TestCase
{
	private $handler;

	public function setUp()
    {
    	$this->handler = new RequestHandler();
    }
	public function testParsePOSTMock()
	{
		$mockURL = array("serviceType" => "mock", "predName" => "Scomberomorus cavalla");
		$this->assertEquals("", $this->handler->parsePOST($mockURL));
	}
	public function testGetTrophicServiceMock()
	{
		$mockURL = array("serviceType" => "mock", "predName" => "Scomberomorus cavalla");
		$this->handler->parsePOST($mockURL);

		$trophicService = $this->handler->getTrophicService();
		$preyNames = array("Synalpheus latastei", "Lutjanus jocu");

		$iterator = 0;
		foreach ($trophicService as $value) {
			$this->assertEquals($trophicService[$iterator], $value);
			$iterator++;
		}
	}
	public function testCreatJSONResponseMock()
	{
		$mockURL = array("serviceType" => "mock", "predName" => "Scomberomorus cavalla");
		$this->handler->parsePOST($mockURL);
		$this->handler->getTrophicService();

		$jsonTestString = '[{"scientificName": "Scomberomorus cavalla", "subjectInstances": {"prey": [{"scientificName": "Synalpheus latastei"}, {"scientificName": "Lutjanus jocu"}]}}]';
		// http://jsonlint.com/ will format this for anyone who wants to look at it in a more readable structure 

		$jsonObject = $this->handler->creatJSONResponse('');
		$this->assertEquals($jsonTestString, $jsonObject);
	}
}
?>