<?php
/**
 * Netscape bookmark file format is documented at
 * http://msdn.microsoft.com/en-us/library/aa753582(VS.85).aspx
 *
 * SemanticScuttle - your social bookmark manager.
 *
 * PHP version 5.
 *
 * @category Bookmarking
 * @package  SemanticScuttle
 * @author   Benjamin Huynh-Kim-Bang <mensonge@users.sourceforge.net>
 * @author   Christian Weiske <cweiske@cweiske.de>
 * @author   Eric Dane <ericdane@users.sourceforge.net>
 * @license  GPL http://www.gnu.org/licenses/gpl.html
 * @link     http://sourceforge.net/projects/semanticscuttle
 */

/*
    CODE MODIFIED by q2apro / memelpower.com for Scuttle HTML Export
    SEE https://github.com/q2apro/scuttle-to-shaarli
*/

define('DB_HOST', 'localhost');
define('DB_PASS', 'db-password');
define('DB_NAME', 'db-name');

$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
mysqli_set_charset($db, 'utf8'); // set charset of mysql to UTF8, so we do not need utf8_encode()
mysqli_select_db($db, DB_NAME);

$query = mysqli_query($db, "SELECT * FROM `sc_bookmarks`");

$bookmarks   = array();
$bookmarkids = array();

while($row = mysqli_fetch_assoc($query))
{
	$bookmarks[]   = $row;
	$bookmarkids[] = $row['bId'];
}

if(count($bookmarkids)) 
{
	$tags = getTagsForBookmarks($bookmarkids, $db);
	foreach($bookmarks as &$bookmark)
	{
		$bookmark['tags'] = $tags[$bookmark['bId']];
	}
}

$bookmarks_array = array ('bookmarks' => $bookmarks, 'total' => $total);


// Set up the XML file and output all the posts.
echo '<!DOCTYPE NETSCAPE-Bookmark-file-1>'."\r\n";
echo '<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8" />';
echo '<!-- This is an automatically generated file. -->'."\r\n";
echo '<TITLE>Bookmarks</TITLE>'."\r\n";
echo '<H1 LAST_MODIFIED="'. date('U') .'">Bookmarks</H1>';
echo '<DL>'."\r\n";

foreach ($bookmarks_array['bookmarks'] as $row) 
{
    if (is_null($row['bDescription']) || (trim($row['bDescription']) == '')) 
	{
        $description = '';
    }
	else 
	{
		$desc = mb_convert_encoding(filter($row['bDescription'], 'xml'), 'Windows-1252', 'UTF-8');
        $description = 'description="'. $desc .'" ';
    }

    $taglist = '';
    if (count($row['tags']) > 0) 
    {
        foreach ($row['tags'] as $tag) 
        {
            $taglist .= convertTag($tag) .',';
        }

        $taglist = substr($taglist, 0, -1);
    } 
    else
    {
        $taglist = 'system:unfiled';
    }
	$title = filter($row['bTitle'], 'xml');
    echo "\t<DT><A HREF=\"". filter($row['bAddress'], 'xml') .'" '. $description .' hash="'. md5($row['bAddress']) .'" TAGS="'. filter($taglist, 'xml') .'" ADD_DATE="'. date('U', strtotime($row['bDatetime'])) ."\" >" . mb_convert_encoding($title, 'Windows-1252', 'UTF-8') ."</a>\r\n";
}


echo '</DL>';


function getTagsForBookmarks($bookmarkids, $db)
{
	if (!is_array($bookmarkids)) 
	{
		return false;
	}
	else if (count($bookmarkids) == 0) 
	{
		return array();
	}

	$query = 'SELECT tag, bId FROM `sc_tags` '
		. ' WHERE bId IN (' . implode(',', $bookmarkids) . ')'
		. ' AND LEFT(tag, 7) <> "system:"'
		. ' ORDER BY id, bId ASC';
	
	if (!($dbresult = mysqli_query($db, $query)))
	{
		echo 'Could not get tags';
		return false;
	}

	$tags = array_combine(
		$bookmarkids,
		array_fill(0, count($bookmarkids), array())
	);
	while($row = mysqli_fetch_assoc($dbresult))
	{
		$tags[$row['bId']][] = $row['tag'];
	}
	return $tags;
}

// Converts tags:
// - direction = out: convert spaces to underscores;
// - direction = in: convert underscores to spaces.
function convertTag($tag, $direction = 'out') 
{
	if ($direction == 'out') 
	{
		$tag = str_replace(' ', '_', $tag);
	} 
	else 
	{
		$tag = str_replace('_', ' ', $tag);
	}
	return $tag;
}

function filter($data, $type = NULL) 
{
	if (is_string($data)) 
	{
		$data = trim($data);
		$data = stripslashes($data);
		switch ($type) 
		{
			case 'url':
				$data = rawurlencode($data);
				break;
			default:
				$data = htmlspecialchars($data);
				break;
		}
	} 
	else if (is_array($data)) 
	{
		foreach(array_keys($data) as $key) 
		{
			$row =& $data[$key];
			$row = filter($row, $type);
		}
	}
	return $data;
}

?>
