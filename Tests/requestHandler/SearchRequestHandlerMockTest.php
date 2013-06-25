<?php

require_once 'requestHandler/SearchRequestHandler.php';

class SearchRequestHandlerMockTest extends PHPUnit_Framework_TestCase
{
	private $handler;
	private $postRequest;
	private $observationPostRequest;
	private $observationPostRequestPred;

	public function setUp()
    {
    	$this->handler = new SearchRequestHandler();
    	$this->postRequest = array("serviceType" => "mock",
			   "findPrey"=>"on",
			   "subjectName" => "Scomberomorus cavalla", "listStyle" => true);
    	$this->observationPostRequest = $arrayName = array("serviceType" => "mock",
			   "findPrey"=>"on",
			   "subjectName" => "Ariopsis felis");
    	$this->observationPostRequestPred = $arrayName = array("serviceType" => "mock",
			   "findPredators"=>"on",
			   "subjectName" => "Callinectes sapidus");

    }

	public function testGetTrophicServiceMockFindPreyForPredator()
	{
		$trophicResultString = array();
		$expectedPreyNames = array("Synalpheus latastei", "Lutjanus jocu");

    	$this->handler->parsePOST($this->postRequest);

		$trophicService = $this->handler->getTrophicService();
		$trophicResultString = $trophicService->findPreyForPredator("Scomberomorus cavalla");
		
		$this->assertEquals(count($expectedPreyNames), count($trophicResultString));

		$iterator = 0;
		foreach ($trophicResultString as $value) {
			$this->assertEquals($value, $expectedPreyNames[$iterator]);
			$iterator++;
		}
	}
	public function testGetTrophicServiceMockFindObservedPreyForPredator()
	{
		$trophicResultString = array();
		$expectedPreyResult = array("Micropogonias undulatus", 28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, 613013, "Brevoortia patronus", 28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, 613013, "Farfantepenaeus aztecus", 28.645202, -96.099923, 0.0, "Senol Akin", 923695200000, 613014, "Mollusca",8.645202, -96.099923, 0.0, "Senol Akin", 923695200000, 613013, "T-Rex", 8.645202, -96.099923, 0.0, "MichaelCas", 923695200000, 613014, "Pterodactyl", 8.645202, -96.099923, 0.0, "MichaelCas", 923695200000, 613015);

    	$this->handler->parsePOST($this->observationPostRequest);

		$trophicService = $this->handler->getTrophicService();
		$trophicResultString = $trophicService->findObservedPreyForPredator("Ariopsi felis", null, null);
		
		$this->assertEquals((count($expectedPreyResult)/7), count($trophicResultString)); #7 catogries 

		$iterator = 0;
		foreach ($trophicResultString as $value) {
			for ($i=0; $i < 6; $i++) { #value holds 7 catagories
				$this->assertEquals($value[$i], $expectedPreyResult[$iterator]);
				$iterator++;
			}
			$iterator++;
		}
	}
	

	public function testCreateJSONResponseMockFindPreyForPredator()
	{
    	$jsonTestString = '[{"scientificName":"Scomberomorus cavalla","preyInstances":[{"prey":["Synalpheus latastei","Lutjanus jocu"]}]}]';
		
		$jsonObject = $this->handler->requestHandlerDriver($this->postRequest);
		$this->assertEquals($jsonTestString, $jsonObject);
	}
	public function testCreateJSONResponseMockFindObservedPreyForPredator()
	{
    	$jsonTestString = '[{"scientificName":"Ariopsis felis","preyInstances":[{"prey":["Micropogonias undulatus","Mollusca"],"date":923695200000,"lat":28.645202,"long":-96.099923,"alt":0,"ref":"Senol Akin"},{"prey":["Brevoortia patronus","T-Rex","Pterodactyl"],"date":923695200000,"lat":28.645202,"long":-96.099923,"alt":0,"ref":"Senol Akin"},{"prey":["Farfantepenaeus aztecus"],"date":923695200000,"lat":28.645202,"long":-96.099923,"alt":0,"ref":"Senol Akin"}]}]';
		
		$jsonObject = $this->handler->requestHandlerDriver($this->observationPostRequest);
		$this->assertEquals($jsonTestString, $jsonObject);
	}
	public function testCreateJSONResponseMockFindObservedPredatorForPrey()
	{
		unset($this->observationPostRequestPred["findPrey"]);
    	$jsonTestString = '[{"scientificName":"Callinectes sapidus","predInstances":[{"pred":["Micropogonias undulatus","Actinopterygii"],"date":923695200000,"lat":28.645202,"long":-96.099923,"alt":0,"ref":"Rogers 1977"},{"pred":["Great White","Mollusca","Tiger Shark"],"date":923695200000,"lat":28.645202,"long":-96.099923,"alt":0,"ref":"John Mayer"},{"pred":["Velociraptor"],"date":923695200000,"lat":8.645202,"long":-96.099923,"alt":0,"ref":"MichaelCas"}]}]';
		
		$jsonObject = $this->handler->requestHandlerDriver($this->observationPostRequestPred);
		$this->assertEquals($jsonTestString, $jsonObject);
	}
	public function testSearchObservedPredatorAndPrey()
	{
		$this->observationPostRequestPred["findPredators"] = "on";
		$this->observationPostRequestPred["findPrey"] = "on";

    	$jsonTestString = '[{"scientificName":"Callinectes sapidus","preyInstances":[{"prey":["Micropogonias undulatus","Mollusca"],"date":923695200000,"lat":28.645202,"long":-96.099923,"alt":0,"ref":"Senol Akin"},{"prey":["Brevoortia patronus","T-Rex","Pterodactyl"],"date":923695200000,"lat":28.645202,"long":-96.099923,"alt":0,"ref":"Senol Akin"},{"prey":["Farfantepenaeus aztecus"],"date":923695200000,"lat":28.645202,"long":-96.099923,"alt":0,"ref":"Senol Akin"}],"predInstances":[{"pred":["Micropogonias undulatus","Actinopterygii"],"date":923695200000,"lat":28.645202,"long":-96.099923,"alt":0,"ref":"Rogers 1977"},{"pred":["Great White","Mollusca","Tiger Shark"],"date":923695200000,"lat":28.645202,"long":-96.099923,"alt":0,"ref":"John Mayer"},{"pred":["Velociraptor"],"date":923695200000,"lat":8.645202,"long":-96.099923,"alt":0,"ref":"MichaelCas"}]}]';
		
		$jsonObject = $this->handler->requestHandlerDriver($this->observationPostRequestPred);
		$this->assertEquals($jsonTestString, $jsonObject);	
	}

