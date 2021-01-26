# Borobudur Archive Scraping
Scraping website [Archive Borobudur](http://arsip.borobudur.id) menggunakan PHP

## Requirements
- PHP MAX_EXECUTE_TIME = 3600
- PHP MEMORY_LIMIT = 512M

## Result Format Data

	[
		0 : {
			'link': ...,
			'image_thumbnail': ...,
			'title':...,
			'reference_code':...,
			'level_description':...,
			'date':...,
			'part_of':...,
			'details': {
				'image':...,
				'context_area': {
					'year':...,
					'description':...,
				},
				'content_area': {
					'scope':...
				}
			}
		},
		...
	]
