<?php
/*
 * The PHP: Utility Functions
 */


/* function:  generates thumbnail */
function make_thumb($src,$dest,$desired_width) {
	/* read the source image */
	$source_image = imagecreatefromjpeg($src);
	$width = imagesx($source_image);
	$height = imagesy($source_image);
	/* find the "desired height" of this thumbnail, relative to the desired width  */
	$desired_height = floor($height*($desired_width/$width));
	/* create a new, "virtual" image */
	$virtual_image = imagecreatetruecolor($desired_width,$desired_height);
	/* copy source image at a resized size */
	imagecopyresized($virtual_image,$source_image,0,0,0,0,$desired_width,$desired_height,$width,$height);
	/* create the physical thumbnail image to its destination */
	imagejpeg($virtual_image,$dest);
}

/* function:  returns files from dir */
function get_files($images_dir,$exts = array('jpg')) {
	$files = array();
	if($handle = opendir($images_dir)) {
		while(false !== ($file = readdir($handle))) {
			$extension = strtolower(get_file_extension($file));
			if($extension && in_array($extension,$exts)) {
				$files[] = $file;
			}
		}
		closedir($handle);
	}
	return $files;
}

/* function:  returns a file's extension */
function get_file_extension($file_name) {
	return substr(strrchr($file_name,'.'),1);
}

/*
We'll use three utility functions to make the system work: get_files (retrieves all of the files in a given directory), get_file_extension, and make_thumb (generates a thumbnail image from a source image). These are good functions to keep at hand for other purposes too.
*/


/*
 * The PHP: Setting and HTML Generation
 */

/** settings **/
$images_dir = 'preload-images/';
$thumbs_dir = 'preload-images-thumbs/';
$thumbs_width = 200;
$images_per_row = 3;

/** generate photo gallery **/
$image_files = get_files($images_dir);
if(count($image_files)) {
	$index = 0;
	foreach($image_files as $index=>$file) {
		$index++;
		$thumbnail_image = $thumbs_dir.$file;
		if(!file_exists($thumbnail_image)) {
			$extension = get_file_extension($thumbnail_image);
			if($extension) {
				make_thumb($images_dir.$file,$thumbnail_image,$thumbs_width);
			}
		}
		echo '<a href="',$images_dir.$file,'" class="photo-link smoothbox" rel="gallery"><img src="',$thumbnail_image,'" /></a>';
		if($index % $images_per_row == 0) { echo '<div class="clear"></div>'; }
	}
	echo '<div class="clear"></div>';
}
else {
	echo '<p>There are no images in this gallery.</p>';
}


/*
The first step is to define a few simple settings which will dictate image paths, the width by which all thumbnails will be created, and the number of images per row. The action begins with rounding up all of the files. With every image in the gallery, we check to see if a thumbnail exists. If a thumbnail doesn't exist, we use PHP and the utility function above to generate one. When the thumbnail is generated (or there was one there in the first place), we output the HTML link/image. I've given the A element the "smoothbox" CSS class so that Smoothbox will make the larger image display in the lightbox.
*/
