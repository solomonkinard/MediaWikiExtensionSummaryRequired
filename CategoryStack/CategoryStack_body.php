<?php
class CategoryStack extends SpecialPage
{
    function CategoryStack() {
        parent::__construct( "CategoryStack" );
        wfLoadExtensionMessages( 'CategoryStack' );
    }

    function execute( $par ) {
        global $wgRequest, $wgOut;

        # required for special pages
        $this->setHeaders();
        
        # load css and js
        $wgOut->addHTML(self::loadJsAndCss());
        
        return true;
    }
    
	/**
	* Prepare to show link to FAST docs at top of valid articles
	*
	* @param mixed $text
	*/
	public function loadJsAndCss()
	{
	    global $wgArticle, $wgJsMimeType;
	    
	    # instantiate variables
	    $extensionName = 'CategoryStack';
        $docsStyleVersion = 1;        
        
        # prepare path for css and js
        $extensionPath = htmlspecialchars( "/extensions/$extensionName/includes" );
        $encCssFile = htmlspecialchars( "$extensionPath/$extensionName.css?$docsStyleVersion" );
        $encJsFile = htmlspecialchars( "$extensionPath/$extensionName.js?$docsStyleVersion" );
        $jQueryPath = htmlspecialchars( "$extensionPath/jquery-1.6.4.min.js" );
        
        # add css and javascript
        $text = <<<EOT
<link href="$encCssFile" rel="stylesheet" type="text/css">
<script type="$wgJsMimeType" src="$jQueryPath"></script>
<script type="$wgJsMimeType" src="$encJsFile"></script>
EOT;

		return $text;
	}
    
    public static function efExampleParserFunction_Setup( &$parser ) {
        # Set a function hook associating the "example" magic word with our function
        $parser->setFunctionHook( 'CategoryStack', 'CategoryStack::efExampleParserFunction_Render' );
    
        return true;
    }
    
    public static function efExampleParserFunction_Magic( &$magicWords, $langCode ) {
        # Add the magic word
        # The first array element is whether to be case sensitive, in this case (0) it is not case sensitive, 1 would be sensitive
        # All remaining elements are synonyms for our parser function
        $magicWords['CategoryStack'] = array( 1, 'CategoryStack' );

        # unless we return true, other parser functions extensions won't get loaded.
        return true;
    }
    
    /**
     * param2 can be used to remove a pre-pended string
     */
    public static function efExampleParserFunction_Render( $parser, $parentCategory = '', $removeThisFromTextStart = ':Category:' ) {
    	# which namespaces to worry about
    	$myNamespaces = array (NS_MAIN, NS_CATEGORY);
    	
    	# get titles of children
    	foreach ($myNamespaces as $myNamespace) {
	        $sql = self::getCategoryChildren($parentCategory, $myNamespace);
	        $res[$myNamespace] = self::sqlReader($sql);
	    }
	    
        # prepare to keep all records for sorting
        $allLinks = array();
        # remove "FAST/Docs/"
        /**
         * @todo start: make html work when possible. for now use wikitext because it works easily.
    	foreach ($res as $category) {
    	    $title = $category->cl_sortkey;
    	    $linkText = substr($title, stripos($title, $removeThis)+strlen($removeThis));
            $link = htmlentities('/index.php?title=Category:' . $title);
    	    // $wgOut->addHTML(sprintf('<a href="%s" title="%s">%s</a> | ', $link, $title, $linkText));
    	    $allLinks[$linkText] = sprintf('<a href="%s" title="%s">%s</a> | ', $link, $title, $linkText);
    	}
    	 */
	    foreach ($res as $namespace => $resObj) {
	    	# ensure category is pre-pended to link
	    	$prependText = '';
	    	if ($namespace == NS_CATEGORY) {
	    		$prependText = ':Category:';
	    	}
	    	# output items
	        foreach ($resObj as $category) {
	        	$title = $linkText = $prependText . $category->page_title;
	        	if (stripos($title, $removeThisFromTextStart) === 0) {
	        		$linkText = substr($title, stripos($title, $removeThisFromTextStart)+strlen($removeThisFromTextStart));
	        	}
	        	// $wgOut->addHTML(sprintf('<a href="%s" title="%s">%s</a> | ', $link, $title, $linkText));
	        	$allLinks[$linkText] = sprintf('[[%s|%s]]', $title, $linkText );
	        }
	    }
    	natcasesort($allLinks); # sort all links in alphabetical order, ignoring case sensitivity
    	# show all links
    	$output = '<br /><br /><center><div style="width:800;">';
    	$output .= implode(' | ', $allLinks);
    	$output .= '</div></center>';
    
    return $output;
    }
    
    public static function getCategoryChildren($category, $namespace) {
        $sql = 'SELECT page.page_title'
			. ' FROM page'
			. ' INNER JOIN categorylinks on categorylinks.cl_from=page.page_id'
			. ' WHERE categorylinks.cl_to="' . $category . '"'
        	. ' AND page.page_namespace="' . $namespace . '";';
        
        return $sql;
    }
    
    public static function sqlReader($sql) {
    	global $dbr;
    	 
    	# get list of pages that are marked for deletion
    	if (!$dbr)
    	    $dbr =& wfGetDB( DB_SLAVE );     # if necessary, obtains read-only connection to DB with MediaWiki API
    	 
    	# obtain and execute query
    	$res = $dbr->query($sql);
    	 
    	# handle db results
    	foreach ($res as $result) {
    		$dbResult[] = $result;
    	}
    	 
    	$dbr->freeResult($res);                     # free result
    	
    	if (isset($dbResult))
    		return $dbResult;
    }
}