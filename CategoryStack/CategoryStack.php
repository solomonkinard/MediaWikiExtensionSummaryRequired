<?php
/**
 * Usage: {{#CategoryStack:category | remove-this-prepended-string }}
 */

# Not a valid entry point, skip unless MEDIAWIKI is defined
if ( !defined( 'MEDIAWIKI' ) ) {
        echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/CategoryStack/CategoryStack.php" );
EOT;
        exit( 1 );
}

$wgExtensionCredits['parser'][] = array(
        'path' => __FILE__,     // Magic so that svn revision number can be shown
        'name' => "CategoryStack",
        'description' => "A simple example parser function extension",    // Should be using descriptionmsg instead so that i18n is supported (but this is a simple example).
        'version' => '0.2', 
        'author' => "Solomon Kinard",
        'url' => "http://www.solomonkinard.com",
);
 
# Define a setup function
$wgHooks['ParserFirstCallInit'][] = 'CategoryStack::efExampleParserFunction_Setup';
# Add a hook to initialise the magic word
$wgHooks['LanguageGetMagic'][]    = 'CategoryStack::efExampleParserFunction_Magic';
 
$dir = dirname( __FILE__ ) . '/';
$wgAutoloadClasses['CategoryStack'] = $dir . 'CategoryStack_body.php';
$wgSpecialPages['CategoryStack'] = 'CategoryStack';
$wgExtensionMessagesFiles['CategoryStack'] = $dir . 'CategoryStack.i18n.php';
$wgExtensionAliasesFiles['CategoryStack'] = $dir . 'CategoryStack.alias.php';

$csChangeLog = array('0.1' => 'Beta entry',
					'0.2' => 'Trying to add category links');