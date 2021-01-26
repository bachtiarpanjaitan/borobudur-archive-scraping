# Borobudur Archive Scraping
Scraping website [Archive Borobudur](http://arsip.borobudur.id) menggunakan PHP

## Requirements
- PHP MAX_EXECUTE_TIME = 3600
- PHP MEMORY_LIMIT = 512M

## Result Format Data

	[
		0 : {
			'link': ...,
			'image': ...,
			'image_base64':...,
			'title':...,
			'reference_code':...,
			'level_description':...,
			'date':...,
			'part_of':...
		}
	]
