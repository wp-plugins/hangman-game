<?php
/*
Plugin Name: Hangman game
Plugin URI: http://www.farbundstil.de/games/1036-wordpress-game-plugin.php
Description: Put a simple hangman game on your wordpress site 
Version: 1.0
Author: Marcel Hollerbach
Author URI: http://www.farbundstil.de

Instructions

Requires at least Wordpress: 2.1.3

1. Upload the hangman folder to your wordpress plugins directory (./wp-content/plugins)
2. Login to the Wordpress admin panel and activate the plugin "Hangman game"
3. Create a new post or page and enter the tag [HANGMAN]

That's it ... Have fun!




>> Copyright and Terms:

This software is copyright (C) 2002-2004 0php.com.  It is distributed
under the terms of the GNU General Public License (GPL).  Because it is licensed
free of charge, there is NO WARRANTY, it is provided AS IS.  The author can not
be held liable for any damage that might arise from the use of this software.
Use it at your own risk.


Credits: The plugin is based on the HANGMAN script of http://www.0php.com/   


=======================================================================================*/

define("HANGMAN_REGEXP", "/\[HANGMAN]/");

// Pre-2.6 compatibility
if ( !defined('WP_CONTENT_URL') )
	define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');

define('HANGMAN_URLPATH', WP_CONTENT_URL.'/plugins/'.plugin_basename( dirname(__FILE__)).'/' );

add_action('wp_head', 'HANGMAN_addcss', 1);
add_action('wp_head', 'HANGMAN_addrobot', 1);

//Add stylesheet to site
function HANGMAN_addcss(){

    echo "<link rel=\"stylesheet\" href=\"". HANGMAN_URLPATH. "hangman_style.css\"  type=\"text/css\" media=\"screen\" />";

}

//To prevent Google from playing hangman, add the lines in the <head> part
function HANGMAN_addrobot(){
    echo "<META NAME=\"robots\" CONTENT=\"NOINDEX,NOFOLLOW\">";
}