	public function testSearchPredatorAndPrey()
	{
		$this->postRequest["subjectName"] = "Ariopsis felis";
		$this->postRequest["findPredators"] = "on";
		$this->postRequest["findPrey"] = "on";
    	
		$jsonTestString = '[{"scientificName":"Ariopsis felis","preyInstances":[{"prey":["Synalpheus latastei","Lutjanus jocu"]}],"predInstances":[{"pred":["Ariopsis felis","Scomberomorus cavalla"]}]}]';
		
		$jsonObject = $this->handler->requestHandlerDriver($this->postRequest);
		$this->assertEquals($jsonTestString, $jsonObject);
	}


	public function testGetTrophicServiceMockFindPredatorForPrey()
	{
		$trophicResultString = array();
		$expectedPredNames =array("Ariopsis felis", "Scomberomorus cavalla");

		$this->postRequest["subjectName"] = "Mugil cephalus";
    	unset($this->postRequest["findPrey"]);
		$this->postRequest["findPredators"] = "on";
    	$this->handler->parsePOST($this->postRequest);

		$trophicService = $this->handler->getTrophicService();
		$trophicResultString = $trophicService->findPredatorForPrey("Mugil cephalus");
		
		$this->assertEquals(count($expectedPredNames), count($trophicResultString));

		$iterator = 0;
		foreach ($trophicResultString as $value) {
			$this->assertEquals($value, $expectedPredNames[$iterator]);
			$iterator++;
		}
	}

	public function testCreateJSONResponseMockFindPredatorForPrey()
	{
		$this->postRequest["subjectName"] = "Mugil cephalus";
		unset($this->postRequest["findPrey"]);
		$this->postRequest["findPredators"] = "on";
    	
		$jsonTestString = '[{"scientificName":"Mugil cephalus","predInstances":[{"pred":["Ariopsis felis","Scomberomorus cavalla"]}]}]';
		
		$jsonObject = $this->handler->requestHandlerDriver($this->postRequest);
		$this->assertEquals($jsonTestString, $jsonObject);
	}

	public function testGetTrophicServiceMockFindCloseTaxonNameMatches()
	{
		$trophicResultString = array();
		$expectedMatchNames =array('Ariopsis felis', 'Scomberomorus cavalla');

    	$this->postRequest["suggestion"] = "Scomb";
    	$this->handler->parsePOST($this->postRequest);

		$trophicService = $this->handler->getTrophicService();
		$trophicResultString = $trophicService->findCloseTaxonNameMatches("Scomb");
		
		$this->assertEquals(count($expectedMatchNames), count($trophicResultString));

		$iterator = 0;
		foreach ($trophicResultString as $value) {
			$this->assertEquals($value, $expectedMatchNames[$iterator]);
			$iterator++;
		}
	}

	public function testCreateJSONResponseMockFindCloseTaxonNameMatches()
	{
		$this->postRequest["suggestion"] = "Scomb";
    	$this->handler->parsePOST($this->postRequest);

		$this->handler->getTrophicService();

		$jsonTestString = '[{"fuzzyName":"Scomb","matches":["Ariopsis felis","Scomberomorus cavalla"]}]';
		
		$jsonObject = $this->handler->createJSONResponse();
		$this->assertEquals($jsonTestString, $jsonObject);
	}
	public function testFindExternalTaxonURLMock()
	{
		$post = array("serviceType" => "mock", "deepLinks" => "homo sapiens");
		$this->handler->parsePOST($post);

		$this->handler->getTrophicService();

		$jsonTestString = '{"scientificName":"homo sapiens","URL":"http://eol.org/pages/327955"}';

		$jsonObject = $this->handler->createJSONResponse();
		$this->assertEquals($jsonTestString, $jsonObject);
	}

}


?>