<?php 

require_once 'RequestParser.php';
require_once 'MimeResponseFactory.php';

class SearchRequestHandler
{
    private $trophicService;
    private $parser;
    private $mimeResponse;

    public function __construct()
    {

    }
    public function requestHandlerDriver($urlPost)
    {
    	$this->parsePOST($urlPost); 
    	$this->getTrophicService();
        return $this->createMimeResponse(); #mime is a reference to 'Internet media type'
    }
    public function parsePOST($urlPost)
    {
    	$this->parser = new RequestParser();
        $this->parser->parse($urlPost);
    }

    public function getTrophicService()
    {
    	$serviceFactory = new TrophicServiceFactory();
    	$this->trophicService = $serviceFactory->createServiceOfType($this->parser->getServiceType());
    	return $this->trophicService;
    }
    public function getMimeResponse()
    {
        $responseFactory = new MimeResponseFactory();
        $this->mimeResponse = $responseFactory->createMimeOfType($this->parser->getResponseType());
        return $this->mimeResponse;
    }
    public function createMimeResponse()
    {
        $responseFactory = new MimeResponseFactory();
        $this->mimeResponse = $responseFactory->createMimeOfType($this->parser->getResponseType());

        $searchType = $this->parser->getSearchType();
        $responseObjectContainer = array();

        $speciesSubject = $this->parser->getSubjectName();

        if ($searchType == 'fuzzySearch') {
            $fuzzyResponseObject = new FuzzyResponseObject();
            $phpServiceObject = $this->trophicService->findCloseTaxonNameMatches($speciesSubject);
            $this->mimeResponse->addFuzzySearchResultToResponse($fuzzyResponseObject, $phpServiceObject);
            $fuzzyResponseObject->fuzzyName = $speciesSubject;
            $responseObjectContainer[0] = $fuzzyResponseObject;
        } elseif ($searchType == 'exactMatchObservation') {
            $responseObject = new ResponseObject();
            
            if ($this->parser->shouldIncludePrey()) {
                $phpServiceObject = $this->trophicService->findObservedPreyForPredator($speciesSubject, $this->parser->getInteractionFilters(), $this->parser->getLocationConstraints(),$this->parser->getResponseType());
                $this->mimeResponse->addObservationToResponse($responseObject, $phpServiceObject, 'prey');
            } 
            if ($this->parser->shouldIncludePredators()) {
                $phpServiceObject = $this->trophicService->findObservedPredatorsForPrey($speciesSubject, $this->parser->getInteractionFilters(), $this->parser->getLocationConstraints(),$this->parser->getResponseType());
                $this->mimeResponse->addObservationToResponse($responseObject, $phpServiceObject, 'pred');
            }
            $responseObject->scientificName = $speciesSubject;
            $responseObjectContainer[0] = $responseObject;

        } elseif ($searchType == 'taxonURLLookup') {
            $responseObject = new ResponseObject();
            
            $phpServiceObject = $this->trophicService->findExternalTaxonURL($speciesSubject);
            $this->mimeResponse->addTaxonURLLookupToResponse($responseObject, $phpServiceObject);
            $responseObject->scientificName = $speciesSubject;
            $responseObjectContainer = $responseObject;
        }else {
            $responseObject = new ResponseObject();
            
            if ($this->parser->shouldIncludePrey()) {
                $phpServiceObject = $this->trophicService->findPreyForPredator($speciesSubject);
                $this->mimeResponse->addPreyListToResponse($responseObject, $phpServiceObject);
            } 
            if ($this->parser->shouldIncludePredators()) {
                $phpServiceObject = $this->trophicService->findPredatorForPrey($speciesSubject);
                $this->mimeResponse->addPredatorListToResponse($responseObject, $phpServiceObject);
            } 
            $responseObject->scientificName = $speciesSubject;
            $responseObjectContainer[0] = $responseObject;
        }
        
        $mimeResponseFinalObject = $this->mimeResponse->cleanObject($responseObjectContainer);

        return $mimeResponseFinalObject;
    }


}
class ResponseObject
{
    public $scientificName;
}
class FuzzyResponseObject
{
    public $fuzzyName;
    public $matches = array();
}
?>