function HANGMAN_plugin_callback($match)
{
    
    # Translation
    # Use this part to customize your wording
    $LANG_HANGED = "SORRY, YOU ARE HANGED!!!";
    $LANG_TERM = "word";
    $LANG_PHRASE = "phrase";
    $LANG_THE = "The";
    $LANG_WAS = "was";
    $LANG_PLAYAGAIN = "Play again";
    $LANG_GUESSESLEFT = "Wrong Guesses Left";
    $LANG_CHOOSELETTER = "Choose a letter";
    $LANG_YOUWIN = "Congratulations!!! You win!!!";
    
    $max=6;	# maximum number of wrong
    
    $Category = "Music and stars";
    
    # list of words (phrases) to guess below, separated by new line
    $list = "TOKIO HOTEL
    BEATSTAKES
    KID ROCK
    KANYE WEST
    PANIC AT THE DISCO
    MARIAH CAREY
    AVRIL LAVIGNE
    JONAS BROTHERS
    BABYSHAMBLES
    CHRISTINA AGUILERA
    AMY WINEHOUSE
    KYLIE MINOGUE
    KATY PERRY
    PARIS HILTON
    LEONA LEWIS
    BRITNEY SPEARS
    LINKIN PARK
    MARK RONSON
    P DIDDY
    FRANZ FERDINAND
    KEYBOARD
    GRAND PIANO
    STAGE LIGHTS
    LIVE ACT";
    
    
    # make sure that any characters to be used in $list are in either
    #   $alpha OR $additional_letters, but not in both.  It may not work if you change fonts.
    #   You can use either upper OR lower case of each, but not both cases of the same letter.
    
    # below ($alpha) is the alphabet letters to guess from.
    #   you can add international (non-English) letters, in any order, such as in:
    #   $alpha = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $alpha = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    
    # below ($additional_letters) are extra characters given in words; '?' does not work
    #   these characters are automatically filled in if in the word/phrase to guess
    $additional_letters = " -.,;!?%&0123456789";
    
    #========= do not edit below here ======================================================
    

    $len_alpha = strlen($alpha);
    
    if(isset($_GET["n"])) $n=$_GET["n"];
    if(isset($_GET["letters"])) $letters=$_GET["letters"];
    if(!isset($letters)) $letters="";
    
    echo "<div id=\"hangman\">";
    
    $self = get_permalink();
    
    $links="";
    
    
    # error_reporting(0);
    $list = strtoupper($list);
    $words = explode("\n",$list);
    srand ((double)microtime()*1000000);
    $all_letters=$letters.$additional_letters;
    $wrong = 0;
    
 
    if (!isset($n)) { $n = rand(1,count($words)) - 1; }
    $word_line="";
    $word = trim($words[$n]);
    $done = 1;
    for ($x=0; $x < strlen($word); $x++)
    {
      if (strstr($all_letters, $word[$x]))
      {
        if ($word[$x]==" ") $word_line.="&nbsp; "; else $word_line.=$word[$x];
      } 
      else { $word_line.="_<font size=1>&nbsp;</font>"; $done = 0; }
    }
    
    if (!$done)
    {
    
      for ($c=0; $c<$len_alpha; $c++)
      {
        if (strstr($letters, $alpha[$c]))
        {
          if (strstr($words[$n], $alpha[$c])) {$links .= "\n<B>$alpha[$c]</B> "; }
          else { $links .= "\n<FONT color=\"red\">$alpha[$c] </font>"; $wrong++; }
        }
        else
        { $links .= "\n<A HREF=\"$self?letters=$alpha[$c]$letters&n=$n\">$alpha[$c]</A> "; }
      }
      $nwrong=$wrong; if ($nwrong>6) $nwrong=6;
      echo "\n<p><BR>\n<IMG SRC=\"". HANGMAN_URLPATH. "images/hangman_$nwrong.gif\" ALIGN=\"MIDDLE\" BORDER=0 WIDTH=100 HEIGHT=100 ALT=\"Wrong: $wrong out of $max\">\n";
    
      if ($wrong >= $max)
      {
        $n++;
        if ($n>(count($words)-1)) $n=0;
        echo "<BR><BR><H1><font size=5>\n$word_line</font></H1>\n";
        echo "<p><BR><FONT color=\"red\"><BIG>" . $LANG_HANGED . "</BIG></FONT><BR><BR>";
        if (strstr($word, " ")) $term=$LANG_PHRASE; else $term=$LANG_TERM;
        echo "$LANG_THE $term $LANG_WAS \"<B>$word</B>\"<BR><BR>\n";
        echo "<A HREF=$self?n=$n>" . $LANG_PLAYAGAIN . "</A>\n\n";
      }
      else
      {
        echo " &nbsp; # ". $LANG_GUESSESLEFT . ": <B>".($max-$wrong)."</B><BR>\n";
        echo "<H1><font size=5>\n$word_line</font></H1>\n";
        echo "<P><BR>" . $LANG_CHOOSELETTER . ":<BR><BR>\n";
        echo "$links\n";
      }
    }
    else
    {
      $n++;	# get next word
      if ($n>(count($words)-1)) $n=0;
      echo "<BR><BR><H1><font size=5>\n$word_line</font></H1>\n";
      echo "<P><BR><BR><B>" . $LANG_YOUWIN . "</B><BR><BR><BR>\n";
      echo "<A HREF=$self?n=$n>" . $LANG_PLAYAGAIN . "</A>\n\n";
    }
    
    echo "</div>";
    

	return ($output);
}

function HANGMAN_plugin($content)
{
	return (preg_replace_callback(HANGMAN_REGEXP, 'HANGMAN_plugin_callback', $content));
}

add_filter('the_content', 'HANGMAN_plugin');
add_filter('comment_text', 'HANGMAN_plugin');


?>
